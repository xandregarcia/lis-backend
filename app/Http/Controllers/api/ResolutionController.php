<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use App\Customs\Messages;
use App\Models\Resolution;
use App\Models\CommunicationStatus;

use App\Http\Resources\Resolution\ResolutionResource;
use App\Http\Resources\Resolution\ResolutionListResourceCollection;

class ResolutionController extends Controller
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
        $bokal_id = (is_null($filters['bokal_id']))?null:$filters['bokal_id'];
        $date_passed = (is_null($filters['date_passed']))?null:$filters['date_passed'];

        $wheres = [];

        if ($bokal_id!=null) {
            $wheres[] = ['bokal_id', $bokal_id];
        }

        if ($date_passed!=null) {
            $wheres[] = ['date_passed', $date_passed];
        }

        $resolutions = Resolution::where($wheres)->paginate(10);

        $data = new ResolutionListResourceCollection($resolutions);

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
            'bokal_id' => 'integer ',
            'date_passed' => 'date',
            'pdf' => 'required|mimes:pdf|max:10000000'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
        
        $resolution = new Resolution;
		$resolution->fill($data);
        $resolution->save();

        /**
         * Upload Attachment
         */
        if (isset($data['pdf'])) {
            $folder = config('folders.resolutions');
            $path = "{$folder}/{$resolution->id}";
            // $filename = Str::random(20).".".$request->file('pdf')->getClientOriginalExtension();
            $filename = $request->file('pdf')->getClientOriginalName();
            $request->file('pdf')->storeAs("public/{$path}", $filename);
            $pdf = "{$path}/{$filename}";
            $resolution->file = $pdf;
            $resolution->save();
        }

        $status = CommunicationStatus::where('for_referral_id',$resolution->for_referral_id)->get();
        $type = $status->first()->type;
        $status->toQuery()->update([
            'approved' => true,
        ]);

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Resolution succesfully added");
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

        $resolution = Resolution::find($id);

        if (is_null($resolution)) {
			return $this->jsonErrorResourceNotFound();
        }

		$data = new ResolutionResource($resolution);

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
            'bokal_id' => 'integer ',
            'date_passed' => 'date',
        ];

        $resolution = Resolution::find($id);

        if (is_null($resolution)) {
			return $this->jsonErrorResourceNotFound();
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
        $resolution->fill($data);
        $resolution->save();

         /**
         * Upload Attachment
         */
        if (isset($data['pdf'])) {
            $folder = config('folders.resolutions');
            $path = "{$folder}/{$resolution->id}";
            // $filename = Str::random(20).".".$request->file('pdf')->getClientOriginalExtension();
            $filename = $request->file('pdf')->getClientOriginalName();
            $request->file('pdf')->storeAs("public/{$path}", $filename);
            $pdf = "{$path}/{$filename}";
            $resolution->file = $pdf;
            $resolution->save();
        }

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Resolution succesfully updated");        
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

        $resolution = Resolution::find($id);

        if (is_null($resolution)) {
			return $this->jsonErrorResourceNotFound();
        }  

        $resolution->delete();

        return $this->jsonDeleteSuccessResponse();         
    }
}
