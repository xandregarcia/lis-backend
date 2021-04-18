<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use App\Customs\Messages;
use App\Models\ForReferral;

use App\Http\Resources\ForReferral\ForReferralResource;
use App\Http\Resources\ForReferral\ForReferralListResourceCollection;

class ForReferralController extends Controller
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
        $for_referrals = ForReferral::paginate(10);

        $data = new ForReferralListResourceCollection($for_referrals);

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
            'id' => 'string',
            'subject' => 'string',
            'receiving_date' => 'date',
            'category_id' => 'integer',
            'origin_id' => 'integer',
            'agenda_date' => 'date',
            'committees' => 'array',
            'file' => 'string'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
        
        $for_referral = new ForReferral;
		$for_referral->fill($data);
        $for_referral->save();

        // Sync in pivot table
        $committees = $data['committees'];
        $syncs = [];
        foreach ($committees as $committee) {
            $syncs[$committee['id']] = [
                "lead_committee" => $committee['lead_committee'],
                "joint_committee" => $committee['joint_committee'],
            ];
        }

        $for_referral->committees()->sync($syncs);

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Communication succesfully added");
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

        $for_referral = ForReferral::find($id);

        if (is_null($for_referral)) {
			return $this->jsonErrorResourceNotFound();
        }

		$data = new ForReferralResource($for_referral);

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
            'id' => 'string',
            'subject' => 'string',
            'receiving_date' => 'date',
            'category_id' => 'integer',
            'origin_id' => 'integer',
            'agenda_date' => 'date',
            'committees' => 'array',
            'file' => 'string'
        ];

        $for_referral = ForReferral::find($id);

        if (is_null($for_referral)) {
			return $this->jsonErrorResourceNotFound();
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
		$for_referral->fill($data);
        $for_referral->save();

        // Sync in pivot table
        $committees = $data['committees'];
        $syncs = [];
        foreach ($committees as $committee) {
            $syncs[$committee['id']] = [
                "lead_committee" => $committee['lead_committee'],
                "joint_committee" => $committee['joint_committee'],
            ];
        }

        $for_referral->committees()->sync($syncs);

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Communication info succesfully updated");        
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

        $for_referral = ForReferral::find($id);

        if (is_null($for_referral)) {
			return $this->jsonErrorResourceNotFound();
        }  

        $for_referral->delete();

        return $this->jsonDeleteSuccessResponse();         
    }
}
