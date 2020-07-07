<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'BoardController@index');

Route::get('/boards', 'BoardController@index')->name('boards.index');
Route::post('/boards', 'BoardController@store');

// Route::get('/boards/{board}', 'BoardController@show')->name('boards.show');
Route::get('/apitest', 'BoardController@index');

Route::get('/boards/{board}/edit', 'BoardController@edit');
Route::put('/boards/{board}', 'BoardController@update');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
