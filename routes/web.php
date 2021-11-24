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
        // Add Logout log
        add_log(Auth::id(), 2, null, null);
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

	    Route::resource('role','App\Http\Controllers\RoleController')->except(['show'])->middleware('super-admin');
	    Route::post('role/get-all', 'App\Http\Controllers\RoleController@getAll')->name('role.get-all')->middleware('super-admin');
	    Route::get('role/chart', 'App\Http\Controllers\RoleController@getRoleChart')->name('role.chart')->middleware('super-admin');

	    Route::resource('user','App\Http\Controllers\UserController');
	    Route::post('user/get-all', 'App\Http\Controllers\UserController@getAll')->name('user.get-all');
	    Route::post('user/status/{id}', 'App\Http\Controllers\UserController@updateStatus')->name('user.status');
	    Route::post('user/get-city', 'App\Http\Controllers\UserController@getCity')->name('user.get-city');
	    Route::post('user/get-roles', 'App\Http\Controllers\UserController@getRoles')->name('user.get-roles');
	    Route::post('user/get-parents', 'App\Http\Controllers\UserController@getParents')->name('user.get-parents');


		Route::resource('productfeatures','App\Http\Controllers\ProductFeaturesController')->middleware('super-admin');
	    Route::post('productfeatures/get-all', 'App\Http\Controllers\ProductFeaturesController@getAll')->name('productfeatures.get-all')->middleware('super-admin');

		Route::resource('productbenefits','App\Http\Controllers\ProductBenefitsController')->middleware('super-admin');
	    Route::post('productbenefits/get-all', 'App\Http\Controllers\ProductBenefitsController@getAll')->name('productbenefits.get-all')->middleware('super-admin');

		Route::resource('productsellsheets','App\Http\Controllers\ProductSellSheetsController')->middleware('super-admin');
	    Route::post('productsellsheets/get-all', 'App\Http\Controllers\ProductSellSheetsController@getAll')->name('productsellsheets.get-all')->middleware('super-admin');

        // Sales Persons
        Route::resource('sales-persons','App\Http\Controllers\SalesPersonsController');
	    Route::post('sales-persons/get-all', 'App\Http\Controllers\SalesPersonsController@getAll')->name('sales-persons.get-all');
	    Route::post('sales-persons/sync-sales-persons', 'App\Http\Controllers\SalesPersonsController@syncSalesPersons')->name('sales-persons.sync-sales-persons');

	    Route::resource('product','App\Http\Controllers\ProductController');
	    Route::post('product/get-all', 'App\Http\Controllers\ProductController@getAll')->name('product.get-all');
	    Route::post('product/sync-products', 'App\Http\Controllers\ProductController@syncProducts')->name('product.sync-products');

        // Orders
        Route::resource('orders','App\Http\Controllers\OrdersController');
	    Route::post('orders/get-all', 'App\Http\Controllers\OrdersController@getAll')->name('orders.get-all');
	    Route::post('orders/sync-orders', 'App\Http\Controllers\OrdersController@syncOrders')->name('orders.sync-orders');

        // Invoices
        Route::resource('invoices','App\Http\Controllers\InvoicesController');
	    Route::post('invoices/get-all', 'App\Http\Controllers\InvoicesController@getAll')->name('invoices.get-all');
	    Route::post('invoices/sync-orders', 'App\Http\Controllers\InvoicesController@syncInvoices')->name('invoices.sync-invoices');

        // Pramotions
        Route::resource('promotion','App\Http\Controllers\PromotionsController');
	    Route::post('promotion/get-all', 'App\Http\Controllers\PromotionsController@getAll')->name('promotion.get-all');
        Route::post('promotion/status/{id}', 'App\Http\Controllers\PromotionsController@updateStatus')->name('promotion.status');
        Route::post('promotion/get-customers/','App\Http\Controllers\PromotionsController@getCustomers')->name('promotion.getCustomers');
        Route::post('promotion/get-products/','App\Http\Controllers\PromotionsController@getProducts')->name('promotion.getProducts');
        Route::post('promotion/get-promotion-data', 'App\Http\Controllers\PromotionsController@getPromotionData')->name('promotion.get-promotion-data');

        Route::resource('location','App\Http\Controllers\LocationController');
	    Route::post('location/get-all', 'App\Http\Controllers\LocationController@getAll')->name('location.get-all');
	    Route::post('location/status/{id}', 'App\Http\Controllers\LocationController@updateStatus')->name('location.status');

	    Route::resource('department','App\Http\Controllers\DepartmentController');
	    Route::post('department/get-all', 'App\Http\Controllers\DepartmentController@getAll')->name('department.get-all');
	    Route::post('department/status/{id}', 'App\Http\Controllers\DepartmentController@updateStatus')->name('department.status');

	    Route::resource('organisation','App\Http\Controllers\OrganisationController');

        // Activity Log
        Route::resource('activitylog','App\Http\Controllers\ActivityLogController');
	    Route::post('activitylog/get-all', 'App\Http\Controllers\ActivityLogController@getAll')->name('activitylog.get-all');
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
