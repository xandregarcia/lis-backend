<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use App\Customs\Messages;
use App\Models\SecondReading;

use App\Http\Resources\SecondReading\SecondReadingResource;
use App\Http\Resources\SecondReading\SecondReadingListResourceCollection;

class SecondReadingController extends Controller
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
        $second_readings = SecondReading::paginate(10);

        $data = new SecondReadingListResourceCollection($second_readings);

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
            'date_received' => 'date',
            'agenda_date' => 'date',
            'file' => 'string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
        
        $second_reading = new SecondReading;
		$second_reading->fill($data);
        $second_reading->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Group succesfully added");
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

        $second_reading = SecondReading::find($id);

        if (is_null($second_reading)) {
			return $this->jsonErrorResourceNotFound();
        }

		$data = new SecondReadingResource($second_reading);

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
            'date_received' => 'date',
            'agenda_date' => 'date',
            'file' => 'string',
        ];

        $second_reading = SecondReading::find($id);

        if (is_null($second_reading)) {
			return $this->jsonErrorResourceNotFound();
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
        $second_reading->fill($data);
        $second_reading->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Group info succesfully updated");        
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

        $second_reading = SecondReading::find($id);

        if (is_null($second_reading)) {
			return $this->jsonErrorResourceNotFound();
        }  

        $second_reading->delete();
    }
}
