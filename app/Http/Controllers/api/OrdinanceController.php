<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

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
    public function index(Request $request)
    {
        $filters = $request->all();
        $id = (is_null($filters['id']))?null:$filters['id'];
        $ordinance_no = (is_null($filters['ordinance_no']))?null:$filters['ordinance_no'];
        $title = (is_null($filters['title']))?null:$filters['title'];
        $amending = (is_null($filters['amending']))?null:$filters['amending'];
        $date_passed = (is_null($filters['date_passed']))?null:$filters['date_passed'];
        $date_signed = (is_null($filters['date_signed']))?null:$filters['date_signed'];
        $author = (is_null($filters['author']))?null:$filters['author'];

        $wheres = [];

        if($id!=null) {
            $wheres[] = ['id', 'LIKE', "%{$id}%"];
        }

        if($ordinance_no!=null) {
            $wheres[] = ['ordinance_no', 'LIKE', "%{$ordinance_no}%"];
        }


        if($title!=null) {
            $wheres[] = ['title', 'LIKE', "%{$title}%"];
        }

        if($amending!=null) {
            $wheres[] = ['amending', 'LIKE', "%{$amending}%"];
        }

        if($date_passed!=null) {
            $wheres[] = ['date_passed', $date_passed];
        }

        if($date_signed!=null) {
            $wheres[] = ['date_signed', $date_signed];
        }

        $wheres[] = ['archive', 0];

        $ordinances = Ordinance::where($wheres);

        if ($author!=null) {
			$ordinances->whereHas('bokals', function(Builder $query) use ($author) {
				$query->where([['bokal_ordinance.bokal_id', $author],['bokal_ordinance.author',true]]);
			});
		}

        $ordinances = $ordinances->orderBy('ordinance_no','desc')->paginate(10);

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
            'for_referral_id' => ['integer', 'unique:appropriations'],
            'ordinance_no' => ['string', 'unique:ordinances'],
            'title' => 'string',
            'amending' => 'integer',
            'date_passed' => 'date',
            'date_signed' => 'date',
            'authors' => 'array',
            'co_authors' => 'array',
            'pdf' => 'required|mimes:pdf|max:10000000'
        ];

        $customMessages = [
            'ordinance_no.unique' => 'Ordinance Number is already taken',
            'for_referral_id.unique' => 'Ordinance is already existing'
        ];

        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation($validator->errors());
        }

        $data = $validator->valid();

        try{
            DB::beginTransaction();

            $ordinance = new Ordinance;
            $ordinance->fill($data);
            $ordinance->save();
    
            /**
             * Upload Attachment
             */
            if (isset($data['pdf'])) {
                $folder = config('folders.ordinances');
                $path = "{$folder}/{$ordinance->id}";
                $filename = $request->file('pdf')->getClientOriginalName();
                $request->file('pdf')->storeAs("public/{$path}", $filename);
                $pdf = "{$path}/{$filename}";
                $ordinance->file = $pdf;
                $ordinance->save();
            }
            if(isset($data['for_referral_id'])){
                $for_referral = $data['for_referral_id']; 
                $status = CommunicationStatus::where('for_referral_id',$for_referral)->get();
                $status->toQuery()->update([
                    'approved' => true,
                ]);
            }

            // Sync in pivot table
            $authors = $data['authors'];
            $syncs = [];

            //authors
            foreach ($authors as $author) {
                $syncs[$author['id']] = [
                    'author' => true,
                    'co_author' =>false,
                ];
            }

            //co-authors
            if(isset($data['co_authors'])) {
                $co_authors = $data['co_authors'];
                foreach ($co_authors as $co_author) {
                    $syncs[$co_author['id']] = [
                        'author' => false,
                        'co_author' =>true,
                    ];
                }
            }
            

            $ordinance->bokals()->sync($syncs);

            DB::commit();

            return $this->jsonSuccessResponse(null, $this->http_code_ok, "Ordinance succesfully added");

        }catch (\Exception $e) {

            DB::rollBack();

            return $e;

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

        $ordinance = Ordinance::find($id);

        if (is_null($ordinance)) {
			return $this->jsonErrorResourceNotFound();
        }

        $rules = [
            'for_referral_id' => 'integer',
            'ordinance_no' => ['string', Rule::unique('ordinances')->ignore($ordinance),],
            'title' => 'string',
            'amending' => 'integer',
            'date_passed' => 'date',
            'date_signed' => 'date',
            'authors' => 'array',
            'co_authors' => 'array',
        ];

        $customMessages = [
            'ordinance_no.unique' => 'Ordinance Number is already taken'
        ];
        
        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation($validator->errors());
        }

        $data = $validator->valid();

        try{

            DB::beginTransaction();
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
            
            if(isset($data['for_referral_id'])){
                $for_referral = $data['for_referral_id']; 
                $status = CommunicationStatus::where('for_referral_id',$for_referral)->get();
                $status->toQuery()->update([
                    'approved' => true,
                ]);
            }

            // Sync in pivot table
            $authors = $data['authors'];
            $syncs = [];

            //authors
            foreach ($authors as $author) {
                $syncs[$author['id']] = [
                    'author' => true,
                    'co_author' =>false,
                ];
            }

            //co-authors
            if(isset($data['co_authors'])) {
                $co_authors = $data['co_authors'];
                foreach ($co_authors as $co_author) {
                    $syncs[$co_author['id']] = [
                        'author' => false,
                        'co_author' =>true,
                    ];
                }
            }

            $ordinance->bokals()->sync($syncs);

            DB::commit();

            return $this->jsonSuccessResponse(null, $this->http_code_ok, "Ordinance succesfully updated");

        }catch (\Exception $e) {
            
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

        $ordinance = Ordinance::find($id);

        if (is_null($ordinance)) {
			return $this->jsonErrorResourceNotFound();
        }  

        $ordinance->delete();

        return $this->jsonDeleteSuccessResponse();         
    }
}