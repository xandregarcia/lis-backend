<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

use App\Customs\Messages;
use App\Models\Resolution;
use App\Models\CommunicationStatus;
use App\Models\CommitteeReport;

use App\Http\Resources\Resolution\ResolutionResource;
use App\Http\Resources\Resolution\ResolutionListResourceCollection;

class ResolutionController extends Controller
{

    use Messages;

    private $http_code_ok;
    private $http_code_error;    

	public function __construct()
	{
		$this->middleware(['auth:api']);
		// $this->authorizeResource(Group::class, Group::class);
		
        $this->http_code_ok = 200;
        $this->http_code_error = 500;

	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $filters = $request->all();
        $resolution_no = (is_null($filters['resolution_no']))?null:$filters['resolution_no'];
        $subject = (is_null($filters['subject']))?null:$filters['subject'];
        $bokal_id = (is_null($filters['bokal_id']))?null:$filters['bokal_id'];
        $date_passed = (is_null($filters['date_passed']))?null:$filters['date_passed'];
        $category_id = (is_null($filters['category_id']))?null:$filters['category_id'];
        $origin_id = (is_null($filters['origin_id']))?null:$filters['origin_id'];
        $lead_committee_id = (is_null($filters['lead_committee_id']))?null:$filters['lead_committee_id'];
		$joint_committee_id = (is_null($filters['joint_committee_id']))?null:$filters['joint_committee_id'];

        $wheres = [];

        if ($resolution_no!=null) {
            $wheres[] = ['resolution_no', $resolution_no];
        }

        if ($subject!=null) {
            $wheres[] = ['subject', 'LIKE', "%{$subject}%"];
        }

        if ($bokal_id!=null) {
            $wheres[] = ['bokal_id', $bokal_id];
        }
        if ($date_passed!=null) {
            $wheres[] = ['date_passed', $date_passed];
        }

        $wheres[] = ['archive', 0];

        $resolutions = Resolution::where($wheres);

        if ($category_id!=null) {
			$resolutions->whereHas('for_referral', function(Builder $query) use ($category_id) {
				$query->where([['for_referrals.origin_id', $category_id]]);
			});
		}

        if ($origin_id!=null) {
			$resolutions->whereHas('for_referral', function(Builder $query) use ($origin_id) {
				$query->where([['for_referrals.origin_id', $origin_id]]);
			});
		}

        if ($lead_committee_id!=null) {
			$resolutions->whereHas('for_referral.committees', function(Builder $query) use ($lead_committee_id) {
				$query->where([['committee_for_referral.committee_id', $lead_committee_id],['committee_for_referral.lead_committee',true]]);
			});
		}
		if ($joint_committee_id!=null) {
			$resolutions->whereHas('for_referral.committees', function(Builder $query) use ($joint_committee_id) {
				$query->where([['committee_for_referral.committee_id', $joint_committee_id],['committee_for_referral.joint_committee',true]]);
			});
		}

        $resolutions = $resolutions->orderBy('resolution_no','desc')->paginate(10);

        $data = new ResolutionListResourceCollection($resolutions);

        return $this->jsonSuccessResponse($data, $this->http_code_ok);      
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'resolution_no' => ['string', 'unique:resolutions'],
            'subject' => 'string ',
            'bokal_id' => 'integer ',
            'date_passed' => 'date',
            'for_referral_id' => 'array',
            'pdf' => 'required|mimes:pdf|max:10000000',
            'committee_report_id' => 'integer',
        ];

        $customMessages = [
            'resolution_no.unique' => 'Resolution Number is already taken'
        ];
        
        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation($validator->errors());
        }

        $data = $validator->valid();
        try{
            DB::beginTransaction();
            $resolution = new Resolution;
            $resolution->fill($data);
            $resolution->save();

            /**
             * Upload Attachment
             */
            if (isset($data['pdf'])) {
                $folder = config('folders.resolutions');
                $path = "{$folder}/{$resolution->id}";
                // $filename = Str::random(20).".".$request->file('pdf')->getClientOriginalExtension();
                $filename = $request->file('pdf')->getClientOriginalName();
                $request->file('pdf')->storeAs("public/{$path}", $filename);
                $pdf = "{$path}/{$filename}";
                $resolution->file = $pdf;
                $resolution->save();
            }

            $syncs = [];

            if(isset($data['committee_report_id'])){
                $id = $data['committee_report_id'];
                $communications = CommitteeReport::find($id)->for_referral;
                foreach ($communications as $communication) {
                    $syncs[] = $communication['id'];
                    $status = CommunicationStatus::where('for_referral_id',$communication['id'])->get();
                    $status->toQuery()->update([
                        'adopt' => true,
                    ]);
				}
            }else if(isset($data['for_referral_id'])){
                $for_referrals = $data['for_referral_id'];            
                foreach ($for_referrals as $for_referral) {
                    $syncs[] = $for_referral;
                    $status = CommunicationStatus::where('for_referral_id',$for_referral)->get();
                    $status->toQuery()->update([
                        'approved' => true,
                    ]);
                }
            }

            $resolution->for_referral()->sync($syncs);

            DB::commit();
            
            return $this->jsonSuccessResponse(null, $this->http_code_ok, "Resolution succesfully added");

        } catch (\Exception $e) {

            DB::rollBack();

            return $this->jsonFailedResponse(null, $this->http_code_error, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $resolution = Resolution::find($id);

        if (is_null($resolution)) {
			return $this->jsonErrorResourceNotFound();
        }

		$data = new ResolutionResource($resolution);

        return $this->jsonSuccessResponse($data, $this->http_code_ok);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }        
        $resolution = Resolution::find($id);

        if (is_null($resolution)) {
			return $this->jsonErrorResourceNotFound();
        }

        $rules = [
            'resolution_no' => ['string', Rule::unique('resolutions')->ignore($resolution),],
            'subject' => 'string ',
            'for_referral_id' => 'array',
            'bokal_id' => 'integer ',
            'date_passed' => 'date',
            'for_referral_id' => 'array',
            'pdf' => 'mimes:pdf|max:10000000'
        ];

        $customMessages = [
            'resolution_no.unique' => 'Resolution Number is already taken'
        ];

        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation($validator->errors());
        }

        $data = $validator->valid();
        try{
            DB::beginTransaction();
            $resolution->fill($data);
            $resolution->save();

            /**
             * Upload Attachment
             */
            if (isset($data['pdf'])) {
                $folder = config('folders.resolutions');
                $path = "{$folder}/{$resolution->id}";
                // $filename = Str::random(20).".".$request->file('pdf')->getClientOriginalExtension();
                $filename = $request->file('pdf')->getClientOriginalName();
                $request->file('pdf')->storeAs("public/{$path}", $filename);
                $pdf = "{$path}/{$filename}";
                $resolution->file = $pdf;
                $resolution->save();
            }

            $syncs = [];

            if(isset($data['for_referral_id'])){
                $for_referrals = $data['for_referral_id'];            
                foreach ($for_referrals as $for_referral) {
                    $syncs[] = $for_referral;
                    $status = CommunicationStatus::where('for_referral_id',$for_referral)->get();
                    $status->toQuery()->update([
                        'approved' => true,
                    ]);
                }
            }
            
            $resolution->for_referral()->sync($syncs);
            DB::commit();
            
            return $this->jsonSuccessResponse(null, $this->http_code_ok, "Resolution succesfully updated");

        } catch (\Exception $e) {

            DB::rollBack();

            return $this->jsonFailedResponse(null, $this->http_code_error, $e->getMessage());
        }    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $resolution = Resolution::find($id);

        if (is_null($resolution)) {
			return $this->jsonErrorResourceNotFound();
        }  

        $resolution->delete();

        return $this->jsonDeleteSuccessResponse();         
    }
}
