<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::middleware('auth:sanctum')->get('/apitest', function (Request $request) {
    return $request->user()->boards;
});