<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

use App\Customs\Messages;
use App\Models\ForReferral;
use App\Models\Ordinance;

use App\Http\Resources\Report\ReportResource;
use App\Http\Resources\Report\ReportListResourceCollection;
use App\Http\Resources\Ordinance\OrdinanceListResourceCollection;

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

    public function iso6(Request $request)
    {
        $filters = $request->all();
        $subject = (is_null($filters['subject']))?null:$filters['subject'];
        $agenda_date = (is_null($filters['agenda_date']))?null:$filters['agenda_date'];
        
        $wheres = [];

        if ($subject!=null) {
			$wheres[] = ['subject', 'LIKE',  "%{$subject}%"];
		}

        if ($agenda_date!=null) {
			$wheres[] = ['agenda_date', 'LIKE',  "%{$agenda_date}%"];
		}

        $iso6 = ForReferral::where($wheres)->where(function (Builder $query) {
            return $query->where('category_id', 1);
        });

        $iso6 = $iso6->orderBy('agenda_date','desc')->paginate(10);

        $data = new ReportListResourceCollection($iso6);


        return $this->jsonSuccessResponse($data, $this->http_code_ok);        
    }

    public function iso7(Request $request)
    {
        $filters = $request->all();
        $subject = (is_null($filters['subject']))?null:$filters['subject'];
        $agenda_date = (is_null($filters['agenda_date']))?null:$filters['agenda_date'];
        
        $wheres = [];

        if ($subject!=null) {
			$wheres[] = ['subject', 'LIKE',  "%{$subject}%"];
		}

        if ($agenda_date!=null) {
			$wheres[] = ['agenda_date', 'LIKE',  "%{$agenda_date}%"];
		}

        $iso7 = ForReferral::where($wheres)->where(function (Builder $query) {
            return $query->where('category_id', 3);
        });

        $iso7 = $iso7->orderBy('agenda_date','desc')->paginate(10);

        $data = new ReportListResourceCollection($iso7);

        return $this->jsonSuccessResponse($data, $this->http_code_ok);        
    }

    public function iso11(Request $request)
    {
        $filters = $request->all();
        $title = (is_null($filters['title']))?null:$filters['title'];
        $date_passed = (is_null($filters['date_passed']))?null:$filters['date_passed'];
        
        $wheres = [];

        if ($title!=null) {
			$wheres[] = ['title', 'LIKE',  "%{$title}%"];
		}

        if ($date_passed!=null) {
			$wheres[] = ['date_passed', 'LIKE', "%{$date_passed}%"];
		}

        $iso11 = Ordinance::where($wheres)->orderBy('ordinance_no','desc')->paginate(10);

        $data = new OrdinanceListResourceCollection($iso11);


        return $this->jsonSuccessResponse($data, $this->http_code_ok);        
    }

    public function iso17(Request $request)
    {
        $filters = $request->all();
        $title = (is_null($filters['title']))?null:$filters['title'];
        $date_passed = (is_null($filters['date_passed']))?null:$filters['date_passed'];
        
        $wheres = [];

        if ($title!=null) {
			$wheres[] = ['title', 'LIKE',  "%{$title}%"];
		}

        if ($date_passed!=null) {
			$wheres[] = ['date_passed', 'LIKE', "%{$date_passed}%"];
		}

        $iso17 = Ordinance::where($wheres)->orderBy('ordinance_no','desc')->paginate(10);

        $data = new OrdinanceListResourceCollection($iso17);


        return $this->jsonSuccessResponse($data, $this->http_code_ok);        
    }

}
