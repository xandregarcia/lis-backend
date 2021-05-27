<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;

use App\Customs\Messages;
use App\Models\CommunicationStatus;

use App\Http\Resources\CommunicationStatus\CommunicationStatusResource;
use App\Http\Resources\CommunicationStatus\CommunicationStatusListResourceCollection;

class CommunicationStatusController extends Controller
{
    use Messages;

    private $http_code_ok;
    private $http_code_error;

	public function __construct()
	{
		$this->middleware(['auth:api']);
		
        $this->http_code_ok = 200;
        $this->http_code_error = 500;

	}

    public function approveRef(Request $request)
    {

        $filters = $request->all();
        $for_referral_id = (is_null($filters['for_referral_id']))?null:$filters['for_referral_id'];
        $subject = (is_null($filters['subject']))?null:$filters['subject'];
        $agenda_date = (is_null($filters['agenda_date']))?null:$filters['agenda_date'];

        $wheres = [];

        if ($for_referral_id!=null) {
			$wheres[] = ['for_referral_id', $for_referral_id];
		}

        $wheres[] = ['passed',0];
        $wheres[] = ['endorsement',0];

        $comm_status = CommunicationStatus::where($wheres);

        if ($subject!=null) {
			$comm_status->whereHas('for_referrals', function(Builder $query) use ($subject) {
				$query->where([['subject','LIKE', "%{$subject}%"]]);
			});
		}

        if ($agenda_date!=null) {
			$comm_status->whereHas('for_referrals', function(Builder $query) use ($agenda_date) {
				$query->where([['agenda_date','LIKE', "%{$agenda_date}%"]]);
			});
		}

        $comm_status= $comm_status->orderBy('id')->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    public function endorsements(Request $request)
    {

        $filters = $request->all();
        // $for_referral_id = (is_null($filters['for_referral_id']))?null:$filters['for_referral_id'];
        $subject = (is_null($filters['subject']))?null:$filters['subject'];
        $agenda_date = (is_null($filters['agenda_date']))?null:$filters['agenda_date'];

        $wheres = [];

        // if ($for_referral_id!=null) {
		// 	$wheres[] = ['for_referral_id', $for_referral_id];
		// }

        $wheres[] = ['endorsement',1];
        $wheres[] = ['committee_report',0];
        $wheres[] = ['passed',0];

        $comm_status = CommunicationStatus::where($wheres);

        if ($subject!=null) {
			$comm_status->whereHas('for_referrals', function(Builder $query) use ($subject) {
				$query->where([['subject','LIKE', "%{$subject}%"]]);
			});
		}

        if ($agenda_date!=null) {
			$comm_status->whereHas('for_referrals', function(Builder $query) use ($agenda_date) {
				$query->where([['agenda_date','LIKE', "%{$agenda_date}%"]]);
			});
		}

        $comm_status = $comm_status->orderBy('id')->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    public function committeeReports(Request $request)
    {
        $filters = $request->all();
        $for_referral_id = (is_null($filters['for_referral_id']))?null:$filters['for_referral_id'];
        $subject = (is_null($filters['subject']))?null:$filters['subject'];
        $agenda_date = (is_null($filters['agenda_date']))?null:$filters['agenda_date'];

        $wheres = [];

        if ($for_referral_id!=null) {
			$wheres[] = ['for_referral_id', $for_referral_id];
		}

        $wheres[] = ['committee_report',1];
        $wheres[] = ['second_reading',0];
        $wheres[] = ['passed',0];

        $comm_status = CommunicationStatus::where($wheres);

        if ($subject!=null) {
			$comm_status->whereHas('for_referrals', function(Builder $query) use ($subject) {
				$query->where([['subject','LIKE', "%{$subject}%"]]);
			});
		}

        if ($agenda_date!=null) {
			$comm_status->whereHas('for_referrals', function(Builder $query) use ($agenda_date) {
				$query->where([['agenda_date','LIKE', "%{$agenda_date}%"]]);
			});
		}

        $comm_status = $comm_status->orderBy('id','desc')->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    public function secondReadings(Request $request)
    {

        $filters = $request->all();
        $for_referral_id = (is_null($filters['for_referral_id']))?null:$filters['for_referral_id'];
        $subject = (is_null($filters['subject']))?null:$filters['subject'];
        $agenda_date = (is_null($filters['agenda_date']))?null:$filters['agenda_date'];

        $wheres = [];

        if ($for_referral_id!=null) {
			$wheres[] = ['for_referral_id', $for_referral_id];
		}

        $wheres[] = ['second_reading',1];
        $wheres[] = ['third_reading',0];
        $wheres[] = ['passed',0];
        $wheres[] = ['type','<',3];

        $comm_status = CommunicationStatus::where($wheres);

        if ($subject!=null) {
			$comm_status->whereHas('for_referrals', function(Builder $query) use ($subject) {
				$query->where([['subject','LIKE', "%{$subject}%"]]);
			});
		}

        if ($agenda_date!=null) {
			$comm_status->whereHas('for_referrals', function(Builder $query) use ($agenda_date) {
				$query->where([['agenda_date','LIKE', "%{$agenda_date}%"]]);
			});
		}

        $comm_status = $comm_status->orderBy('id','desc')->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    public function thirdReadings(Request $request)
    {

        $filters = $request->all();
        $for_referral_id = (is_null($filters['for_referral_id']))?null:$filters['for_referral_id'];
        $subject = (is_null($filters['subject']))?null:$filters['subject'];
        $agenda_date = (is_null($filters['agenda_date']))?null:$filters['agenda_date'];

        $wheres = [];

        if ($for_referral_id!=null) {
			$wheres[] = ['for_referral_id', $for_referral_id];
		}

        $wheres[] = ['third_reading',1];
        $wheres[] = ['passed',0];
        $wheres[] = ['type','<',3];

        $comm_status = CommunicationStatus::where($wheres);

        if ($subject!=null) {
			$comm_status->whereHas('for_referrals', function(Builder $query) use ($subject) {
				$query->where([['subject','LIKE', "%{$subject}%"]]);
			});
		}

        if ($agenda_date!=null) {
			$comm_status->whereHas('for_referrals', function(Builder $query) use ($agenda_date) {
				$query->where([['agenda_date','LIKE', "%{$agenda_date}%"]]);
			});
		}
    
        $comm_status = $comm_status->orderBy('id','desc')->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    public function resolutions(Request $request)
    {

        $filters = $request->all();
        $for_referral_id = (is_null($filters['for_referral_id']))?null:$filters['for_referral_id'];
        $subject = (is_null($filters['subject']))?null:$filters['subject'];
        $agenda_date = (is_null($filters['agenda_date']))?null:$filters['agenda_date'];

        $wheres = [];

        if ($for_referral_id!=null) {
			$wheres[] = ['for_referral_id', $for_referral_id];
		}

        $wheres[] = ['passed',1];
        $wheres[] = ['approved',0];
        $wheres[] = ['type',3];

        $comm_status = CommunicationStatus::where($wheres);

        if ($subject!=null) {
			$comm_status->whereHas('for_referrals', function(Builder $query) use ($subject) {
				$query->where([['subject','LIKE', "%{$subject}%"]]);
			});
		}

        if ($agenda_date!=null) {
			$comm_status->whereHas('for_referrals', function(Builder $query) use ($agenda_date) {
				$query->where([['agenda_date','LIKE', "%{$agenda_date}%"]]);
			});
		}
    
        $comm_status = $comm_status->orderBy('id','desc')->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    public function ordinances(Request $request)
    {
        $filters = $request->all();
        $for_referral_id = (is_null($filters['for_referral_id']))?null:$filters['for_referral_id'];
        $subject = (is_null($filters['subject']))?null:$filters['subject'];
        $agenda_date = (is_null($filters['agenda_date']))?null:$filters['agenda_date'];

        $wheres = [];

        if ($for_referral_id!=null) {
			$wheres[] = ['for_referral_id', $for_referral_id];
		}

        $wheres[] = ['passed',1];
        $wheres[] = ['approved',0];
        $wheres[] = ['type',1];

        $comm_status = CommunicationStatus::where($wheres);

        if ($subject!=null) {
			$comm_status->whereHas('for_referrals', function(Builder $query) use ($subject) {
				$query->where([['subject','LIKE', "%{$subject}%"]]);
			});
		}

        if ($agenda_date!=null) {
			$comm_status->whereHas('for_referrals', function(Builder $query) use ($agenda_date) {
				$query->where([['agenda_date','LIKE', "%{$agenda_date}%"]]);
			});
		}
    
        $comm_status = $comm_status->orderBy('id','desc')->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    public function appropriations(Request $request)
    {
        $filters = $request->all();
        $for_referral_id = (is_null($filters['for_referral_id']))?null:$filters['for_referral_id'];
        $subject = (is_null($filters['subject']))?null:$filters['subject'];
        $agenda_date = (is_null($filters['agenda_date']))?null:$filters['agenda_date'];

        $wheres = [];

        if ($for_referral_id!=null) {
			$wheres[] = ['for_referral_id', $for_referral_id];
		}

        $wheres[] = ['passed',1];
        $wheres[] = ['approved',0];
        $wheres[] = ['type',2];

        $comm_status = CommunicationStatus::where($wheres);

        if ($subject!=null) {
			$comm_status->whereHas('for_referrals', function(Builder $query) use ($subject) {
				$query->where([['subject','LIKE', "%{$subject}%"]]);
			});
		}

        if ($agenda_date!=null) {
			$comm_status->whereHas('for_referrals', function(Builder $query) use ($agenda_date) {
				$query->where([['agenda_date','LIKE', "%{$agenda_date}%"]]);
			});
		}
    
        $comm_status = $comm_status->orderBy('id','desc')->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    public function publish(Request $request)
    {
    
        $comm_status = CommunicationStatus::where('approved',1)->where('type',1)->where('pubished',0)->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    public function furnishResolution(Request $request)
    {
    
        $comm_status = CommunicationStatus::where('approved',1)->where('type',3)->where('furnished',0)->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    public function furnishOrdinance(Request $request)
    {
    
        $comm_status = CommunicationStatus::where('approved',1)->where('type',1)->where('furnished',0)->paginate(10);
        $data = new CommunicationStatusListResourceCollection($comm_status);
        
        return $this->jsonSuccessResponse($data, $this->http_code_ok); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, $id)
    {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }
        
        $comm_status = CommunicationStatus::find($id);

        if (is_null($comm_status)) {
			return $this->jsonErrorResourceNotFound();
        }
        
        $comm_status->fill([
            'passed' => true,
        ]);

        $comm_status->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Successfully approved");        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function refer(Request $request, $id)
    {
        if (filter_var($id, FILTER_VALIDATE_INT) === false ) {
            return $this->jsonErrorInvalidParameters();
        }
        
        $comm_status = CommunicationStatus::find($id);

        if (is_null($comm_status)) {
			return $this->jsonErrorResourceNotFound();
        }
        
        $comm_status->fill([
            'endorsement' => true,
        ]);

        $comm_status->save();

        return $this->jsonSuccessResponse(null, $this->http_code_ok, "Successfully referred");        
    }
}