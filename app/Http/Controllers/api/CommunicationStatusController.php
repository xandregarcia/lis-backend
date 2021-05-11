<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use App\Customs\Messages;
use App\Models\CommunicationStatus;

use App\Http\Resources\CommunicationStatus\CommunicationStatusResource;
use App\Http\Resources\CommunicationStatus\CommunicationStatusListResourceCollection;

class CommunicationStatusController extends Controller
{
    use Messages;

    private $http_code_ok;
    private $http_code_error;

	public function __construct()
	{
		$this->middleware(['auth:api']);
		
        $this->http_code_ok = 200;
        $this->http_code_error = 500;

	}

    public function approveRef(Request $request)
    {

        // $filters = $request->all();
        // $subject = (is_null($filters['subject']))?null:$filters['subject'];

        $wheres = [];

        // if ($subject!=null) {
        //     $wheres[] = ['subject', 'LIKE', "%{$subject}%"];
        // }

        $comm_status = CommunicationStatus::where('passed',0)->where('endorsement',0)->where($wheres)->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    public function endorsements(Request $request)
    {
        $comm_status = CommunicationStatus::where('passed',0)->where('endorsement',1)->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    public function committeeReports(Request $request)
    {
        $comm_status = CommunicationStatus::where('passed',0)->where('committee_report',1)->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    public function secondReadings(Request $request)
    {
        $comm_status = CommunicationStatus::where('passed',0)->where('second_reading',1)->where('type','>',1)->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    public function thirdReadings(Request $request)
    {
    
        $comm_status = CommunicationStatus::where('passed',0)->where('third_reading',1)->where('type','>',1)->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    public function resolutions(Request $request)
    {
    
        $comm_status = CommunicationStatus::where('passed',1)->where('approved',0)->where('type',3)->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    public function ordinances(Request $request)
    {
    
        $comm_status = CommunicationStatus::where('passed',1)->where('approved',0)->where('type',1)->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    public function appropriations(Request $request)
    {
    
        $comm_status = CommunicationStatus::where('passed',1)->where('approved',0)->where('type',2)->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    public function publish(Request $request)
    {
    
        $comm_status = CommunicationStatus::where('approved',1)->where('type',1)->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    public function furnishResolution(Request $request)
    {
    
        $comm_status = CommunicationStatus::where('approved',1)->where('type',3)->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    public function furnishOrdinance(Request $request)
    {
    
        $comm_status = CommunicationStatus::where('approved',1)->where('type',1)->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, $id)
    {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }
        
        $comm_status = CommunicationStatus::find($id);

        if (is_null($comm_status)) {
			return $this->jsonErrorResourceNotFound();
        }
        
        $comm_status->fill([
            'passed' => true,
        ]);

        $comm_status->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Successfully approved");        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function refer(Request $request, $id)
    {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }
        
        $comm_status = CommunicationStatus::find($id);

        if (is_null($comm_status)) {
			return $this->jsonErrorResourceNotFound();
        }
        
        $comm_status->fill([
            'endorsement' => true,
        ]);

        $comm_status->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Successfully referred");        
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

        $comm_status = CommunicationStatus::find($id);

        if (is_null($comm_status)) {
			return $this->jsonErrorResourceNotFound();
        }

		$data = new CommunicationStatusResource($comm_status);

        return $this->jsonSuccessResponse($data, $this->http_code_ok);
    }
}