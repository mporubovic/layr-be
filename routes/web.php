<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\File;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return File::get(public_path() . '/index.html');
});

Route::group(['prefix' => 'auth'], function () {

    Route::get('/csrf-cookie', '\Laravel\Sanctum\Http\Controllers\CsrfCookieController@show');

    Route::post('/login', 'Auth\LoginController@login');
    Route::post('/logout', 'Auth\LoginController@logout');
    Route::post('/register', 'Auth\RegisterController@register');
});

Route::get('/{any}', function () {
    return File::get(public_path() . '/index.html');
})->where('any', '.*');
