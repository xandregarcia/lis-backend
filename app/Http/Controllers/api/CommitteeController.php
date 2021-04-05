<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use App\Customs\Messages;
use App\Models\Committee;

use App\Http\Resources\Committee\CommitteeResource;
use App\Http\Resources\Committee\CommitteeListResourceCollection;

class CommitteeController extends Controller
{

    use Messages;

    private $http_code_ok;
    private $http_code_error;    

	public function __construct()
	{
		$this->middleware(['auth:api']);
		// $this->authorizeResource(Committee::class, Committee::class);
		
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
        $committees = Committee::paginate(10);

        $data = new CommitteeListResourceCollection($committees);

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
            'name' => 'string',
            'bokals' => 'array'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }        

        $data = $validator->valid();
        
        $committee = new Committee;
		$committee->fill($data);
        $committee->save();

        // Sync in pivot table
        $bokals = $data['bokals'];
        $syncs = [];
        foreach ($bokals as $bokal) {
            $syncs[$bokal['id']] = [
                "chairman" => $bokal['chairman'],
                "vice_chairman" => $bokal['vice_chairman'],
                "member" => $bokal['member'],
            ];
        }

        $committee->bokals()->sync($syncs);

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Committee succesfully added");
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

        $committee = Committee::find($id);

        if (is_null($committee)) {
			return $this->jsonErrorResourceNotFound();
        }

		$data = new CommitteeResource($committee);

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
            'bokals' => 'array'
        ];

        $committee = Committee::find($id);

        if (is_null($committee)) {
			return $this->jsonErrorResourceNotFound();
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
        $committee->fill($data);
        $committee->save();

        // Sync in pivot table
        $bokals = $data['bokals'];
        $syncs = [];
        foreach ($bokals as $bokal) {
            $syncs[$bokal['id']] = [
                "chairman" => $bokal['chairman'],
                "vice_chairman" => $bokal['vice_chairman'],
                "member" => $bokal['member'],
            ];
        }

        $committee->bokals()->sync($syncs);

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Committee info succesfully updated");        
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

        $committee = Committee::find($id);

        if (is_null($committee)) {
			return $this->jsonErrorResourceNotFound();
        }  

        $committee->delete();
    }
}
