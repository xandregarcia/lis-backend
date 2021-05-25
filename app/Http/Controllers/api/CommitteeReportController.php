<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

use App\Customs\Messages;
use App\Models\CommitteeReport;
use App\Models\CommunicationStatus;

use App\Http\Resources\CommitteeReport\CommitteeReportResource;
use App\Http\Resources\CommitteeReport\CommitteeReportListResourceCollection;

class CommitteeReportController extends Controller
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
        $date_received = (is_null($filters['date_received']))?null:$filters['date_received'];
        $agenda_date = (is_null($filters['agenda_date']))?null:$filters['agenda_date'];
        $meeting_date = (is_null($filters['meeting_date']))?null:$filters['meeting_date'];
        $lead_committee_id = (is_null($filters['lead_committee_id']))?null:$filters['lead_committee_id'];
		$joint_committee_id = (is_null($filters['joint_committee_id']))?null:$filters['joint_committee_id'];

        $wheres = [];

        if ($date_received!=null) {
            $wheres[] = ['date_received', $date_received];
        }

        if ($agenda_date!=null) {
            $wheres[] = ['agenda_date', $agenda_date];
        }

        if ($meeting_date!=null) {
            $wheres[] = ['meeting_date', $meeting_date];
        }

        $committeeReports = CommitteeReport::where($wheres);

        if ($lead_committee_id!=null) {
			$committeeReports->whereHas('committees', function(Builder $query) use ($lead_committee_id) {
				$query->where([['committee_for_referral.committee_id', $lead_committee_id],['committee_for_referral.lead_committee',true]]);
			});
		}
		if ($joint_committee_id!=null) {
			$committeeReports->whereHas('committees', function(Builder $query) use ($joint_committee_id) {
				$query->where([['committee_for_referral.committee_id', $joint_committee_id],['committee_for_referral.joint_committee',true]]);
			});
		}

        $committeeReports = $committeeReports->paginate(10);
        $data = new CommitteeReportListResourceCollection($committeeReports);

        return $this->jsonSuccessResponse($data, $this->http_code_ok);      
    }

    public function adoptReports(Request $request)
    {

        $filters = $request->all();
        $for_referral_id = (is_null($filters['for_referral_id']))?null:$filters['for_referral_id'];
        $subject = (is_null($filters['subject']))?null:$filters['subject'];
        $agenda_date = (is_null($filters['agenda_date']))?null:$filters['agenda_date'];

        $wheres = [];

        if ($for_referral_id!=null) {
			$wheres[] = ['for_referral_id', $for_referral_id];
		}

        $comm_status = CommitteeReport::where($wheres);
        $comm_status = $comm_status->whereHas('for_referral.comm_status', function(Builder $query){
            $query->where([['committee_report',1],['adopt',0]])->where(function ($query2) {
                $query2->where('second_reading',1)->orWhere('passed',1);
            });
        });

        // if ($subject!=null) {
		// 	$comm_status->whereHas('for_referrals', function(Builder $query) use ($subject) {
		// 		$query->where([['subject','LIKE', "%{$subject}%"]]);
		// 	});
		// }

        // if ($agenda_date!=null) {
		// 	$comm_status->whereHas('for_referrals', function(Builder $query) use ($agenda_date) {
		// 		$query->where([['agenda_date','LIKE', "%{$agenda_date}%"]]);
		// 	});
		// }
        $comm_status = $comm_status->paginate(10);
        $data = new CommitteeReportListResourceCollection($comm_status);
        
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
            'for_referral_id' => 'array',
            'date_received' => 'date ',
            'agenda_date' => 'date',
            'remarks' => 'string',
            'meeting_date' => 'date',
            'pdf' => 'required|mimes:pdf|max:10000000'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // return $validator->errors();
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();

        try{

            DB::beginTransaction();

            $committeeReport = new CommitteeReport;
            $committeeReport->fill($data);
            $committeeReport->save();

            /**
             * Upload Attachment
             */
            if (isset($data['pdf'])) {
                $folder = config('folders.committee_reports');
                $path = "{$folder}/{$committeeReport->id}";
                // $filename = Str::random(20).".".$request->file('pdf')->getClientOriginalExtension();
                $filename = $request->file('pdf')->getClientOriginalName();
                $request->file('pdf')->storeAs("public/{$path}", $filename);
                $pdf = "{$path}/{$filename}";
                $committeeReport->file = $pdf;
                $committeeReport->save();
            }

            $sync = [];

            $for_referrals = $data['for_referral_id'];
            foreach ($for_referrals as $for_referral) {
                $status = CommunicationStatus::where('for_referral_id',$for_referral)->get();
                $type = $status->first()->type;
                if($type == 3) {
                    $status->toQuery()->update([
                        'passed' => true,
                    ]);
                }else {
                    $status->toQuery()->update([
                        'second_reading' => true,
                    ]);
                }
            }
            
            $committeeReport->for_referral()->sync($for_referrals);

            DB::commit();

            return $this->jsonSuccessResponse(null, $this->http_code_ok, "Committee Report succesfully added");

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

        $committeeReport = CommitteeReport::find($id);

        if (is_null($committeeReport)) {
			return $this->jsonErrorResourceNotFound();
        }

		$data = new CommitteeReportResource($committeeReport);

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

        $rules = [
            'for_referral_id' => 'array',
            'date_received' => 'date ',
            'agenda_date' => 'date',
            'remarks' => 'string',
            'meeting_date' => 'date',
            'pdf' => 'required|mimes:pdf|max:10000000'
        ];

        $committeeReport = CommitteeReport::find($id);

        if (is_null($committeeReport)) {
			return $this->jsonErrorResourceNotFound();
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
        try{

            DB::beginTransaction();
            $committeeReport->fill($data);
            $committeeReport->save();

            /**
             * Upload Attachment
             */
            if (isset($data['pdf'])) {
                $folder = config('folders.committee_reports');
                $path = "{$folder}/{$committeeReport->id}";
                // $filename = Str::random(20).".".$request->file('pdf')->getClientOriginalExtension();
                $filename = $request->file('pdf')->getClientOriginalName();
                $request->file('pdf')->storeAs("public/{$path}", $filename);
                $pdf = "{$path}/{$filename}";
                $committeeReport->file = $pdf;
                $committeeReport->save();
            }

            $sync = [];

            $for_referrals = $data['for_referral_id'];
            
            $committeeReport->for_referral()->sync($for_referrals);

            DB::commit();

            return $this->jsonSuccessResponse(null, $this->http_code_ok, "Committee Report succesfully updated");  

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

        $committeeReport = CommitteeReport::find($id);

        if (is_null($committeeReport)) {
			return $this->jsonErrorResourceNotFound();
        }  

        $committeeReport->delete();

        return $this->jsonDeleteSuccessResponse();         
    }
}
