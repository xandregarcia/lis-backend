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
                'for_referral_id' => $endorsement['for_referral_id'],
                'subject' => $endorsement['for_referrals']['subject'],
            ];
        });
        
        return $this->jsonSuccessResponse($endorsements, $this->http_code_ok); 
    }

    public function ordinances()
    {
        $ordinances = Ordinance::all(['id','title']);
        return $this->jsonSuccessResponse($ordinances, $this->http_code_ok); 
    }

}
