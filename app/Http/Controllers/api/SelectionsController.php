<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Customs\Messages;
use App\Models\User;
use App\Models\Group;
use App\Models\Bokal;
use App\Models\Agency;
use App\Models\Origin;
use App\Models\Publisher;
use App\Models\Category;
use App\Models\Committee;
use App\Models\Ordinance;
use App\Models\CommunicationStatus;

class SelectionsController extends Controller
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

    public function users()
    {
        $all = User::all();

        $users = $all->map(function($user) {
            $row = [
                "id" => $user->id,
                "name" => "{$user->firstname} {$user->lastname}",
            ];
            return $row;
        });

        return $this->jsonSuccessResponse($users, $this->http_code_ok);        
    }

    public function groups()
    {
        $groups = Group::all(['id','name']);
        return $this->jsonSuccessResponse($groups, $this->http_code_ok); 
    }

    public function categories()
    {
        $categories = Category::all(['id','name']);
        return $this->jsonSuccessResponse($categories, $this->http_code_ok); 
    }

    public function committees()
    {
        $committees = Committee::all(['id','name']);
        return $this->jsonSuccessResponse($committees, $this->http_code_ok); 
    }

    public function origins()
    {
        $origins = Origin::all(['id','name']);
        return $this->jsonSuccessResponse($origins, $this->http_code_ok); 
    }

    public function publishers()
    {
        $publishers = Publisher::all(['id','name']);
        return $this->jsonSuccessResponse($publishers, $this->http_code_ok); 
    }

    public function agencies()
    {
        $agencies = Agency::all(['id','name']);
        return $this->jsonSuccessResponse($agencies, $this->http_code_ok); 
    }

    public function allBokals()
    {
        $all = Bokal::all();

        $bokals = $all->map(function($bokal) {
            $row = [
                "id" => $bokal->id,
                "name" => "Hon. {$bokal->first_name} {$bokal->middle_name} {$bokal->last_name}",
            ];
            return $row;
        });

        return $this->jsonSuccessResponse($bokals, $this->http_code_ok); 
    }

    public function activeBokals()
    {
        $all = Bokal::where('active',1)->get();

        $bokals = $all->map(function($bokal) {
            $row = [
                "id" => $bokal->id,
                "name" => "Hon. {$bokal->first_name} {$bokal->middle_name} {$bokal->last_name}",
            ];
            return $row;
        });

        return $this->jsonSuccessResponse($bokals, $this->http_code_ok); 
    }

    public function endorsements()
    {
        $wheres = [];
        $wheres[] = ['endorsement',1];
        $wheres[] = ['committee_report',0];
        $wheres[] = ['passed',0];

        $endorsements = CommunicationStatus::where($wheres)->with('for_referrals')->get();
        $endorsements = $endorsements->map(function ($endorsement) {
            return [
                'id' => $endorsement['for_referral_id'],
                'subject' => $endorsement['for_referrals']['subject'],
            ];
        });
        
        return $this->jsonSuccessResponse($endorsements, $this->http_code_ok); 
    }

    public function committeeReports()
    {
        $wheres = [];

        $wheres[] = ['committee_report',1];
        $wheres[] = ['second_reading',0];
        $wheres[] = ['passed',0];

        $reports = CommunicationStatus::where($wheres)->with('for_referrals')->get();
        $reports = $reports->map(function ($report) {
            return [
                'id' => $report['for_referral_id'],
                'subject' => $report['for_referrals']['subject'],
            ];
        });
        
        return $this->jsonSuccessResponse($reports, $this->http_code_ok); 
    }

    public function adoptReports()
    {
        $wheres = [];

        $wheres[] = ['committee_report',1];
        $wheres[] = ['adopt',0];

        $reports = CommunicationStatus::where($wheres)->where(function($query) {
            $query->where('second_reading',1)->orWhere('passed',1);
        })->with('for_referrals')->get();
        $reports = $reports->map(function ($report) {
            return [
                'id' => $report['for_referral_id'],
                'subject' => $report['for_referrals']['subject'],
            ];
        });
        
        return $this->jsonSuccessResponse($reports, $this->http_code_ok); 
    }

    public function resolutions()
    {

        $wheres = [];

        $wheres[] = ['passed',1];
        $wheres[] = ['approved',0];
        $wheres[] = ['type',3];

        $resolutions = CommunicationStatus::where($wheres)->with('for_referrals')->get();
        $resolutions = $resolutions->map(function ($resolution) {
            return [
                'id' => $resolution['for_referral_id'],
                'subject' => $resolution['for_referrals']['subject'],
            ];
        });

        return $this->jsonSuccessResponse($resolutions, $this->http_code_ok); 
    }

    public function ordinances()
    {
        $ordinances = Ordinance::all(['id','title']);
        return $this->jsonSuccessResponse($ordinances, $this->http_code_ok); 
    }

    //all referrals
    public function allEndorsements()
    {
        $wheres = [];
        $wheres[] = ['endorsement',1];

        $endorsements = CommunicationStatus::where($wheres)->with('for_referrals')->get();
        $endorsements = $endorsements->map(function ($endorsement) {
            return [
                'id' => $endorsement['for_referral_id'],
                'subject' => $endorsement['for_referrals']['subject'],
            ];
        });
        
        return $this->jsonSuccessResponse($endorsements, $this->http_code_ok); 
    }

    public function allCommitteeReports()
    {
        $wheres = [];

        $wheres[] = ['committee_report',1];

        $reports = CommunicationStatus::where($wheres)->with('for_referrals')->get();
        $reports = $reports->map(function ($report) {
            return [
                'id' => $report['for_referral_id'],
                'subject' => $report['for_referrals']['subject'],
            ];
        });
        
        return $this->jsonSuccessResponse($reports, $this->http_code_ok); 
    }

    public function allAdoptReports()
    {
        $wheres = [];

        $wheres[] = ['committee_report',1];

        $reports = CommunicationStatus::where($wheres)->where(function($query) {
            $query->where('second_reading',1)->orWhere('passed',1);
        })->get();
        $reports = $reports->map(function ($report) {
            return [
                'id' => $report['for_referral_id'],
                'subject' => $report['for_referrals']['subject'],
            ];
        });
        
        return $this->jsonSuccessResponse($reports, $this->http_code_ok); 
    }

    public function allResolutions()
    {

        $wheres = [];

        $wheres[] = ['committee_report',1];

        $resolutions = CommunicationStatus::where($wheres)->with('for_referrals')->where(function($query) {
            $query->where('second_reading',1)->orWhere('passed',1);
        })->get();
        $resolutions = $resolutions->map(function ($resolution) {
            return [
                'id' => $resolution['for_referral_id'],
                'subject' => $resolution['for_referrals']['subject'],
            ];
        });

        return $this->jsonSuccessResponse($resolutions, $this->http_code_ok); 
    }

}
