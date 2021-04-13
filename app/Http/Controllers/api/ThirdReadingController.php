<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

<<<<<<< Updated upstream
class ThirdReadingController extends Controller
{
=======
use Illuminate\Support\Facades\Validator;

use App\Customs\Messages;
use App\Models\ThirdReading;

use App\Http\Resources\ThirdReading\ThirdReadingResource;
use App\Http\Resources\ThirdReading\ThirdReadingListResourceCollection;

class ThirdReadingController extends Controller
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

>>>>>>> Stashed changes
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
<<<<<<< Updated upstream
        //
=======
        $third_readings = ThirdReading::paginate(10);

        $data = new ThirdReadingListResourceCollection($third_readings);

        return $this->jsonSuccessResponse($data, $this->http_code_ok);      
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
        //
=======
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
        
        $third_reading = new ThirdReading;
		$third_reading->fill($data);
        $third_reading->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Group succesfully added");
>>>>>>> Stashed changes
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
<<<<<<< Updated upstream
        //
=======
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $third_reading = ThirdReading::find($id);

        if (is_null($third_reading)) {
			return $this->jsonErrorResourceNotFound();
        }

		$data = new ThirdReadingResource($third_reading);

        return $this->jsonSuccessResponse($data, $this->http_code_ok);
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
        //
=======
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }        

        $rules = [
            'for_referral_id' => 'integer',
            'date_received' => 'date',
            'agenda_date' => 'date',
            'file' => 'string',
        ];

        $third_reading = ThirdReading::find($id);

        if (is_null($third_reading)) {
			return $this->jsonErrorResourceNotFound();
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
        $third_reading->fill($data);
        $third_reading->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Group info succesfully updated");        
>>>>>>> Stashed changes
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
<<<<<<< Updated upstream
        //
=======
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }

        $third_reading = ThirdReading::find($id);

        if (is_null($third_reading)) {
			return $this->jsonErrorResourceNotFound();
        }  

        $third_reading->delete();
>>>>>>> Stashed changes
    }
}
