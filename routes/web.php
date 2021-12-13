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
        Route::post('promotion/get-territories/','App\Http\Controllers\PromotionsController@getTerritories')->name('promotion.getTerritories');
        Route::post('promotion/get-classes/','App\Http\Controllers\PromotionsController@getClasses')->name('promotion.getClasses');
        Route::post('promotion/get-sales-specialist/','App\Http\Controllers\PromotionsController@getSalesSpecialist')->name('promotion.getSalesSpecialist');
        Route::post('promotion/get-promotion-interest-data/','App\Http\Controllers\PromotionsController@getPromotionInterestData')->name('promotion.get-promotion-interest-data');

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

	    Route::get('product-list/', 'App\Http\Controllers\ProductListController@index')->name('product-list.index')->middleware('not-super-admin');
	    Route::get('product-list/{id}', 'App\Http\Controllers\ProductListController@show')->name('product-list.show')->middleware('not-super-admin');
	    Route::post('product-list/get-all', 'App\Http\Controllers\ProductListController@getAll')->name('product-list.get-all')->middleware('not-super-admin');

        // Territories
        Route::resource('territory','App\Http\Controllers\TerritoriesController');
	    Route::post('territory/get-all', 'App\Http\Controllers\TerritoriesController@getAll')->name('territory.get-all');
	    Route::post('territory/sync-territory', 'App\Http\Controllers\TerritoriesController@syncTerritories')->name('territory.sync-territory');

	    Route::resource('customer-group','App\Http\Controllers\CustomerGroupController');
	    Route::post('customer-group/get-all', 'App\Http\Controllers\CustomerGroupController@getAll')->name('customer-group.get-all');
	    Route::post('customer-group/sync-customer-groups', 'App\Http\Controllers\CustomerGroupController@syncCustomerGroups')->name('customer-group.sync-customer-groups');

        // Sales Specialist Assignment
        Route::resource('customers-sales-specialist','App\Http\Controllers\CustomersSalesSpecialistsController');
	    Route::post('customers-sales-specialist/get-all', 'App\Http\Controllers\CustomersSalesSpecialistsController@getAll')->name('customers-sales-specialist.get-all');
	    Route::post('customers-sales-specialist/status/{id}', 'App\Http\Controllers\CustomersSalesSpecialistsController@updateStatus')->name('customers-sales-specialist.status');
        Route::post('customers-sales-specialist/get-customers/','App\Http\Controllers\CustomersSalesSpecialistsController@getCustomers')->name('customers-sales-specialist.getCustomers');
        Route::post('customers-sales-specialist/get-salse-specialist/','App\Http\Controllers\CustomersSalesSpecialistsController@getSalseSpecialist')->name('customers-sales-specialist.getSalseSpecialist');

	    Route::resource('class','App\Http\Controllers\ClassController');
	    Route::post('class/get-all', 'App\Http\Controllers\ClassController@getAll')->name('class.get-all');

	    // Territory Sales Specialist
        Route::resource('territory-sales-specialist','App\Http\Controllers\TerritorySalesSpecialistController');
	    Route::post('territory-sales-specialist/get-all', 'App\Http\Controllers\TerritorySalesSpecialistController@getAll')->name('territory-sales-specialist.get-all');
        Route::post('territory-sales-specialist/get-territory/','App\Http\Controllers\TerritorySalesSpecialistController@getTerritory')->name('territory-sales-specialist.get-territory');
        Route::post('territory-sales-specialist/get-sales-specialist/','App\Http\Controllers\TerritorySalesSpecialistController@getSalesSpecialist')->name('territory-sales-specialist.get-sales-specialist');

        // Local Orders
        Route::resource('sales-specialist-orders','App\Http\Controllers\LocalOrderController', [
            'names' => [
                'index' => 'sales-specialist-orders.index',
                'create' => 'sales-specialist-orders.create',
                'store' => 'sales-specialist-orders.store',
                'edit' => 'sales-specialist-orders.edit',
            ]
        ]);
        Route::post('sales-specialist-orders/get-all', 'App\Http\Controllers\LocalOrderController@getAll')->name('sales-specialist-orders.get-all');
        Route::post('sales-specialist-orders/get-customers/','App\Http\Controllers\LocalOrderController@getCustomers')->name('sales-specialist-orders.getCustomers');
        Route::post('sales-specialist-orders/get-products/','App\Http\Controllers\LocalOrderController@getProducts')->name('sales-specialist-orders.getProducts');
        Route::post('sales-specialist-orders/get-address/','App\Http\Controllers\LocalOrderController@getAddress')->name('sales-specialist-orders.getAddress');

        Route::get('customer-promotion/', 'App\Http\Controllers\CustomerPromotionController@index')->name('customer-promotion.index')/*->middleware('not-super-admin')*/;
        Route::post('customer-promotion/get-all', 'App\Http\Controllers\CustomerPromotionController@getAll')->name('customer-promotion.get-all');
        Route::get('customer-promotion/show/{id}', 'App\Http\Controllers\CustomerPromotionController@show')->name('customer-promotion.show');
        Route::post('customer-promotion/get-all-product-list', 'App\Http\Controllers\CustomerPromotionController@getAllProductList')->name('customer-promotion.get-all-product-list');
        Route::post('customer-promotion/store-interest', 'App\Http\Controllers\CustomerPromotionController@storeInterest')->name('customer-promotion.store-interest');
        Route::get('customer-promotion/product-detail/{id}/{promotion_id}', 'App\Http\Controllers\CustomerPromotionController@productDetail')->name('customer-promotion.product-detail');


        Route::get('customer-promotion/order/', 'App\Http\Controllers\CustomerPromotionController@orderIndex')->name('customer-promotion.order.index');
        Route::get('customer-promotion/order/{id}', 'App\Http\Controllers\CustomerPromotionController@orderCreate')->name('customer-promotion.order.create');
        Route::post('customer-promotion/order/store', 'App\Http\Controllers\CustomerPromotionController@orderStore')->name('customer-promotion.order.store');
        Route::post('customer-promotion/order/get-all', 'App\Http\Controllers\CustomerPromotionController@orderGetAll')->name('customer-promotion.order.get-all');

    });

    // Pramotion Type
    Route::resource('promotion-type','App\Http\Controllers\PromotionTypeController')->middleware('super-admin');
    Route::post('promotion-type/get-all', 'App\Http\Controllers\PromotionTypeController@getAll')->name('promotion-type.get-all')->middleware('super-admin');
    Route::post('promotion-type/status/{id}', 'App\Http\Controllers\PromotionTypeController@updateStatus')->name('promotion-type.status')->middleware('super-admin');
    Route::post('promotion-type/get-products/','App\Http\Controllers\PromotionTypeController@getProducts')->name('promotion-type.get-products')->middleware('super-admin');


	Route::resource('help-desk','App\Http\Controllers\HelpDeskController');
    Route::post('help-desk/get-all', 'App\Http\Controllers\HelpDeskController@getAll')->name('help-desk.get-all');
    Route::post('help-desk/status', 'App\Http\Controllers\HelpDeskController@updateStatus')->name('help-desk.status');
    Route::post('help-desk/comment/store', 'App\Http\Controllers\HelpDeskController@storeComment')->name('help-desk.comment.store');
    Route::post('help-desk/comment/get-all', 'App\Http\Controllers\HelpDeskController@getAllComment')->name('help-desk.comment.get-all');
});

Route::get('clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:cache');
    Artisan::call('config:clear');
    return "Cache is cleared";
});
