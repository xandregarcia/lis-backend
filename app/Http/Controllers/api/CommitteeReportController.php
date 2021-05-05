<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
    public function index()
    {
        $committeeReports = CommitteeReport::paginate(10);

        $data = new CommitteeReportListResourceCollection($committeeReports);

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
            'date_received' => 'date ',
            'agenda_date' => 'date',
            'remarks' => 'string',
            'meeting_date' => 'date',
            'pdf' => 'required|mimes:pdf|max:10000000'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
        
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
        $status = CommunicationStatus::where('for_referral_id',$committeeReport->for_referral_id)->get();
        
        $type = $status->first()->type;

        if($type == 1) {
            $status->toQuery()->update([
                'passed' => true,
            ]);
        }else {
            $status->toQuery()->update([
                'second_reading' => true,
            ]);
        }
        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Committee Report succesfully added");
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
            'for_referral_id' => 'integer',
            'date_received' => 'date ',
            'agenda_date' => 'date',
            'remarks' => 'string',
            'meeting_date' => 'date'
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

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Committee Report succesfully updated");        
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
