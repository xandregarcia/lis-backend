<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Customs\Messages;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\User\UserResource;

class LoginApiController extends Controller
{

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->jsonErrorDataValidation($validator->errors());
        }

        $login = $validator->valid();

        if (!Auth::attempt($login)) {
            return $this->jsonErrorInvalidCredentials();
        }

        $access_token = Auth::user()->createToken('authToken')->accessToken;
        $user = User::find(Auth::id());

        $data = new UserResource($user);

        return $this->jsonSuccessResponse($data, 200);

    }

    public function logout()
    {
        $revoked = Auth::guard('api')->user()->token()->revoke();
        if ($revoked) {
            return $this->jsonSuccessLogout();
        }
        return $this->jsonFailedResponse(null, $this->http_code_error, 'Something went wrong.');
    }    

}
