<?php

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

Route::get('test_email', function(){ Mail::raw('This is a Mailgun test email 1 ...2', function($message) { $message->to('jettore@eyeforsecurity.com'); }); });

// Auth
Route::get('/signup', 'AuthController@signupForm');
Route::post('/signup', 'AuthController@signup');

Route::get( '/login', ['as'=>'login', 'uses' => 'AuthController@loginForm']);
Route::post('/login', 'AuthController@login');


//Unprotected mobile app routes
Route::post('cssi/login', 'AuthController@appLogin');
Route::post('cssi/signup', 'AuthController@appSignup');

Route::get('/password', 'AuthController@passwordForm');
Route::post('/password', 'AuthController@password');
Route::get('/password/reset', 'AuthController@passwordResetForm');
Route::post('/password/reset', 'AuthController@passwordReset');
Route::get('logout', ['as'=>'logout', 'uses' => 'AuthController@logout']);

// Documentation
Route::get('documentation', 'DocumentationController@index');

Route::get('/company/screen/start', 'CompanyController@start');
Route::get('/company/screen/view', 'CompanyController@viewScreen');


//Updating terms of service.
Route::group(['middleware' => 'auth'], function(){
	Route::get('/tou', function(){ return view('settings.tou_wrap'); });
	Route::get('/tou/notice/show', function(){ return view('settings.tou_notify'); });
	Route::get('/tou/{id}', 'SettingsController@addTouAccept');
});


// Authenticated Routes
Route::group(['middleware' => ['auth', 'tou.accepted'] ], function () {
	
	// Settings
	Route::get('settings', 'SettingsController@index');

	// Settings - Account
    Route::get('settings/account', 'SettingsController@account');
	Route::post('settings/ach', 'SettingsController@achRegister');
	
    Route::match(['get', 'post'], 'settings/contact', 'SettingsController@contact');
    Route::match(['get', 'post'], 'settings/password', 'SettingsController@password');

    // Settings - API
    Route::get('settings/api', 'SettingsController@viewApi');

    // Settings - Billing
    Route::match(['get', 'post'], 'settings/billing', 'SettingsController@billing');
	Route::post('settings/removePaymentInfo', 'SettingsController@removePaymentInfo');

    Route::get('settings/awaiting_approval', 'SettingsController@awaitingApproval');
	
	
    // Account
    Route::get('sidebar', 'AccountController@sidebar');
    Route::get('undisguise', 'AccountController@undisguise');
    Route::get('restore', 'AccountController@restore');

    // Forms
    Route::get('forms', 'FormController@index');
	
	Route::get('users/company', 'Admin\UserController@company');
	Route::post('users/create/contact', 'Admin\UserController@createContact');
	Route::post('company/employee/screen', 'CompanyController@screen');
	
	
	// Company
	Route::get('company/report', 'ReportController@companyReport');
	

    // After Setup Routes
    Route::group(['middleware' => ['setup.contact', 'setup.payment', 'setup.approved']], function () {

        Route::get('/', function() {
            return redirect(secure_url('reports'));
        });

        // Transactions
        Route::get('transactions', 'TransactionController@index');

        // Reports
        Route::get('reports', 'ReportController@index');
		Route::get('reports/limitedAdmin/companyReport', 'ReportController@limitedAdminCompanyReport');
		
        
        // Invoices
        Route::get('invoices', 'InvoiceController@index');
		Route::get('invoices/stream/pdf', 'InvoiceController@streamInvoice');
		
		//InvoiceController@read
		
        //Route::get('invoices/{id}/{format?}', 'InvoiceController@show');
		Route::get('invoices/{id}', 'InvoiceController@read');
		
        //Checks
        Route::get('checks', 'CheckController@index');
		
        Route::get('checks/create', 'CheckController@create');
        Route::get('checks/input', 'CheckController@input');
        Route::get('checks/profile', 'CheckController@profile');
		Route::get('checks/print/{id}', 'CheckController@printable');
		
		
        Route::get('checks/{id}/{view?}', 'CheckController@show');
		
		Route::post('checks', 'CheckController@store');
		
		//Imports
		Route::post('import', 'CheckController@import');
		
		Route::get('company/users/{id}/approve', 'CompanyController@approve');
        Route::get('company/users/{id}/disapprove', 'CompanyController@disapprove');

    });
	
	

    /* ADMIN */
	Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
    	
        // Sandbox
        Route::get('sandbox', 'Admin\SandboxController@index');

        // Reports
        Route::get('reports', 'Admin\ReportController@index');
		Route::get('reports/show', 'Admin\ReportController@show');
		Route::post('reports/update', 'Admin\ReportController@update');
		
		Route::get('reports/checksForDay', 'Admin\ReportController@checksForDay');
		
		Route::get('reports/checksForMonth', 'Admin\ReportController@checksForMonth');
		Route::get('reports/checksForPriorMonth', 'Admin\ReportController@checksForPriorMonth');
		Route::get('reports/checksForYtd', 'Admin\ReportController@checksForYtd');
		Route::get('reports/rawtotals', 'Admin\ReportController@rawtotals');
		

        // Users
        Route::get('users/{id}/disguise', 'Admin\UserController@disguise');
        Route::get('users/{id}/approve', 'Admin\UserController@approve');
        Route::get('users/{id}/disapprove', 'Admin\UserController@disapprove');
        Route::get('users/{id}/promote', 'Admin\UserController@promote');
        Route::get('users/{id}/demote', 'Admin\UserController@demote');
        Route::get('users/{id}/delete', 'Admin\UserController@delete');
		Route::get('users/{id}/apiuser', 'Admin\UserController@apiuser');
        Route::get('users/{id}/notapiuser', 'Admin\UserController@notapiuser');
		
		Route::get('users/{id}/authorizedrep', 'Admin\UserController@authorizedrep');
        Route::get('users/{id}/notauthorizedrep', 'Admin\UserController@notauthorizedrep');
		
		Route::get('users/{id}/limitedadmin', 'Admin\UserController@limitedadmin');
        Route::get('users/{id}/notlimitedadmin', 'Admin\UserController@notlimitedadmin');
		
        Route::get('users/{id}', 'Admin\UserController@show');
		
        Route::get('users', 'Admin\UserController@index');
		
		//Companies - 
		Route::get('company', 'CompanyController@index');
		Route::put('company/_update', 'CompanyController@_update');
		Route::put('company/prices/_update', 'CompanyController@_updatePrices');
		Route::get('company/_totals', 'CompanyController@_totals');
		
		//Route::get('companies', function(){
			//return json_encode(["Returning from routes."]);
		//});
		
		Route::get('companies', 'CompanyController@index');

        // Checks
        Route::match(['get', 'post'], 'checks', 'Admin\CheckController@index');
        Route::match(['get', 'post'], 'checks/{id}/employment', 'Admin\CheckController@employment');
        Route::match(['get', 'post'], 'checks/{id}/education', 'Admin\CheckController@education');
        Route::match(['get', 'post'], 'checks/{id}/mvr', 'Admin\CheckController@mvr');
        Route::get('checks/{id}/complete', 'Admin\CheckController@complete');
        Route::get('checks/{id}/incomplete', 'Admin\CheckController@incomplete');
        Route::get('checks/{id}/delete', 'Admin\CheckController@delete');
        Route::get('checks/{check_id}/redo/{type_id}', 'Admin\CheckController@redo');
		
		
        Route::get('checks/{id}/{view?}', 'Admin\CheckController@show');
		
		
		// Settings
		Route::get('settings/configs', '\App\Http\Controllers\Api\V1\SettingsController@configs');
		Route::post('settings/configs', '\App\Http\Controllers\Api\V1\SettingsController@configs');
		
		// Transactions
		Route::post('transactions/update', '\App\Http\Controllers\Api\V1\TransactionController@update');
		
		Route::get('transactions/edit', function(){
			return view('transactions/edit');
		});
		
		Route::get("types/toggle", "TypeController@toggle");		
		Route::get('types', "TypeController@index");
		
		Route::get('transactions/{id}', '\App\Http\Controllers\Api\V1\TransactionController@adminShow');
		
		Route::get('invoices/pdf', function(){
					return view('invoices/list');
				});
				
		Route::get('invoices/manage', function(){
					return view('admin/invoices/index');
				});
				
		Route::get('invoices/regenerate', '\App\Http\Controllers\Api\V1\InvoiceController@regenerate');
				
		Route::get('invoices/i/{id}', 'InvoiceController@read');
		

        /*
        // Diagnostics
        Route::post('diagnostics/text', 'Admin\DiagnosticController@text');
        Route::post('diagnostics/email', 'Admin\DiagnosticController@email');
        Route::get('diagnostics/exception', 'Admin\DiagnosticController@exception');
        Route::get('diagnostics', 'Admin\DiagnosticController@index');
		 * * 
		 *
		 */

        // Utilities
        Route::get('utilities', 'Admin\UtilityController@index');
		
		/*
        Route::get('utilities/kint', 'Admin\UtilityController@kint');
        Route::get('utilities/session', 'Admin\UtilityController@session');
        Route::get('utilities/cache', 'Admin\UtilityController@cache');

        Route::get('utilities/emails/{template?}', 'Admin\UtilityController@emails');
		*/
		
		//Pricing
		Route::get('pricing', 'Admin\PricingController@index');
    });

});

