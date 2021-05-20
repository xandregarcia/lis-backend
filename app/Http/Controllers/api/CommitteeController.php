<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
    public function index(Request $request)
    {

        $filters = $request->all();
        $name = (is_null($filters['name']))?null:$filters['name'];
        $chairman_id = (isset($filters['chairman_id']))?(is_null($filters['chairman_id']))?null:$filters['chairman_id']:null;
        $vice_chairman_id = (isset($filters['vice_chairman_id']))?(is_null($filters['vice_chairman_id']))?null:$filters['vice_chairman_id']:null;
        $member_id = (isset($filters['member_id']))?(is_null($filters['member_id']))?null:$filters['member_id']:null;

        $wheres = [];

        if ($name!=null) {
            $wheres[] = ['name', 'LIKE', "%{$name}%"];
        }

        $committees = Committee::where($wheres);
        if ($chairman_id!=null) {
			$committees->whereHas('groups', function(Builder $query) use ($chairman_id) {
				$query->where([['committee_group.group_id', $chairman_id],['committee_group.chairman',true]]);
			});
		}
        if ($vice_chairman_id!=null) {
			$committees->whereHas('groups', function(Builder $query) use ($vice_chairman_id) {
				$query->where([['committee_group.group_id', $vice_chairman_id],['committee_group.vice_chairman',true]]);
			});
		}
        if ($member_id!=null) {
			$committees->whereHas('groups', function(Builder $query) use ($member_id) {
				$query->where([['committee_group.group_id', $member_id],['committee_group.member',true]]);
			});
		}

        $committees = $committees->paginate(10);

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
            'name' => ['string', 'string', 'unique:committees'],
            'chairman' => 'integer',
            'vice_chairman' => 'integer',
            'members' => 'array'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }        

        $data = $validator->valid();
        
        try {

            DB::beginTransaction();

            $committee = new Committee;
            $committee->fill($data);
            $committee->save();

            $members = $data['members'];

            // Sync in pivot table
            $syncs = [];
            // Chairman
            $syncs[$data['chairman']] = [
                "chairman" => true,
                "vice_chairman" => false,
                "member" => false,
            ];
            // Vice Chairman
            $syncs[$data['vice_chairman']] = [
                "chairman" => false,
                "vice_chairman" => true,
                "member" => false,
            ];

            //members
            foreach ($members as $member) {
                $syncs[$member['id']] = [
                    "chairman" => false,
                    "vice_chairman" => false,
                    "member" => true,
                ];
            }

            $committee->groups()->sync($syncs);

            DB::commit();

            return $this->jsonSuccessResponse(null, $this->http_code_ok, "Committee succesfully added");            

        } catch (\Exception $e) {

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
            'groups' => 'array'
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

        try {
            DB::beginTransaction();
            $committee->fill($data);
            $committee->save();

            // Sync in pivot table
            $members = $data['members'];
            $syncs = [];

            // Chairman
            $syncs[$data['chairman']] = [
                "chairman" => true,
                "vice_chairman" => false,
                "member" => false,
            ];
            
            // Vice Chairman
            $syncs[$data['vice_chairman']] = [
                "chairman" => false,
                "vice_chairman" => true,
                "member" => false,
            ];

            //Member
            foreach ($members as $member) {
                $syncs[$member['id']] = [
                    "chairman" => false,
                    "vice_chairman" => false,
                    "member" => true,
                ];
            }

            $committee->groups()->sync($syncs);

            DB::commit();

            return $this->jsonSuccessResponse(null, $this->http_code_ok, "Committee succesfully updated");           

        } catch (\Exception $e) {

            DB::rollBack();

            return $this->jsonFailedResponse(null, $this->http_code_error, $e->getMessage());

        }
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

        return $this->jsonDeleteSuccessResponse();         
    }
}
