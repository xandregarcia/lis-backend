<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use App\Customs\Messages;
use App\Models\Appropriation;
use App\Models\CommunicationStatus;

use App\Http\Resources\Appropriation\AppropriationResource;
use App\Http\Resources\Appropriation\AppropriationListResourceCollection;


class AppropriationController extends Controller
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
        $title = (is_null($filters['title']))?null:$filters['title'];
        $date_passed = (is_null($filters['date_passed']))?null:$filters['date_passed'];

        $wheres = [];

        if ($title!=null) {
            $wheres[] = ['title', 'LIKE', "%{$title}%"];
        }

        if ($title!=null) {
            $wheres[] = ['date_passed',$date_passed];
        }

        $appropriations = Appropriation::where($wheres)->orderBy('id','desc')->paginate(10);

        $data = new AppropriationListResourceCollection($appropriations);

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
            'title' => 'string',
            'date_passed' => 'date',
            'pdf' => 'required|mimes:pdf|max:10000000'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
        
        $appropriation = new Appropriation;
		$appropriation->fill($data);
        $appropriation->save();

        /**
         * Upload Attachment
         */
        if (isset($data['pdf'])) {
            $folder = config('folders.appropriations');
            $path = "{$folder}/{$appropriation->id}";
            $filename = $request->file('pdf')->getClientOriginalName();
            $request->file('pdf')->storeAs("public/{$path}", $filename);
            $pdf = "{$path}/{$filename}";
            $appropriation->file = $pdf;
            $appropriation->save();
        }

        $status = CommunicationStatus::where('for_referral_id',$appropriation->for_referral_id)->get();
        $status->toQuery()->update([
            'approved' => true,
        ]);
        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Appropriation Ordinance succesfully added");
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

        $appropriation = Appropriation::find($id);

        if (is_null($appropriation)) {
			return $this->jsonErrorResourceNotFound();
        }

		$data = new AppropriationResource($appropriation);

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
            'title' => 'string',
            'date_passed' => 'date'
        ];

        $appropriation = Appropriation::find($id);

        if (is_null($appropriation)) {
			return $this->jsonErrorResourceNotFound();
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
        $appropriation->fill($data);
        $appropriation->save();

         /**
         * Upload Attachment
         */
        if (isset($data['pdf'])) {
            $folder = config('folders.appropriations');
            $path = "{$folder}/{$appropriation->id}";
            // $filename = Str::random(20).".".$request->file('pdf')->getClientOriginalExtension();
            $filename = $request->file('pdf')->getClientOriginalName();
            $request->file('pdf')->storeAs("public/{$path}", $filename);
            $pdf = "{$path}/{$filename}";
            $appropriation->file = $pdf;
            $appropriation->save();
        }

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Appropriation Ordinance succesfully updated");        
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

        $appropriation = Appropriation::find($id);

        if (is_null($appropriation)) {
			return $this->jsonErrorResourceNotFound();
        }  

        $appropriation->delete();

        return $this->jsonDeleteSuccessResponse();         
    }
}
