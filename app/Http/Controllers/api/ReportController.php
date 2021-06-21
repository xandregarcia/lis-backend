<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

use App\Customs\Messages;
use App\Models\ForReferral;

use App\Http\Resources\Report\ReportResource;
use App\Http\Resources\Report\ReportListResourceCollection;

class ReportController extends Controller
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

    public function ISO6(Request $request)
    {
        $filters = $request->all();
        $subject = (is_null($filters['subject']))?null:$filters['subject'];
        $agenda_date = (is_null($filters['agenda_date']))?null:$filters['agenda_date'];
        
        $wheres = [];

        if ($subject!=null) {
			$wheres[] = ['subject', $subject];
		}

        if ($agenda_date!=null) {
			$wheres[] = ['agenda_date', 'LIKE',  "%{$agenda_date}%"];
		}

        $iso6 = ForReferral::where($wheres)->where(function (Builder $query) {
            return $query->where('category', 1);
        });

        $iso6 = $iso6->paginate(10);

        $data = new ReportListResourceCollection($iso6);


        return $this->jsonSuccessResponse($iso6, $this->http_code_ok);        
    }

}
