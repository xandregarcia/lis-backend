<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use App\Customs\Messages;
use App\Models\Ordinance;
use App\Models\CommunicationStatus;

use App\Http\Resources\Ordinance\OrdinanceResource;
use App\Http\Resources\Ordinance\OrdinanceListResourceCollection;


class OrdinanceController extends Controller
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
        $ordinances = Ordinance::paginate(10);

        $data = new OrdinanceListResourceCollection($ordinances);

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
            'amending' => 'integer',
            'date_passed' => 'date',
            'date_signed' => 'date',
            'pdf' => 'required|mimes:pdf|max:10000000'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
        
        $oridnance = new Ordinance;
		$oridnance->fill($data);
        $oridnance->save();

        /**
         * Upload Attachment
         */
        if (isset($data['pdf'])) {
            $folder = config('folders.oridnances');
            $path = "{$folder}/{$oridnance->id}";
            $filename = $request->file('pdf')->getClientOriginalName();
            $request->file('pdf')->storeAs("public/{$path}", $filename);
            $pdf = "{$path}/{$filename}";
            $oridnance->file = $pdf;
            $oridnance->save();
        }

        // $status = CommunicationStatus::where('for_referral_id',$resolution->for_referral_id)->get();
        // $type = $status->first()->type;
        // $status->toQuery()->update([
        //     'passed' => true,
        // ]);
        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Ordinance succesfully added");
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

        $ordinance = Ordinance::find($id);

        if (is_null($ordinance)) {
			return $this->jsonErrorResourceNotFound();
        }

		$data = new OrdinanceResource($ordinance);

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
            'amending' => 'integer',
            'date_passed' => 'date',
            'date_signed' => 'date',
            'pdf' => 'required|mimes:pdf|max:10000000'
        ];

        $ordinance = Ordinance::find($id);

        if (is_null($ordinance)) {
			return $this->jsonErrorResourceNotFound();
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation();
        }

        $data = $validator->valid();
        $ordinance->fill($data);
        $ordinance->save();

         /**
         * Upload Attachment
         */
        if (isset($data['pdf'])) {
            $folder = config('folders.ordinances');
            $path = "{$folder}/{$ordinance->id}";
            // $filename = Str::random(20).".".$request->file('pdf')->getClientOriginalExtension();
            $filename = $request->file('pdf')->getClientOriginalName();
            $request->file('pdf')->storeAs("public/{$path}", $filename);
            $pdf = "{$path}/{$filename}";
            $ordinance->file = $pdf;
            $ordinance->save();
        }

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Group info succesfully updated");        
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

        $ordinance = Ordinance::find($id);

        if (is_null($ordinance)) {
			return $this->jsonErrorResourceNotFound();
        }  

        $ordinance->delete();

        return $this->jsonDeleteSuccessResponse();         
    }
}
