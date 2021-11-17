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


Route::middleware('guest')->group(function(){
	Route::get('/login','App\Http\Controllers\LoginController@index')->name('login');
	Route::post('/login','App\Http\Controllers\LoginController@checkLogin')->name('check-login');

	// Forgot Password
	Route::get('forgot-password/', 'App\Http\Controllers\ForgotPasswordController@index')->name('forgot-password.index');
	Route::get('forgot-password/{email}/{token}', 'App\Http\Controllers\ForgotPasswordController@showResetPasswordForm')->name('forgot-password.reset');
	Route::post('forgot-password/email', 'App\Http\Controllers\ForgotPasswordController@email')->name('forgot-password.email');
	Route::post('forgot-password/reset-password', 'App\Http\Controllers\ForgotPasswordController@resetPassword')->name('forgot-password.reset-password');
});


//Route::get('/get-users','App\Http\Controllers\SapApiController@index');

Route::middleware(['auth'])->group(function(){
	Route::get('/home','App\Http\Controllers\HomeController@index')->name('home');

	Route::get('/logout', function () {
		Auth::logout();
	    return redirect()->route('login');
	})->name('logout');


	Route::resource('profile', 'App\Http\Controllers\ProfileController')->except('show');
    Route::get('profile/change-password', 'App\Http\Controllers\ProfileController@changePasswordIndex')->name('profile.change-password.index');
    Route::post('profile/change-password', 'App\Http\Controllers\ProfileController@changePasswordStore')->name('profile.change-password.store');


    Route::middleware('check-access')->group(function(){

		Route::resource('customer','App\Http\Controllers\CustomerController');
	    Route::post('customer/get-all', 'App\Http\Controllers\CustomerController@getAll')->name('customer.get-all');
	    Route::post('customer/sync-customers', 'App\Http\Controllers\CustomerController@syncCustomers')->name('customer.sync-customers');

	    Route::resource('role','App\Http\Controllers\RoleController')->middleware('super-admin');
	    Route::post('role/get-all', 'App\Http\Controllers\RoleController@getAll')->name('role.get-all')->middleware('super-admin');

	    Route::resource('user','App\Http\Controllers\UserController');
	    Route::post('user/get-all', 'App\Http\Controllers\UserController@getAll')->name('user.get-all');
	    Route::post('user/status/{id}', 'App\Http\Controllers\UserController@updateStatus')->name('user.status');


		Route::resource('productfeatures','App\Http\Controllers\ProductFeaturesController')->middleware('super-admin');
	    Route::post('productfeatures/get-all', 'App\Http\Controllers\ProductFeaturesController@getAll')->name('productfeatures.get-all')->middleware('super-admin');

		Route::resource('productbenefits','App\Http\Controllers\ProductBenefitsController')->middleware('super-admin');
	    Route::post('productbenefits/get-all', 'App\Http\Controllers\ProductBenefitsController@getAll')->name('productbenefits.get-all')->middleware('super-admin');

		Route::resource('productsellsheets','App\Http\Controllers\ProductSellSheetsController')->middleware('super-admin');
	    Route::post('productsellsheets/get-all', 'App\Http\Controllers\ProductSellSheetsController@getAll')->name('productsellsheets.get-all')->middleware('super-admin');


        Route::resource('sales-persons','App\Http\Controllers\SalesPersonsController');
	    Route::post('sales-persons/get-all', 'App\Http\Controllers\SalesPersonsController@getAll')->name('sales-persons.get-all');
	    Route::post('sales-persons/sync-sales-persons', 'App\Http\Controllers\SalesPersonsController@syncSalesPersons')->name('sales-persons.sync-sales-persons');

	    Route::resource('product','App\Http\Controllers\ProductController');
	    Route::post('product/get-all', 'App\Http\Controllers\ProductController@getAll')->name('product.get-all');
	    Route::post('product/sync-products', 'App\Http\Controllers\ProductController@syncProducts')->name('product.sync-products');

    });

});

Route::get('clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:cache');
    Artisan::call('config:clear');
    return "Cache is cleared";
});
