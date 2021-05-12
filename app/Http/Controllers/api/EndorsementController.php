<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use App\Customs\Messages;
use App\Models\Endorsement;
use App\Models\CommunicationStatus;

use App\Http\Resources\Endorsement\EndorsementResource;
use App\Http\Resources\Endorsement\EndorsementListResourceCollection;

class EndorsementController extends Controller
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

        // $filters = $request->all();
        // $date_endorsed = (is_null($filters['date_endorsed']))?null:$filters['date_endorsed'];

        $wheres = [];

        // if ($date_endorsed!=null) {
        //     $wheres[] = ['date_endorsed', 'LIKE', "%{$date_endorsed}%"];
        // }

        $endorsements = Endorsement::where($wheres)->paginate(10);

        $data = new EndorsementListResourceCollection($endorsements);

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
            'for_referral_id' => 'integer',
            'date_endorsed' => 'date',
            'pdf' => 'required|mimes:pdf|max:10000000'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();

        $endorsement = new Endorsement;
        $endorsement->fill($data);
        $endorsement->save();

        /**
         * Upload Attachment
         */
        if (isset($data['pdf'])) {
            $folder = config('folders.endorsements');
            $path = "{$folder}/{$endorsement->id}";
            // $filename = Str::random(20).".".$request->file('pdf')->getClientOriginalExtension();
            $filename = $request->file('pdf')->getClientOriginalName();
            $request->file('pdf')->storeAs("public/{$path}", $filename);
            $pdf = "{$path}/{$filename}";
            $endorsement->file = $pdf;
            $endorsement->save();
        }

        $status = CommunicationStatus::where('for_referral_id',$endorsement->for_referral_id)->get();
        $status->toQuery()->update([
            'committee_report' => true,
        ]);
        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Endorsement succesfully added");
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

        $endorsement = Endorsement::find($id);

        if (is_null($endorsement)) {
			return $this->jsonErrorResourceNotFound();
        }

		$data = new EndorsementResource($endorsement);

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
        // return $request;

        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $rules = [
            'for_referral_id' => 'integer',
            'date_endorsed' => 'date'
        ];
        
        $endorsement = Endorsement::find($id);

        if (is_null($endorsement)) {
			return $this->jsonErrorResourceNotFound();
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // return $validator->errors();
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
		$endorsement->fill($data);
        $endorsement->save();

        /**
         * Upload Attachment
         */
        if (isset($data['pdf'])) {
            $folder = config('folders.endorsements');
            $path = "{$folder}/{$endorsement->id}";
            // $filename = Str::random(20).".".$request->file('pdf')->getClientOriginalExtension();
            $filename = $request->file('pdf')->getClientOriginalName();
            $request->file('pdf')->storeAs("public/{$path}", $filename);
            $pdf = "{$path}/{$filename}";
            $endorsement->file = $pdf;
            $endorsement->save();
        }

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Endorsement succesfully updated");         
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

        $endorsement = Endorsement::find($id);

        if (is_null($endorsement)) {
			return $this->jsonErrorResourceNotFound();
        }  

        $endorsement->delete();

        return $this->jsonDeleteSuccessResponse();         
    }
}
