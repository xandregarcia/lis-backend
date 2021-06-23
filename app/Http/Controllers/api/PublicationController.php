<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use App\Customs\Messages;
use App\Models\Publication;
use App\Models\Ordinance;
use App\Models\CommunicationStatus;

use App\Http\Resources\Publication\PublicationResource;
use App\Http\Resources\Publication\PublicationListResourceCollection;

class PublicationController extends Controller
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

    public function second_publication(Request $request)
    {
        $filters = $request->all();
        $ordinance_id = (is_null($filters['ordinance_id']))?null:$filters['ordinance_id'];
        $publisher_id = (is_null($filters['publisher_id']))?null:$filters['publisher_id'];
        $first_publication = (is_null($filters['first_publication']))?null:$filters['first_publication'];

        $wheres = [];

        if($ordinance_id!=null) {
            $wheres[] = ['ordinance_id',$ordinance_id];
        }

        if($publisher_id!=null) {
            $wheres[] = ['publisher_id',$publisher_id];
        }

        if($first_publication!=null) {
            $wheres[] = ['first_from',$first_publication];
        }

        $publications = Publication::whereNotNull('first_from')->whereNull('second_from')->where($wheres)->paginate(10);

        $data = new PublicationListResourceCollection($publications);

        return $this->jsonSuccessResponse($data, $this->http_code_ok);
    }

    public function third_publication(Request $request)
    {
        $filters = $request->all();
        $ordinance_id = (is_null($filters['ordinance_id']))?null:$filters['ordinance_id'];
        $publisher_id = (is_null($filters['publisher_id']))?null:$filters['publisher_id'];
        $first_publication = (is_null($filters['first_publication']))?null:$filters['first_publication'];
        $second_publication = (is_null($filters['second_publication']))?null:$filters['second_publication'];

        $wheres = [];

        if($ordinance_id!=null) {
            $wheres[] = ['ordinance_id',$ordinance_id];
        }

        if($publisher_id!=null) {
            $wheres[] = ['publisher_id',$publisher_id];
        }

        if($first_publication!=null) {
            $wheres[] = ['first_from',$first_publication];
        }

        if($second_publication!=null) {
            $wheres[] = ['second_from',$second_publication];
        }

        $publications = Publication::whereNotNull('second_from')->whereNull('third_from')->where($wheres)->paginate(10);

        $data = new PublicationListResourceCollection($publications);

        return $this->jsonSuccessResponse($data, $this->http_code_ok);
    }

    public function index(Request $request)
    {
        $filters = $request->all();
        $ordinance_id = (is_null($filters['ordinance_id']))?null:$filters['ordinance_id'];
        $publisher_id = (is_null($filters['publisher_id']))?null:$filters['publisher_id'];
        $first_publication = (is_null($filters['first_publication']))?null:$filters['first_publication'];
        $second_publication = (is_null($filters['second_publication']))?null:$filters['second_publication'];
        $third_publication = (is_null($filters['third_publication']))?null:$filters['third_publication'];

        $wheres = [];

        if($ordinance_id!=null) {
            $wheres[] = ['ordinance_id',$ordinance_id];
        }

        if($publisher_id!=null) {
            $wheres[] = ['publisher_id',$publisher_id];
        }

        if($first_publication!=null) {
            $wheres[] = ['first_from',$first_publication];
        }

        if($second_publication!=null) {
            $wheres[] = ['second_from',$second_publication];
        }

        if($third_publication!=null) {
            $wheres[] = ['third_from',$third_publication];
        }

        $publications = Publication::whereNotNull('third_from')->where($wheres)->paginate(10);

        $data = new PublicationListResourceCollection($publications);

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
            'ordinance_id' => 'integer',
            'publisher_id' => 'integer',
            'first_from' => 'date',
            'first_to' => 'date',
            'second_from' => 'date',
            'second_to' => 'date',
            'third_from' => 'date',
            'third_to' => 'date',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $validator->errors();
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
        
        $publication = new Publication;
		$publication->fill($data);
        $publication->save();
        $for_referral_id = Ordinance::find( $data['ordinance_id'])->for_referral_id;
        $status = CommunicationStatus::where('for_referral_id',$for_referral_id)->get();
        $status->toQuery()->update([
            'published' => true,
        ]);

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Succesfully published");
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

        $publication = Publication::find($id);

        if (is_null($publication)) {
			return $this->jsonErrorResourceNotFound();
        }

		$data = new PublicationResource($publication);

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
            'ordinance_id' => 'integer',
            'publisher_id' => 'integer',
            'first_from' => 'date',
            'first_to' => 'date',
            'second_from' => 'date',
            'second_to' => 'date',
            'third_from' => 'date',
            'third_to' => 'date',
        ];

        $publication = Publication::find($id);

        if (is_null($publication)) {
			return $this->jsonErrorResourceNotFound();
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation($validator->errors());
        }

        $data = $validator->valid();
        $publication->fill($data);
        $publication->save();
        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Published Ordinance successfully updated");        
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

        $publication = Publicationi::find($id);

        if (is_null($publication)) {
			return $this->jsonErrorResourceNotFound();
        }  

        $publication->delete();

        return $this->jsonDeleteSuccessResponse();         
    }
}
