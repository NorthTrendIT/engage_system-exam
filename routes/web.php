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
    return redirect()->route('login');
    //return view('welcome');
});


Route::get('/login','App\Http\Controllers\LoginController@index')->name('login')->middleware('guest');
Route::post('/login','App\Http\Controllers\LoginController@checkLogin')->name('check-login')->middleware('guest');

Route::get('/get-users','App\Http\Controllers\SapApiController@index');

Route::middleware('auth')->group(function(){
	Route::get('/home','App\Http\Controllers\HomeController@index')->name('home');
	
	Route::get('/logout', function () {
		Auth::logout();
	    return redirect()->route('login');
	})->name('logout');

	Route::resource('customer','App\Http\Controllers\CustomerController');

});

Route::get('clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:cache');
    Artisan::call('config:clear');
    return "Cache is cleared";
});
