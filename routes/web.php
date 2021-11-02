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

Route::get('/', function () {
    return view('welcome');
});


// following route won't work because middleware is already been used in the constructor of controller
//Route::get('/get-users','App\Http\Controllers\SapApiController@index')->middleware('guest');

// keep following route
Route::get('/get-users','App\Http\Controllers\SapApiController@index')->name('getusers');