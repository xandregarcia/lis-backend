<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


use App\Customs\Messages;
use App\Models\ForReferral;
use App\Models\CommunicationStatus;

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
    public function index(Request $request)
    {
        $filters = $request->all();
        $subject = null;
        // $subject = (is_null($filters['subject']))?null:$filters['subject'];
        // $receiving_date = (is_null($filters['receiving_date']))?null:$filters['receiving_date'];
        // $category_id = $filters['category_id'];
        // $origin_id = $filters['origin_id'];
        // $agenda_date = $filters['agenda_date'];
        // $lead_committee = $filters['lead_committee'];

        $wheres = [];
        if ($subject!=null) {
            $wheres[] = ['subject', 'LIKE', "{$subject}%"];
        }
        // if ($receiving_date!=null) {
        //     $wheres[] = ['receiving_date', $receiving_date];
        // }

        $for_referrals = ForReferral::where($wheres)->paginate(10);

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
            'lead_committee' => 'integer',
            'joint_committee' => 'array',
            'file' => 'string'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();

        try{

            DB::beginTransaction();
        
            $for_referral = new ForReferral;
            $for_referral->fill($data);
            $for_referral->save();

            $status = new CommunicationStatus;
            $status->fill([
                "endorsement" => false,
                "committee_report" => false,
                "second_reading" => false,
                "third_reading" => false
            ]);

            $for_referral->comm_status()->save($status);

            // Sync in pivot table
            $committees = $data['joint_committee'];
            $syncs = [];
            //lead committee
            $syncs[$data['lead_committee']] = [
                "lead_committee" => true,
                "joint_committee" => false,
            ];

            foreach ($committees as $committee) {
                $syncs[$committee['id']] = [
                    "lead_committee" => false,
                    "joint_committee" =>true,
                ];
            }
            $for_referral->committees()->sync($syncs);

            DB::commit();

            return $this->jsonSuccessResponse(null, $this->http_code_ok, "Communication succesfully added");

        }catch (\Exception $e) {

            DB::rollBack();

            return $this->jsonFailedResponse(null, $this->http_code_error, $e->getMessage());

        }
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
            'lead_committee' => 'integer',
            'joint_committee' => 'array',
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
        $committees = $data['joint_committee'];
        $syncs = [];
        //lead committee
        $syncs[$data['lead_committee']] = [
            "lead_committee" => true,
            "joint_committee" => false,
        ];

        foreach ($committees as $committee) {
            $syncs[$committee['id']] = [
                "lead_committee" => false,
                "joint_committee" =>true,
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
