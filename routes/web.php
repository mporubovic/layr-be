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
// Route::get('/apitest', 'BoardController@index');


// Route::get('/', 'BoardController@index');


// Route::group(['domain' => ''])

// Route::get('/{any}', function () {
//     return \File::get(public_path() . '/indexv.html');
// })->where('any', '.*');


Route::domain('{subdomain}.' . env('APP_DOMAIN'))->group(function ($subdomain) {
    Route::get('/', function () {
        return File::get(public_path() . '/indexv.html');

    });
});

Route::get('/', function () {
    return File::get(public_path() . '/indexw.php');
});

Route::group(['prefix' => 'auth'], function () {

    Route::get('/csrf-cookie', '\Laravel\Sanctum\Http\Controllers\CsrfCookieController@show');

    Route::post('/login', 'Auth\LoginController@login');
    Route::post('/logout', 'Auth\LoginController@logout');
});

// Route::get('/boards', 'BoardController@index')->name('boards.index');
// Route::post('/boards', 'BoardController@store');

// // Route::get('/boards/{board}', 'BoardController@show')->name('boards.show');


// Route::get('/boards/{board}/edit', 'BoardController@edit');
// Route::put('/boards/{board}', 'BoardController@update');


// Auth::routes();

// // Route::post('/login', 'Auth\LoginController@login');
// // Route::post('/logout', 'Auth\LoginController@logout');
// // Route::post('/register', 'Auth\RegisterController@register');

// Route::get('/home', 'HomeController@index')->name('home');
