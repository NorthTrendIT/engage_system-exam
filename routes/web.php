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
});

Route::middleware('guest')->group(function () {
    Route::get('/login', 'App\Http\Controllers\LoginController@index')->name('login');
    Route::get('/login-by-link/{hash?}', 'App\Http\Controllers\LoginController@loginByLink')->name('login-by-link');
    Route::post('/login', 'App\Http\Controllers\LoginController@checkLogin')->name('check-login');

    // Forgot Password
    Route::get('forgot-password/', 'App\Http\Controllers\ForgotPasswordController@index')->name('forgot-password.index');
    Route::get('forgot-password/{email}/{token}', 'App\Http\Controllers\ForgotPasswordController@showResetPasswordForm')->name('forgot-password.reset');
    Route::post('forgot-password/email', 'App\Http\Controllers\ForgotPasswordController@email')->name('forgot-password.email');
    Route::post('forgot-password/reset-password', 'App\Http\Controllers\ForgotPasswordController@resetPassword')->name('forgot-password.reset-password');
});


//Route::get('/get-users','App\Http\Controllers\SapApiController@index');

Route::middleware(['auth'])->group(function () {
    Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home')->middleware('check-login');
    Route::get('/comments', 'App\Http\Controllers\HomeController@comments')->name('comments')->middleware('check-login');

    Route::get('/payment-terms', 'App\Http\Controllers\HomeController@paymentTerms')->name('payment-terms')->middleware('check-login');
    Route::get('customer-payment', 'App\Http\Controllers\HomeController@customerPayment')->name('customer-payment')->middleware('check-login');

    Route::post('home/get-report-data', 'App\Http\Controllers\HomeController@getReportData')->name('home.get-report-data');



    Route::post('/ckeditor-image-upload', 'App\Http\Controllers\HomeController@ckeditorImageUpload')->name('ckeditor-image-upload');


    Route::get('/logout', function () {
        // Add Logout log
        add_log(Auth::id(), 2, null, null);
        Auth::logout();
        return redirect()->route('login');
    })->name('logout');


    Route::resource('profile', 'App\Http\Controllers\ProfileController')->except('show');
    Route::get('profile/change-password', 'App\Http\Controllers\ProfileController@changePasswordIndex')->name('profile.change-password.index');
    Route::post('profile/change-password', 'App\Http\Controllers\ProfileController@changePasswordStore')->name('profile.change-password.store');


    Route::middleware('check-login')->group(function () {

        Route::middleware('check-access')->group(function () {

            // Role
            Route::resource('role', 'App\Http\Controllers\RoleController')->except(['show']);
            Route::post('role/get-all', 'App\Http\Controllers\RoleController@getAll')->name('role.get-all');
            Route::get('role/chart', 'App\Http\Controllers\RoleController@getRoleChart')->name('role.chart')->middleware('super-admin');

            // Customer
            Route::get('customer/export', 'App\Http\Controllers\CustomerController@export')->name('customer.export');
            Route::resource('customer', 'App\Http\Controllers\CustomerController');
            Route::post('customer/get-all', 'App\Http\Controllers\CustomerController@getAll')->name('customer.get-all');
            Route::post('customer/sync-customers', 'App\Http\Controllers\CustomerController@syncCustomers')->name('customer.sync-customers');
            Route::post('customer/get-all-bp-address', 'App\Http\Controllers\CustomerController@getAllBpAddress')->name('customer.get-all-bp-address');
            Route::post('customer/get-territory', 'App\Http\Controllers\CustomerController@getTerritory')->name('customer.get-territory');

            Route::get('vatgroup', 'App\Http\Controllers\VatGroupController@index')->name('vatgroup.index');
            Route::post('vatgroup/sync-vatgroup', 'App\Http\Controllers\VatGroupController@syncVatGroups')->name('vatgroup.sync-vatgroup');
            Route::get('vatgroup/fetch', 'App\Http\Controllers\VatGroupController@fetchVatGroups')->name('vatgroup.fetch');

            Route::resource('user', 'App\Http\Controllers\UserController');
            Route::post('user/get-all', 'App\Http\Controllers\UserController@getAll')->name('user.get-all');
            Route::post('user/status/{id}', 'App\Http\Controllers\UserController@updateStatus')->name('user.status');
            Route::post('user/get-city', 'App\Http\Controllers\UserController@getCity')->name('user.get-city');
            Route::post('user/get-roles', 'App\Http\Controllers\UserController@getRoles')->name('user.get-roles');
            Route::post('user/get-parents', 'App\Http\Controllers\UserController@getParents')->name('user.get-parents');
            Route::post('user/change-password', 'App\Http\Controllers\UserController@changePasswordStore')->name('user.change-password.store');
            Route::get('user-password-change', 'App\Http\Controllers\UserController@userChangePassword');
            Route::get('abpw-user-password-change', 'App\Http\Controllers\UserController@userChangePasswordABPW');
            Route::get('solid-user-password-change', 'App\Http\Controllers\UserController@userChangePasswordSOLID');
            Route::get('philcrest-user-password-change', 'App\Http\Controllers\UserController@userChangePasswordPHILCREST');
            Route::get('philsyn-user-password-change', 'App\Http\Controllers\UserController@userChangePasswordPHILSYN');
            Route::get('user-export', 'App\Http\Controllers\UserController@export')->name('user.export');

            Route::get('user-test', 'App\Http\Controllers\UserController@test')->name('user.test');

            Route::resource('productfeatures', 'App\Http\Controllers\ProductFeaturesController')->middleware('super-admin');
            Route::post('productfeatures/get-all', 'App\Http\Controllers\ProductFeaturesController@getAll')->name('productfeatures.get-all')->middleware('super-admin');

            Route::resource('productbenefits', 'App\Http\Controllers\ProductBenefitsController')->middleware('super-admin');
            Route::post('productbenefits/get-all', 'App\Http\Controllers\ProductBenefitsController@getAll')->name('productbenefits.get-all')->middleware('super-admin');

            Route::resource('productsellsheets', 'App\Http\Controllers\ProductSellSheetsController')->middleware('super-admin');
            Route::post('productsellsheets/get-all', 'App\Http\Controllers\ProductSellSheetsController@getAll')->name('productsellsheets.get-all')->middleware('super-admin');

            // Sales Persons
            Route::resource('sales-persons', 'App\Http\Controllers\SalesPersonsController');
            Route::post('sales-persons/get-all', 'App\Http\Controllers\SalesPersonsController@getAll')->name('sales-persons.get-all');
            Route::post('sales-persons/sync-sales-persons', 'App\Http\Controllers\SalesPersonsController@syncSalesPersons')->name('sales-persons.sync-sales-persons');

            // Product
            Route::get('product/export', 'App\Http\Controllers\ProductController@export')->name('product.export');
            Route::resource('product', 'App\Http\Controllers\ProductController');
            Route::post('product/get-all', 'App\Http\Controllers\ProductController@getAll')->name('product.get-all');
            Route::post('product/sync-products', 'App\Http\Controllers\ProductController@syncProducts')->name('product.sync-products');
            Route::post('product/get-brand-data', 'App\Http\Controllers\ProductController@getBrandData')->name('product.get-brand-data');
            Route::post('product/get-product-category-data', 'App\Http\Controllers\ProductController@getProductCategoryData')->name('product.get-product-category-data');
            Route::post('product/get-product-line-data', 'App\Http\Controllers\ProductController@getProductLineData')->name('product.get-product-line-data');
            Route::post('product/get-product-class-data', 'App\Http\Controllers\ProductController@getProductClassData')->name('product.get-product-class-data');
            Route::post('product/get-product-type-data', 'App\Http\Controllers\ProductController@getProductTypeData')->name('product.get-product-type-data');
            Route::post('product/get-product-application-data', 'App\Http\Controllers\ProductController@getProductApplicationData')->name('product.get-product-application-data');
            Route::post('product/get-product-pattern-data', 'App\Http\Controllers\ProductController@getProductPatternData')->name('product.get-product-pattern-data');

            // Product Group
            Route::resource('product-group', 'App\Http\Controllers\ProductGroupController');
            Route::post('product-group/get-all', 'App\Http\Controllers\ProductGroupController@getAll')->name('product-group.get-all');
            Route::post('product-group/sync-product-groups', 'App\Http\Controllers\ProductGroupController@syncProductGroups')->name('product-group.sync-product-groups');
            Route::post('product-group/status/{id}', 'App\Http\Controllers\ProductGroupController@updateStatus')->name('product-group.status');

            // Orders
            Route::get('orders/export', 'App\Http\Controllers\OrdersController@export')->name('orders.export');
            Route::resource('orders', 'App\Http\Controllers\OrdersController');
            Route::post('orders/get-all', 'App\Http\Controllers\OrdersController@getAll')->name('orders.get-all');
            Route::post('orders/sync-orders', 'App\Http\Controllers\OrdersController@syncOrders')->name('orders.sync-orders');
            Route::post('orders/sync-specific-orders', 'App\Http\Controllers\OrdersController@syncSpecificOrder')->name('orders.sync-specific-orders');
            Route::post('orders/get-customer', 'App\Http\Controllers\OrdersController@getCustomer')->name('orders.get-customer');
            Route::post('orders/notify-customer', 'App\Http\Controllers\OrdersController@notifyCustomer')->name('orders.notify-customer');
            Route::post('orders/cancel-order', 'App\Http\Controllers\OrdersController@cancelOrder')->name('orders.cancel-order');
            Route::post('orders/complete-order', 'App\Http\Controllers\OrdersController@completeOrder')->name('orders.complete-order');

            Route::get('pending-orders', 'App\Http\Controllers\OrdersController@pendingOrder')->name('orders.panding-orders');
            Route::get('pending-orders/{id}', 'App\Http\Controllers\OrdersController@pendingOrderView')->name('orders.panding-orders.view');
            Route::post('pending-orders/get-all', 'App\Http\Controllers\OrdersController@getAllPendingOrder')->name('orders.get-all-pending-orders');
            Route::get('pending-promotion', 'App\Http\Controllers\OrdersController@pendingPromotion')->name('orders.pending-promotion');
            Route::get('pending-promotion/{id}', 'App\Http\Controllers\OrdersController@pendingPromotionView')->name('orders.pending-promotion.view');
            Route::post('pending-promotion/get-all', 'App\Http\Controllers\OrdersController@getAllPendingPromotion')->name('orders.get-all-pending-promotion');
            Route::post('pending-orders/push-order', 'App\Http\Controllers\OrdersController@pushSingleOrder')->name('orders.push-order');
            Route::post('pending-orders/push-all', 'App\Http\Controllers\OrdersController@pushAllOrder')->name('orders.push-all-order');
            Route::post('pending-promotion/push-all', 'App\Http\Controllers\OrdersController@pushAllPromotion')->name('orders.push-all-promotion');
            Route::post('pending-orders/delete-push-order', 'App\Http\Controllers\OrdersController@deletePushSingleOrder')->name('orders.delete-push-order');

            Route::post('orders/item_status-track', 'App\Http\Controllers\OrdersController@itemStatus')->name('orders.item_status-track');

            // Invoices
            Route::get('invoices/export', 'App\Http\Controllers\InvoicesController@export')->name('invoices.export');
            Route::resource('invoices', 'App\Http\Controllers\InvoicesController');
            Route::post('invoices/get-all', 'App\Http\Controllers\InvoicesController@getAll')->name('invoices.get-all');
            Route::post('invoices/sync-invoices', 'App\Http\Controllers\InvoicesController@syncInvoices')->name('invoices.sync-invoices');
            Route::post('invoices/sync-specific-invoices', 'App\Http\Controllers\InvoicesController@syncSpecificInvoice')->name('invoices.sync-specific-invoices');
            Route::post('invoices/get-customer', 'App\Http\Controllers\InvoicesController@getCustomer')->name('invoices.get-customer');

            // Route::resource('location','App\Http\Controllers\LocationController');
            // Route::post('location/get-all', 'App\Http\Controllers\LocationController@getAll')->name('location.get-all');
            // Route::post('location/status/{id}', 'App\Http\Controllers\LocationController@updateStatus')->name('location.status');

            Route::resource('department', 'App\Http\Controllers\DepartmentController');
            Route::post('department/get-all', 'App\Http\Controllers\DepartmentController@getAll')->name('department.get-all');
            Route::post('department/status/{id}', 'App\Http\Controllers\DepartmentController@updateStatus')->name('department.status');

            Route::resource('organisation', 'App\Http\Controllers\OrganisationController');

            // Activity Log
            Route::get('activitylog/export', 'App\Http\Controllers\ActivityLogController@export')->name('activitylog.export');
            Route::resource('activitylog', 'App\Http\Controllers\ActivityLogController');
            Route::post('activitylog/get-all', 'App\Http\Controllers\ActivityLogController@getAll')->name('activitylog.get-all');
            Route::post('activitylog/clear-all-logs', 'App\Http\Controllers\ActivityLogController@clearAllLogs')->name('activitylog.clear-all-logs');

            Route::get('product-list/', 'App\Http\Controllers\ProductListController@index')->name('product-list.index')->middleware('not-super-admin');
            Route::get('product-list/{id}/{customer_id?}', 'App\Http\Controllers\ProductListController@show')->name('product-list.show')->middleware('not-super-admin');
            Route::post('product-list/get-all', 'App\Http\Controllers\ProductListController@getAll')->name('product-list.get-all')->middleware('not-super-admin');
            Route::post('product-list/get-products', 'App\Http\Controllers\ProductListController@getProducts')->name('product-list.get-products')->middleware('not-super-admin');
            Route::resource('recommended-products', 'App\Http\Controllers\RecommendedProductController')->middleware('not-super-admin');
            Route::post('recommended-products/get-all', 'App\Http\Controllers\RecommendedProductController@getAll')->name('recommended-products.get-all')->middleware('not-super-admin');
            Route::post('recommended-products/get-customers/', 'App\Http\Controllers\RecommendedProductController@getCustomers')->name('recommended-products.getCustomers')->middleware('not-super-admin');
            Route::get('recommended-products/customer-cart/{id}', 'App\Http\Controllers\RecommendedProductController@customerCart')->name('recommended-products.goToCart')->middleware('not-super-admin');
            Route::post('recommended-products/customer-cart/add/{id}', 'App\Http\Controllers\RecommendedProductController@addToCart')->name('recommended-products.cart.add')->middleware('not-super-admin');
            Route::post('recommended-products/customer-cart/remove/{id}', 'App\Http\Controllers\RecommendedProductController@removeFromCart')->name('recommended-products.cart.remove')->middleware('not-super-admin');
            Route::post('recommended-products/customer-cart/update-qty/{id}', 'App\Http\Controllers\RecommendedProductController@updateQty')->name('recommended-products.cart.update-qty')->middleware('not-super-admin');
            Route::post('recommended-products/customer-cart/qty-plus/{id}', 'App\Http\Controllers\RecommendedProductController@qtyPlus')->name('recommended-products.cart.qty-plus')->middleware('not-super-admin');
            Route::post('recommended-products/customer-cart/qty-minus/{id}', 'App\Http\Controllers\RecommendedProductController@qtyMinus')->name('recommended-products.cart.qty-minus')->middleware('not-super-admin');
            Route::post('recommended-products/customer-cart/placeOrder', 'App\Http\Controllers\RecommendedProductController@placeOrder')->name('recommended-products.cart.placeOrder')->middleware('not-super-admin');
            Route::post('recommended-products/customer-cart/saveToDraft', 'App\Http\Controllers\RecommendedProductController@saveToDraft')->name('recommended-products.cart.saveToDraft')->middleware('not-super-admin');
            Route::post('get-product-details', 'App\Http\Controllers\ProductListController@getProductDetails')->name('product-list.get-product-details')->middleware('not-super-admin');



            // Territories
            Route::resource('territory', 'App\Http\Controllers\TerritoriesController');
            Route::post('territory/get-all', 'App\Http\Controllers\TerritoriesController@getAll')->name('territory.get-all');
            Route::post('territory/sync-territory', 'App\Http\Controllers\TerritoriesController@syncTerritories')->name('territory.sync-territory');

            // Customer Groups
            Route::resource('customer-group', 'App\Http\Controllers\CustomerGroupController');
            Route::post('customer-group/get-all', 'App\Http\Controllers\CustomerGroupController@getAll')->name('customer-group.get-all');
            Route::post('customer-group/sync-customer-groups', 'App\Http\Controllers\CustomerGroupController@syncCustomerGroups')->name('customer-group.sync-customer-groups');

            // Sales Specialist Assignment
            Route::resource('customers-sales-specialist', 'App\Http\Controllers\CustomersSalesSpecialistsController')->except(['show']);
            Route::post('customers-sales-specialist/get-all', 'App\Http\Controllers\CustomersSalesSpecialistsController@getAll')->name('customers-sales-specialist.get-all');
            Route::post('customers-sales-specialist/status/{id}', 'App\Http\Controllers\CustomersSalesSpecialistsController@updateStatus')->name('customers-sales-specialist.status');

            Route::get('customers-sales-specialist/import', 'App\Http\Controllers\CustomersSalesSpecialistsController@importIndex')->name('customers-sales-specialist.import.index');
            Route::post('customers-sales-specialist/import', 'App\Http\Controllers\CustomersSalesSpecialistsController@importStore')->name('customers-sales-specialist.import.store');

            Route::get('customers-sales-specialist/{id}', 'App\Http\Controllers\CustomersSalesSpecialistsController@show')->name('customers-sales-specialist.show');

            Route::post('customers-sales-specialist/get-customers/', 'App\Http\Controllers\CustomersSalesSpecialistsController@getCustomers')->name('customers-sales-specialist.getCustomers');
            Route::post('customers-sales-specialist/get-customer-groups/', 'App\Http\Controllers\CustomersSalesSpecialistsController@getCustomerGroups')->name('customers-sales-specialist.getCustomerGroups');

            Route::post('customers-sales-specialist/get-salse-specialist/', 'App\Http\Controllers\CustomersSalesSpecialistsController@getSalseSpecialist')->name('customers-sales-specialist.getSalseSpecialist');
            Route::post('customers-sales-specialist/get-product-brand/', 'App\Http\Controllers\CustomersSalesSpecialistsController@getProductBrand')->name('customers-sales-specialist.get-product-brand');
            Route::post('customers-sales-specialist/get-product-line/', 'App\Http\Controllers\CustomersSalesSpecialistsController@getProductLine')->name('customers-sales-specialist.get-product-line');
            Route::post('customers-sales-specialist/get-product-category/', 'App\Http\Controllers\CustomersSalesSpecialistsController@getProductCategory')->name('customers-sales-specialist.get-product-category');

            Route::post('customers-sales-specialist/get-assigned-customer-list/', 'App\Http\Controllers\CustomersSalesSpecialistsController@getAssignedCustomerList')->name('customers-sales-specialist.get-assigned-customer-list');

            Route::resource('class', 'App\Http\Controllers\ClassController');
            Route::post('class/get-all', 'App\Http\Controllers\ClassController@getAll')->name('class.get-all');

            // Territory Sales Specialist
            Route::resource('territory-sales-specialist', 'App\Http\Controllers\TerritorySalesSpecialistController');
            Route::post('territory-sales-specialist/get-all', 'App\Http\Controllers\TerritorySalesSpecialistController@getAll')->name('territory-sales-specialist.get-all');
            Route::post('territory-sales-specialist/get-territory/', 'App\Http\Controllers\TerritorySalesSpecialistController@getTerritory')->name('territory-sales-specialist.get-territory');
            Route::post('territory-sales-specialist/get-sales-specialist/', 'App\Http\Controllers\TerritorySalesSpecialistController@getSalesSpecialist')->name('territory-sales-specialist.get-sales-specialist');

            // Cart
            Route::resource('cart', 'App\Http\Controllers\CartController');
            Route::post('cart/add/{id}', 'App\Http\Controllers\CartController@addToCart')->name('cart.add');
            Route::post('cart/remove/{id}', 'App\Http\Controllers\CartController@removeFromCart')->name('cart.remove');

            Route::post('cart/removeall', 'App\Http\Controllers\CartController@removeAllFromCart')->name('cart.removeall');

            Route::post('cart/update-qty/{id}', 'App\Http\Controllers\CartController@updateQty')->name('cart.update-qty');
            Route::post('cart/placeOrder', 'App\Http\Controllers\CartController@placeOrder')->name('cart.placeOrder');
            Route::post('cart/qty-plus/{id}', 'App\Http\Controllers\CartController@qtyPlus')->name('cart.qty-plus');
            Route::post('cart/qty-minus/{id}', 'App\Http\Controllers\CartController@qtyMinus')->name('cart.qty-minus');
            Route::post('cart/get-product-list', 'App\Http\Controllers\CartController@getAllProductList')->name('cart.get.product.list');


            Route::get('customer-promotion/', 'App\Http\Controllers\CustomerPromotionController@index')->name('customer-promotion.index')/*->middleware('not-super-admin')*/;
            Route::post('customer-promotion/get-all', 'App\Http\Controllers\CustomerPromotionController@getAll')->name('customer-promotion.get-all');
            Route::get('customer-promotion/show/{id}', 'App\Http\Controllers\CustomerPromotionController@show')->name('customer-promotion.show');
            Route::post('customer-promotion/get-all-product-list', 'App\Http\Controllers\CustomerPromotionController@getAllProductList')->name('customer-promotion.get-all-product-list');
            Route::post('customer-promotion/store-interest', 'App\Http\Controllers\CustomerPromotionController@storeInterest')->name('customer-promotion.store-interest');
            Route::get('customer-promotion/get-interest', 'App\Http\Controllers\CustomerPromotionController@getInterest')->name('customer-promotion.get-interest');
            Route::get('customer-promotion/product-detail/{id}/{promotion_id}/{customer_id?}', 'App\Http\Controllers\CustomerPromotionController@productDetail')->name('customer-promotion.product-detail');
            Route::post('customer-promotion/get-customer', 'App\Http\Controllers\CustomerPromotionController@getCustomer')->name('customer-promotion.get-customer');


            Route::get('customer-promotion/order/', 'App\Http\Controllers\CustomerPromotionController@orderIndex')->name('customer-promotion.order.index');
            Route::get('customer-promotion/order/create/{id}/{customer_id?}', 'App\Http\Controllers\CustomerPromotionController@orderCreate')->name('customer-promotion.order.create');
            Route::get('customer-promotion/order/edit/{id}/{customer_id?}', 'App\Http\Controllers\CustomerPromotionController@orderEdit')->name('customer-promotion.order.edit');
            Route::post('customer-promotion/order/store', 'App\Http\Controllers\CustomerPromotionController@orderStore')->name('customer-promotion.order.store');
            Route::post('customer-promotion/order/get-all', 'App\Http\Controllers\CustomerPromotionController@orderGetAll')->name('customer-promotion.order.get-all');
            Route::get('customer-promotion/order/show/{id}', 'App\Http\Controllers\CustomerPromotionController@orderShow')->name('customer-promotion.order.show');
            Route::post('customer-promotion/order/get-customer-address', 'App\Http\Controllers\CustomerPromotionController@getCustomerAddress')->name('customer-promotion.order.get-customer-address');
            Route::post('customer-promotion/order/status', 'App\Http\Controllers\CustomerPromotionController@orderStatus')->name('customer-promotion.order.status');
            Route::post('customer-promotion/order/push-in-sap', 'App\Http\Controllers\CustomerPromotionController@orderPushInSap')->name('customer-promotion.order.push-in-sap');
            Route::post('customer-promotion/order/approved', 'App\Http\Controllers\CustomerPromotionController@orderApproved')->name('customer-promotion.order.approved');
            Route::get('customer-promotion/order/export', 'App\Http\Controllers\CustomerPromotionController@orderExport')->name('customer-promotion.order.export');
            Route::post('customer-promotion/order/sync-delivery-status', 'App\Http\Controllers\CustomerPromotionController@orderSyncDeliveryStatus')->name('customer-promotion.order.sync-delivery-status');

            // Quotations
            Route::resource('quotation', 'App\Http\Controllers\QuotationController');
            Route::post('quotation/get-all', 'App\Http\Controllers\QuotationController@getAll')->name('quotation.get-all');
            Route::post('quotation/sync-quotation', 'App\Http\Controllers\QuotationController@syncQuotations')->name('quotation.sync-quotation');

            // News and Announcement
            Route::resource('news-and-announcement', 'App\Http\Controllers\NewsAndAnnouncementController');
            Route::post('news-and-announcement/get-all', 'App\Http\Controllers\NewsAndAnnouncementController@getAll')->name('news-and-announcement.get-all');
            Route::post('news-and-announcement/get-roles/', 'App\Http\Controllers\NewsAndAnnouncementController@getRoles')->name('news-and-announcement.getRoles');
            Route::post('news-and-announcement/get-customer', 'App\Http\Controllers\NewsAndAnnouncementController@getCustomer')->name('news-and-announcement.getCustomer');
            Route::post('news-and-announcement/get-class-customer', 'App\Http\Controllers\NewsAndAnnouncementController@getClassCustomer')->name('news-and-announcement.getClassCustomer');
            Route::post('news-and-announcement/get-customer-class', 'App\Http\Controllers\NewsAndAnnouncementController@getCustomerClass')->name('news-and-announcement.getCustomerClass');
            Route::post('news-and-announcement/get-sales-specialist', 'App\Http\Controllers\NewsAndAnnouncementController@getSalesSpecialist')->name('news-and-announcement.getSalesSpecialist');
            Route::post('news-and-announcement/get-territory', 'App\Http\Controllers\NewsAndAnnouncementController@getTerritory')->name('news-and-announcement.getTerritory');
            Route::post('news-and-announcement/get-brands', 'App\Http\Controllers\NewsAndAnnouncementController@getBrands')->name('news-and-announcement.getBrands');
            Route::post('news-and-announcement/get-market-sector', 'App\Http\Controllers\NewsAndAnnouncementController@getMarketSector')->name('news-and-announcement.getMarketSector');
            Route::post('news-and-announcement/get-all-role', 'App\Http\Controllers\NewsAndAnnouncementController@getAllRole')->name('news-and-announcement.getAllRole');
            Route::post('news-and-announcement/get-all-customer', 'App\Http\Controllers\NewsAndAnnouncementController@getAllCustomer')->name('news-and-announcement.getAllCustomer');
            Route::post('news-and-announcement/get-all-sales-specialist', 'App\Http\Controllers\NewsAndAnnouncementController@getAllSalesSpecialist')->name('news-and-announcement.getAllSalesSpecialist');
            Route::post('news-and-announcement/get-all-customer-class', 'App\Http\Controllers\NewsAndAnnouncementController@getAllCustomerClass')->name('news-and-announcement.getAllCustomerClass');
            Route::post('news-and-announcement/get-all-territory', 'App\Http\Controllers\NewsAndAnnouncementController@getAllTerritory')->name('news-and-announcement.getAllTerritory');
            Route::post('news-and-announcement/get-all-market-sector', 'App\Http\Controllers\NewsAndAnnouncementController@getAllMarketSector')->name('news-and-announcement.getAllMarketSector');
            Route::post('news-and-announcement/get-all-brands', 'App\Http\Controllers\NewsAndAnnouncementController@getAllBrands')->name('news-and-announcement.getAllBrands');
            Route::post('news-and-announcement/status/{id}', 'App\Http\Controllers\NewsAndAnnouncementController@updateStatus')->name('news-and-announcement.status');

            // Warranty
            Route::resource('warranty', 'App\Http\Controllers\WarrantyController');
            Route::post('warranty/get-all', 'App\Http\Controllers\WarrantyController@getAll')->name('warranty.get-all');
            Route::post('warranty/get-customer', 'App\Http\Controllers\WarrantyController@getCustomer')->name('warranty.get-customer');
            Route::post('warranty/get-department', 'App\Http\Controllers\WarrantyController@getDepartment')->name('warranty.get-department');
            Route::post('warranty/get-department-user', 'App\Http\Controllers\WarrantyController@getDepartmentUser')->name('warranty.get-department-user');
            Route::post('warranty/store-assignment', 'App\Http\Controllers\WarrantyController@storeAssignment')->name('warranty.store-assignment');
            Route::post('warranty/store-diagnostic-report', 'App\Http\Controllers\WarrantyController@storeDiagnosticReport')->name('warranty.store-diagnostic-report');
            Route::get('warranty/export-view/{id}', 'App\Http\Controllers\WarrantyController@exportView')->name('warranty.export-view');
        });

        // Customer Orders
        Route::resource('customer-order', 'App\Http\Controllers\CustomerOrderController');
        Route::post('customer-order/get-all', 'App\Http\Controllers\CustomerOrderController@getAll')->name('customer-order.get-all');

        // DraftOrder
        Route::resource('draft-order', 'App\Http\Controllers\DraftOrderController');
        Route::post('draft-order/get-all', 'App\Http\Controllers\DraftOrderController@getAll')->name('draft-order.get-all');
        Route::post('draft-order/get-products/', 'App\Http\Controllers\DraftOrderController@getProducts')->name('draft-order.getProducts');
        Route::post('draft-order/get-address/', 'App\Http\Controllers\DraftOrderController@getAddress')->name('draft-order.getAddress');
        Route::post('draft-order/place-order/', 'App\Http\Controllers\DraftOrderController@placeOrder')->name('draft-order.placeOrder');
        Route::post('draft-order/get-price/', 'App\Http\Controllers\DraftOrderController@getPrice')->name('draft-order.get-price');


        // Local Orders
        Route::resource('sales-specialist-orders', 'App\Http\Controllers\LocalOrderController', [
            'names' => [
                'index' => 'sales-specialist-orders.index',
                'create' => 'sales-specialist-orders.create',
                'store' => 'sales-specialist-orders.store',
                'edit' => 'sales-specialist-orders.edit',
                'show' => 'sales-specialist-orders.show',
            ]
        ]);
        Route::post('sales-specialist-orders/get-all', 'App\Http\Controllers\LocalOrderController@getAll')->name('sales-specialist-orders.get-all');
        Route::post('sales-specialist-orders/get-customers/', 'App\Http\Controllers\LocalOrderController@getCustomers')->name('sales-specialist-orders.getCustomers');
        Route::post('sales-specialist-orders/get-products/', 'App\Http\Controllers\LocalOrderController@getProducts')->name('sales-specialist-orders.getProducts');
        Route::post('sales-specialist-orders/get-address/', 'App\Http\Controllers\LocalOrderController@getAddress')->name('sales-specialist-orders.getAddress');
        Route::post('sales-specialist-orders/place-order/', 'App\Http\Controllers\LocalOrderController@placeOrder')->name('sales-specialist-orders.placeOrder');
        Route::post('sales-specialist-orders/get-price/', 'App\Http\Controllers\LocalOrderController@getPrice')->name('sales-specialist-orders.get-price');
        Route::post('sales-specialist-orders/get-customer-schedule/', 'App\Http\Controllers\LocalOrderController@getCustomerSchedule')->name('sales-specialist-orders.get-customer-schedule');


        // Common Routes
        Route::post('common/get-business-units', 'App\Http\Controllers\CommonController@getBusinessUnits')->name('common.getBusinessUnits');
        Route::post('common/get-territory', 'App\Http\Controllers\CommonController@getTerritory')->name('common.getTerritory');
        Route::post('common/get-market-sector', 'App\Http\Controllers\CommonController@getMarketSector')->name('common.getMarketSector');
        Route::post('common/get-market-sub-sector', 'App\Http\Controllers\CommonController@getMarketSubSector')->name('common.getMarketSubSector');
        Route::post('common/get-region', 'App\Http\Controllers\CommonController@getRegion')->name('common.getRegion');
        Route::post('common/get-province', 'App\Http\Controllers\CommonController@getProvince')->name('common.getProvince');
        Route::post('common/get-city', 'App\Http\Controllers\CommonController@getCity')->name('common.getCity');
        Route::post('common/get-branch', 'App\Http\Controllers\CommonController@getBranch')->name('common.getBranch');
        Route::post('common/get-sales-specialist', 'App\Http\Controllers\CommonController@getSalesSpecialist')->name('common.getSalesSpecialist');
        Route::post('common/get-customer-class', 'App\Http\Controllers\CommonController@getCustomerClass')->name('common.getCustomerClass');
        Route::post('common/get-brands', 'App\Http\Controllers\CommonController@getBrands')->name('common.getBrands');
        Route::post('common/get-users', 'App\Http\Controllers\CommonController@getUsers')->name('common.getUsers');
        Route::post('common/get-promotion-codes', 'App\Http\Controllers\CommonController@getPromotionCodes')->name('common.getPromotionCodes');
        Route::post('common/get-customers', 'App\Http\Controllers\CommonController@getCustomer')->name('common.getCustomer');
        Route::post('common/get-product-category', 'App\Http\Controllers\CommonController@getProductCategory')->name('common.getProductCategory');
        Route::post('common/get-product-line', 'App\Http\Controllers\CommonController@getProductLine')->name('common.getProductLine');
        Route::post('common/get-product-class', 'App\Http\Controllers\CommonController@getProductClass')->name('common.getProductClass');
        Route::post('common/get-product-type', 'App\Http\Controllers\CommonController@getProductType')->name('common.getProductType');
        Route::post('common/get-product-application', 'App\Http\Controllers\CommonController@getProductApplication')->name('common.getProductApplication');
        Route::post('common/get-product-pattern', 'App\Http\Controllers\CommonController@getProductPattern')->name('common.getProductPattern');



        // Customer Delivery Schedule
        Route::get('customer-delivery-schedule/all-view', 'App\Http\Controllers\CustomerDeliveryScheduleController@allView')->name('customer-delivery-schedule.all-view');

        Route::resource('help-desk', 'App\Http\Controllers\HelpDeskController');
        Route::post('help-desk/get-all', 'App\Http\Controllers\HelpDeskController@getAll')->name('help-desk.get-all');
        Route::post('help-desk/status', 'App\Http\Controllers\HelpDeskController@updateStatus')->name('help-desk.status');
        Route::post('help-desk/comment/store', 'App\Http\Controllers\HelpDeskController@storeComment')->name('help-desk.comment.store');
        Route::post('help-desk/comment/get-all', 'App\Http\Controllers\HelpDeskController@getAllComment')->name('help-desk.comment.get-all');
        Route::post('help-desk/get-department', 'App\Http\Controllers\HelpDeskController@getDepartment')->name('help-desk.get-department');
        Route::post('help-desk/get-department-user', 'App\Http\Controllers\HelpDeskController@getDepartmentUser')->name('help-desk.get-department-user');
        Route::post('help-desk/store-assignment', 'App\Http\Controllers\HelpDeskController@storeAssignment')->name('help-desk.store-assignment');

        // Conversation
        Route::resource('conversation', 'App\Http\Controllers\ConversationController')->except('show');
        Route::post('conversation/search-new-user', 'App\Http\Controllers\ConversationController@searchNewUser')->name('conversation.search-new-user');
        Route::post('conversation/store-message', 'App\Http\Controllers\ConversationController@storeMessage')->name('conversation.store-message');
        Route::post('conversation/update-message', 'App\Http\Controllers\ConversationController@updateMessage')->name('conversation.update-message');
        Route::post('conversation/get-conversation-list', 'App\Http\Controllers\ConversationController@getConversationList')->name('conversation.get-conversation-list');
        Route::post('conversation/get-conversation-message-list', 'App\Http\Controllers\ConversationController@getConversationMessageList')->name('conversation.get-conversation-message-list');
    });

    // For SS Only
    Route::get('customer-delivery-schedule/ss-view', 'App\Http\Controllers\CustomerDeliveryScheduleController@ssView')->name('customer-delivery-schedule.ss-view');
    Route::post('customer-delivery-schedule/get-ss-customer-list/', 'App\Http\Controllers\CustomerDeliveryScheduleController@getSsCustomerList')->name('customer-delivery-schedule.get-ss-customer-list');

    Route::resource('customer-delivery-schedule', 'App\Http\Controllers\CustomerDeliveryScheduleController');
    Route::post('customer-delivery-schedule/get-all', 'App\Http\Controllers\CustomerDeliveryScheduleController@getAll')->name('customer-delivery-schedule.get-all');
    Route::post('customer-delivery-schedule/get-customer-list/', 'App\Http\Controllers\CustomerDeliveryScheduleController@getCustomerList')->name('customer-delivery-schedule.get-customer-list');
    Route::post('customer-delivery-schedule/get-territory/', 'App\Http\Controllers\CustomerDeliveryScheduleController@getTerritory')->name('customer-delivery-schedule.get-territory');


    // Super Admin Routes
    Route::middleware('super-admin')->group(function () {

        // Product Tagging
        Route::get('product-tagging', 'App\Http\Controllers\ProductController@productTaggingIndex')->name('product-tagging.index');

        // Customer Tagging
        Route::get('customer-tagging', 'App\Http\Controllers\CustomerController@customerTaggingIndex')->name('customer-tagging.index');
        Route::post('customer-tagging/get-territory', 'App\Http\Controllers\CustomerController@customerTaggingGetTerritory')->name('customer-tagging.get-territory');



        // Pramotion Type
        Route::get('promotion-type/export', 'App\Http\Controllers\PromotionTypeController@export')->name('promotion-type.export');
        Route::resource('promotion-type', 'App\Http\Controllers\PromotionTypeController');
        Route::post('promotion-type/get-all', 'App\Http\Controllers\PromotionTypeController@getAll')->name('promotion-type.get-all');
        Route::post('promotion-type/status/{id}', 'App\Http\Controllers\PromotionTypeController@updateStatus')->name('promotion-type.status');
        Route::post('promotion-type/get-products/', 'App\Http\Controllers\PromotionTypeController@getProducts')->name('promotion-type.get-products');
        Route::post('promotion-type/get-brands/', 'App\Http\Controllers\PromotionTypeController@getBrands')->name('promotion-type.get-brands');
        Route::post('promotion-type/get-categories/', 'App\Http\Controllers\PromotionTypeController@getCategories')->name('promotion-type.get-categories');
        Route::post('promotion-type/get-patterns/', 'App\Http\Controllers\PromotionTypeController@getPatterns')->name('promotion-type.get-patterns');


        // Pramotions
        Route::get('promotion/export', 'App\Http\Controllers\PromotionsController@export')->name('promotion.export');
        Route::resource('promotion', 'App\Http\Controllers\PromotionsController');
        Route::post('promotion/get-all', 'App\Http\Controllers\PromotionsController@getAll')->name('promotion.get-all');
        Route::post('promotion/status/{id}', 'App\Http\Controllers\PromotionsController@updateStatus')->name('promotion.status');
        Route::post('promotion/get-customers/', 'App\Http\Controllers\PromotionsController@getCustomers')->name('promotion.getCustomers');
        Route::post('promotion/get-products/', 'App\Http\Controllers\PromotionsController@getProducts')->name('promotion.getProducts');
        Route::post('promotion/get-promotion-data', 'App\Http\Controllers\PromotionsController@getPromotionData')->name('promotion.get-promotion-data');
        Route::post('promotion/get-promotion-class-customer-data', 'App\Http\Controllers\PromotionsController@getPromotionClassCustomerData')->name('promotion.get-promotion-class-customer-data');
        Route::post('promotion/get-territories/', 'App\Http\Controllers\PromotionsController@getTerritories')->name('promotion.getTerritories');
        Route::post('promotion/get-classes/', 'App\Http\Controllers\PromotionsController@getClasses')->name('promotion.getClasses');
        Route::post('promotion/get-class-customer/', 'App\Http\Controllers\PromotionsController@getClassCustomer')->name('promotion.getClassCustomer');
        Route::post('promotion/get-sales-specialist/', 'App\Http\Controllers\PromotionsController@getSalesSpecialist')->name('promotion.getSalesSpecialist');
        Route::post('promotion/get-promotion-interest-data/', 'App\Http\Controllers\PromotionsController@getPromotionInterestData')->name('promotion.get-promotion-interest-data');
        Route::post('promotion/get-promotion-claimed-data/', 'App\Http\Controllers\PromotionsController@getPromotionClaimedData')->name('promotion.get-promotion-claimed-data');
        Route::post('promotion/get-promotion-type/', 'App\Http\Controllers\PromotionsController@getPromotionType')->name('promotion.get-promotion-type');
        Route::post('promotion/get-brands/', 'App\Http\Controllers\PromotionsController@getBrands')->name('promotion.get-brands');
        Route::post('promotion/get-market-sectors/', 'App\Http\Controllers\PromotionsController@getMarketSectors')->name('promotion.get-market-sectors');
        Route::post('promotion/check-title/', 'App\Http\Controllers\PromotionsController@checkTitle')->name('promotion.checkTitle');


        // Company
        Route::resource('sap-connection', 'App\Http\Controllers\SapConnectionController', [
            'names' => [
                'index' => 'sap-connection.index',
                'store' => 'sap-connection.store',
                'edit' => 'sap-connection.edit',
                'destroy' => 'sap-connection.destroy',
            ]
        ]);
        Route::post('sap-connection/get-all', 'App\Http\Controllers\SapConnectionController@getAll')->name('sap-connection.get-all');
        Route::get('sap-connection/test/{id}', 'App\Http\Controllers\SapConnectionController@testAPI')->name('sap-connection.test');
        Route::post('sap-connection/update-api-url', 'App\Http\Controllers\SapConnectionController@updateApiUrl')->name('sap-connection.update-api-url');

        // Sap Connection Field
        Route::resource('sap-connection-api-field', 'App\Http\Controllers\SapConnectionApiFieldController');
        Route::post('sap-connection-api-field/get-all', 'App\Http\Controllers\SapConnectionApiFieldController@getAll')->name('sap-connection-api-field.get-all');
        Route::post('sap-connection-api-field/sync-all', 'App\Http\Controllers\SapConnectionApiFieldController@syncAll')->name('sap-connection-api-field.sync-all');
        Route::post('sap-connection-api-field/sync-specific', 'App\Http\Controllers\SapConnectionApiFieldController@syncSpecific')->name('sap-connection-api-field.sync-specific');
    });


    // Report
    Route::resource('report', 'App\Http\Controllers\ReportController');
    Route::prefix('reports')->namespace('App\Http\Controllers\Reports')->name('reports.')->group(function () {

        Route::resource('sales-report', 'SalesReportController')->only('index');
        Route::post('sales-report/get-all', 'SalesReportController@getAll')->name('sales-report.get-all');
        Route::get('sales-report/export', 'SalesReportController@export')->name('sales-report.export');

        Route::resource('promotion-report', 'PromotionReportController')->only('index');
        Route::post('promotion-report/get-all', 'PromotionReportController@getAll')->name('promotion-report.get-all');
        Route::post('promotion-report/get-chart-data', 'PromotionReportController@getChartData')->name('promotion-report.get-chart-data');
        Route::get('promotion-report/export', 'PromotionReportController@export')->name('promotion-report.export');

        Route::resource('product-report', 'ProductReportController')->only('index');
        Route::post('product-report/get-all', 'ProductReportController@getAll')->name('product-report.get-all');
        Route::post('product-report/get-chart-data', 'ProductReportController@getChartData')->name('product-report.get-chart-data');
        Route::get('product-report/export', 'ProductReportController@export')->name('product-report.export');
        Route::post('customer-buying/get-chart-data', 'SalesOrderReportController@getChartData')->name('customer-buying.get-chart-data');
        Route::post('status-count-chart/get-chart-data', 'SalesOrderReportController@getStatusChartData')->name('status-count-chart.get-chart-data');
        Route::post('top-product-per-quantity-chart/get-chart-data', 'ProductReportController@getChartTopProductQuantityData')->name('top-product-per-quantity-chart.get-chart-data');
        Route::post('top-performing-graph/get-chart-data', 'ProductReportController@getChartTopPerformingData')->name('top-performing-graph.get-chart-data');

        Route::resource('sales-order-report', 'SalesOrderReportController')->only('index');
        Route::post('sales-order-report/get-all', 'SalesOrderReportController@getAll')->name('sales-order-report.get-all');

        Route::resource('overdue-sales-invoice-report', 'OverdueSalesInvoiceReportController')->only('index');
        Route::post('overdue-sales-invoice-report/get-all', 'OverdueSalesInvoiceReportController@getAll')->name('overdue-sales-invoice-report.get-all');
        Route::get('overdue-sales-invoice-report/export', 'OverdueSalesInvoiceReportController@export')->name('overdue-sales-invoice-report.export');

        Route::resource('product-sales-report', 'ProductSalesReportController')->only('index');
        Route::post('product-sales-report/get-all', 'ProductSalesReportController@getAll')->name('product-sales-report.get-all');
        Route::post('product-sales-report/get-chart-data', 'ProductSalesReportController@getChartData')->name('product-sales-report.get-chart-data');
        Route::get('product-sales-report/export', 'ProductSalesReportController@export')->name('product-sales-report.export');

        Route::resource('back-order-report', 'BackOrderReportController')->only('index');
        Route::post('back-order-report/get-all', 'BackOrderReportController@getAll')->name('back-order-report.get-all');
        Route::get('back-order-report/export', 'BackOrderReportController@export')->name('back-order-report.export');
        Route::post('back-order-report/get-chart-data', 'BackOrderReportController@getChartData')->name('back-order-report.get-chart-data');
        Route::post('back-order-report/get-product-data', 'BackOrderReportController@getProductData')->name('back-order-report.get-product-data');


        Route::resource('credit-memo-report', 'CreditMemoReportController')->only('index');
        Route::post('credit-memo-report/get-all', 'CreditMemoReportController@getAll')->name('credit-memo-report.get-all');
        Route::get('credit-memo-report/export', 'CreditMemoReportController@export')->name('credit-memo-report.export');


        Route::resource('debit-memo-report', 'DebitMemoReportController')->only('index');
        Route::post('debit-memo-report/get-all', 'DebitMemoReportController@getAll')->name('debit-memo-report.get-all');
        Route::get('debit-memo-report/export', 'DebitMemoReportController@export')->name('debit-memo-report.export');


        Route::resource('return-order-report', 'ReturnOrderReportController')->only('index');
        Route::post('return-order-report/get-all', 'ReturnOrderReportController@getAll')->name('return-order-report.get-all');
        Route::get('return-order-report/export', 'ReturnOrderReportController@export')->name('return-order-report.export');

        Route::resource('sales-order-to-invoice-lead-time-report', 'SalesOrderToInvoiceLeadTimeReportController')->only('index');
        Route::post('sales-order-to-invoice-lead-time-report/get-all', 'SalesOrderToInvoiceLeadTimeReportController@getAll')->name('sales-order-to-invoice-lead-time-report.get-all');
        Route::get('sales-order-to-invoice-lead-time-report/export', 'SalesOrderToInvoiceLeadTimeReportController@export')->name('sales-order-to-invoice-lead-time-report.export');


        Route::resource('invoice-to-delivery-lead-time-report', 'InvoiceToDeliveryLeadTimeReportController')->only('index');
        Route::post('invoice-to-delivery-lead-time-report/get-all', 'InvoiceToDeliveryLeadTimeReportController@getAll')->name('invoice-to-delivery-lead-time-report.get-all');
        Route::get('invoice-to-delivery-lead-time-report/export', 'InvoiceToDeliveryLeadTimeReportController@export')->name('invoice-to-delivery-lead-time-report.export');
    });
    Route::post('customer-tagging/get-sales-specialist', 'App\Http\Controllers\CustomerController@customerTaggingGetSalesSpecialist')->name('customer-tagging.get-sales-specialist');
    Route::post('customer-tagging/get-market-sector', 'App\Http\Controllers\CustomerController@customerTaggingGetMarketSector')->name('customer-tagging.get-market-sector');
    Route::post('customer-tagging/get-market-sub-sector', 'App\Http\Controllers\CustomerController@customerTaggingGetMarketSubSector')->name('customer-tagging.get-market-sub-sector');
    Route::post('customer-tagging/get-customer-class', 'App\Http\Controllers\CustomerController@customerTaggingGetCustomerClass')->name('customer-tagging.get-customer-class');





    Route::get('/documentation', function () {
        $file = 'OMS_DOCUMENT.pdf';
        $path = public_path('assets/files/' . $file);
        $header = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $file . '"'
        ];
        return response()->file($path, $header);
    })->name('documentation');
    // ->middleware('super-admin');
});

Route::get('clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:cache');
    Artisan::call('config:clear');
    return "Cache is cleared";
});


Route::get('/upload-ss', 'App\Http\Controllers\Userupload@index');
Route::post('/uploadfile', 'App\Http\Controllers\Userupload@showUploadFile');
