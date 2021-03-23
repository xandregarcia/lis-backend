<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\LoginController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\GroupController;
use App\Http\Controllers\api\AgencyController;
use App\Http\Controllers\api\PublisherController;
use App\Http\Controllers\api\BokalController;


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

/**
 * Agencies
 */
Route::apiResources([
    'agencies' => AgencyController::class,
],[
    'only' => ['index']
]);

Route::apiResources([
    'agency' => AgencyController::class,
],[
    'except' => ['index']
]);

/**
 * Publishers
 */
Route::apiResources([
    'publishers' => PublisherController::class,
],[
    'only' => ['index']
]);

Route::apiResources([
    'publisher' => PublisherController::class,
],[
    'except' => ['index']
]);

/**
 * Bokals
 */
Route::apiResources([
    'bokals' => BokalController::class,
],[
    'only' => ['index']
]);

Route::apiResources([
    'bokal' => BokalController::class,
],[
    'except' => ['index']
]);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/test/{id}', function () {
        return "Hello, World!";
    });     
});