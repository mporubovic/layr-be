<?php

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
    return $request->user();
});

// Route::get('/apitest', 'BoardController@index');
// Route::get('/apitest', function () {
//     return request()->user()->boards;
// })->middleware('auth:sanctum');


// Route::middleware('auth:sanctum')->get('/boards', function (Request $request) {
//     return $request->user()->boards;
// });

Route::middleware('auth:sanctum')->get('/boards', 'BoardController@index');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/boards', 'BoardController@index');
    Route::get('/boards/{boardId}', 'BoardController@show');
});


// Route::get('/user', 'UserController@show')->middleware('auth:sanctum');

// Route::get('/boards', 'BoardController@index')->middleware('auth:sanctum');