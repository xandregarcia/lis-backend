<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use App\Customs\Messages;
use App\Models\Publisher;

use App\Http\Resources\Publisher\PublisherResource;
use App\Http\Resources\Publisher\PublisherListResourceCollection;

class PublisherController extends Controller
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

        $wheres = [];

        if($name!=null) {
            $wheres[] = ['name', 'LIKE', "%{$name}%"];
        }

        $publishers = Publisher::where($wheres)->paginate(10);

        $data = new PublisherListResourceCollection($publishers);

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
            'name' => ['string', 'string', 'unique:publishers'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
        
        $publisher = new Publisher;
		$publisher->fill($data);
        $publisher->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Publisher succesfully added");
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

        $publisher = Publisher::find($id);

        if (is_null($publisher)) {
			return $this->jsonErrorResourceNotFound();
        }

		$data = new PublisherResource($publisher);

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
            'name' => 'string',
        ];

        $publisher = Publisher::find($id);

        if (is_null($publisher)) {
			return $this->jsonErrorResourceNotFound();
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
        $publisher->fill($data);
        $publisher->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Publisher succesfully updated");        
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

        $publisher = Publisher::find($id);

        if (is_null($publisher)) {
			return $this->jsonErrorResourceNotFound();
        }  

        $publisher->delete();

        return $this->jsonDeleteSuccessResponse();         
    }
}
