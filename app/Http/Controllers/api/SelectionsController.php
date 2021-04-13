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
        $bokals = Bokal::all(['id','name']);
        return $this->jsonSuccessResponse($bokals, $this->http_code_ok); 
    }

    public function activeBokals()
    {
        $bokals = Bokal::where('active',1)->get(['id','name']);
        return $this->jsonSuccessResponse($bokals, $this->http_code_ok); 
    }

}
