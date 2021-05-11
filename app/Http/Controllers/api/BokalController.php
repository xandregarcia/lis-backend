<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use App\Customs\Messages;
use App\Models\Bokal;

use App\Http\Resources\Bokal\BokalResource;
use App\Http\Resources\Bokal\BokalListResourceCollection;

class BokalController extends Controller
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
        $name = (is_null($filters['name']))?null:$filters['name'];
        $active = (is_null($filters['active']))?null:$filters['active'];

        $wheres = [];

        if ($name!=null) {
            $wheres[] = ['name', 'LIKE', "%{$name}%"];
        }

        if ($active!=null) {
            $wheres[] = ['active', $active];
        }

        $bokals = Bokal::where($wheres)->paginate(10);

        $data = new BokalListResourceCollection($bokals);

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
            'first_name' => 'string',
            'middle_name' => 'string',
            'last_name' => 'string',
            'active' => 'boolean',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
            // return $validator->errors();
        }

        $data = $validator->valid();
        
        $bokal = new Bokal;
		$bokal->fill($data);
        $bokal->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Bokal Member succesfully added");
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

        $bokal = Bokal::find($id);

        if (is_null($bokal)) {
			return $this->jsonErrorResourceNotFound();
        }

		$data = new BokalResource($bokal);

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
            'first_name' => 'string',
            'middle_name' => 'string',
            'last_name' => 'string',
            'active' => 'boolean',
        ];

        $bokal = Bokal::find($id);

        if (is_null($bokal)) {
			return $this->jsonErrorResourceNotFound();
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
        $bokal->fill($data);
        $bokal->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Bokal Member succesfully updated");        
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

        $bokal = Bokal::find($id);

        if (is_null($bokal)) {
			return $this->jsonErrorResourceNotFound();
        }  

        $bokal->delete();

        return $this->jsonDeleteSuccessResponse();         
    }
}
