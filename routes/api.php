<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\LoginController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\GroupController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group(function() {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('logout', [LoginController::class, 'logout']);
});

/**
 * Users
 */
Route::apiResources([
    'users' => UserController::class,
],[
    'only' => ['index']
]);

Route::apiResources([
    'user' => UserController::class,
],[
    'except' => ['index']
]);

/**
 * Groups
 */
Route::apiResources([
    'groups' => GroupController::class,
],[
    'only' => ['index']
]);

Route::apiResources([
    'group' => GroupController::class,
],[
    'except' => ['index']
]);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/test/{id}', function () {
        return "Hello, World!";
    });     
});