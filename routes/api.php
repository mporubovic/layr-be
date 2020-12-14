<?php

// use App\Http\Controllers\Services\ServiceController;

use App\Http\Resources\User as UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// use App\Https\Resources\UserResource;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    // return $request->user();
    // return $request->user();
    // return gettype($request->user());
    // $user = $request->user();
    return new UserResource($request->user());
});

// Route::get('/apitest', 'BoardController@index');
// Route::get('/apitest', function () {
//     return request()->user()->boards;
// })->middleware('auth:sanctum');


// Route::middleware('auth:sanctum')->get('/boards', function (Request $request) {
//     return $request->user()->boards;
// });

// Route::middleware('auth:sanctum')->get('/boards', 'BoardController@index');

// Route::middleware('throttle:60,1')
Route::get('/auth/login/checkemail/{email}', 'Services\ServiceController@checkEmail');


// Route::get('/boards', 'BoardController@index'); // for public access to public boards
// Route::get('/boards/{boardId}', 'BoardController@show');

// Route::get('/subdomains/{subdomain}', 'SubdomainController@show');

Route::post('/invite/checktoken', 'InviteController@checkToken');
Route::post('/invite/accepttoken', 'InviteController@acceptToken');



Route::middleware('auth:sanctum')->group(function () {
    
    
    Route::get('/boards', 'BoardController@index');
    Route::get('/boards/{boardId}', 'BoardController@show');
    Route::post('/boards', 'BoardController@store');
    // Route::patch('/boards/{boardId}', 'BoardController@update');
    Route::delete('/boards/{boardId}', 'BoardController@destroy');


    Route::get('/stacks', 'StackController@index');
    Route::get('/stacks/{stackId}', 'StackController@show');
    Route::post('/stacks', 'StackController@store');
    Route::patch('/stacks/{stackId}', 'StackController@update');
    Route::delete('/stacks/{stackId}', 'StackController@destroy');

    Route::get('/cards', 'CardController@index');
    Route::post('/cards', 'CardController@store');
    Route::get('/cards/{cardId}', 'CardController@show');
    Route::patch('/cards/{cardId}', 'CardController@update');
    Route::delete('/cards/{cardId}', 'CardController@destroy');

    Route::get('/content', 'ContentController@index');
    Route::post('/content', 'ContentController@store');
    Route::get('/content/{contentId}', 'ContentController@show');
    Route::patch('/content/{contentId}', 'ContentController@update');
    Route::delete('/content/{contentId}', 'ContentController@destroy');

    Route::get('/students', 'StudentController@index');
    Route::post('/students', 'StudentController@store');
    Route::get('/students/{studentId}', 'StudentController@show');
    Route::patch('/students/{studentId}', 'StudentController@update');
    Route::delete('/students/{studentId}', 'StudentController@destroy');
    
    Route::post('/files', 'FileController@store');
    
    
    
    // Route::get('/services/sitetitle/{site}', 'Services\ServiceController@siteTitle')->where('site', '.*');


});


// Route::get('/user', 'UserController@show')->middleware('auth:sanctum');

// Route::get('/boards', 'BoardController@index')->middleware('auth:sanctum');