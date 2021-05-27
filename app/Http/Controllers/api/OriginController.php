<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Customs\Messages;
use App\Models\Origin;

use App\Http\Resources\Origin\OriginResource;
use App\Http\Resources\Origin\OriginListResourceCollection;

class OriginController extends Controller
{

    use Messages;

    private $http_code_ok;
    private $http_code_error;    

	public function __construct()
	{
		$this->middleware(['auth:api']);
		// $this->authorizeResource(Origin::class, Origin::class);
		
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

        $wheres = [];

        if($name!=null) {
            $wheres[] = ['name', 'LIKE', "%{$name}%"];
        }

        $origins = Origin::where($wheres)->latest()->paginate(10);

        $data = new OriginListResourceCollection($origins);

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
            'name' => ['string', 'string', 'unique:origins'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
        
        $origin = new Origin;
		$origin->fill($data);
        $origin->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Origin succesfully added");
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

        $origin = Origin::find($id);

        if (is_null($origin)) {
			return $this->jsonErrorResourceNotFound();
        }

		$data = new OriginResource($origin);

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
        
        $origin = Origin::find($id);

        if (is_null($origin)) {
			return $this->jsonErrorResourceNotFound();
        }

        $rules = [
            'name' => ['string', Rule::unique('origins')->ignore($origin),],
        ];
 
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
        $origin->fill($data);
        $origin->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Origin succesfully updated");  
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

        $origin = Origin::find($id);

        if (is_null($origin)) {
			return $this->jsonErrorResourceNotFound();
        }  

        $origin->delete();

        return $this->jsonDeleteSuccessResponse();         
    }
}
