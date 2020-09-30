<?php

use Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//ApiUser



$api = app('Dingo\Api\Routing\Router');


$api->version('v1', function ($api) {
	
	$api->group(['middleware' => 'logger'], function ($api) {
		
		//$api->get('api/cssi/checks/{id}/{view?}', '\App\Http\Controllers\CheckController@show');
		
		//could prefix this
		$api->group(['middleware' => 'apiuser'], function ($api) {
			
			
			
			//$api->post('cssi/checks/testData', 'App\Http\Controllers\Api\V1\CheckController@testData');
			
			
			//Don't panic. These routes are for cssi data routes only.
			$api->group(['middleware' => 'isEnabled'], function ($api) {
					
				//These routes are in the wrong place.
				$api->post('cssi/checks/', 'App\Http\Controllers\Api\V1\CheckController@store');  
				$api->post('cssi/data', 'App\Http\Controllers\Api\V1\CssiDataController@data');
				
			});

			$api->post('cssi/checks/retrieve', 'App\Http\Controllers\Api\V1\CheckController@retrieve');
			$api->post('cssi/btob', 'App\Http\Controllers\Api\V1\CheckController@btob');
			
			$api->post('cssi/data/b2bView', 'App\Http\Controllers\Api\V1\CssiDataController@b2bView');
			
			//Neeyamo related
		    $api->post('cssi/ney/insufficiency', '\App\Http\Controllers\Api\V1\NeeyamoController@insufficiency');
			$api->post('cssi/ney/status', '\App\Http\Controllers\Api\V1\NeeyamoController@status');
			$api->post('cssi/ney/result', '\App\Http\Controllers\Api\V1\NeeyamoController@result');
			
			//Mobile app related
			$api->post('cssi/contact', '\App\Http\Controllers\SettingsController@appContact');
			$api->post('cssi/billing', '\App\Http\Controllers\SettingsController@appBilling');
			$api->get('cssi/billing', '\App\Http\Controllers\SettingsController@appBilling');
			$api->post('cssi/capture', '\App\Http\Controllers\Api\V1\CssiDataController@capture');
				
		});
		
		//$api->group(['middleware' => ['apiuser', 'setup.approved'] ], function ($api) {
			
			//$api->post('cssi/contact', '\App\Http\Controllers\SettingsController@appContact');
			//$api->post('cssi/billing', '\App\Http\Controllers\SettingsController@appBilling');
			
		//});
		
		// Auth
	    $api->post('auth/password/reset', 'App\Http\Controllers\Api\V1\AuthController@reset');
	    $api->post('auth/password', 'App\Http\Controllers\Api\V1\AuthController@password');
		$api->post('auth/signup', 'App\Http\Controllers\Api\V1\AuthController@signup');
	    $api->post('auth', 'App\Http\Controllers\Api\V1\AuthController@authenticate');

	    $api->group(['middleware' => 'api.auth'], function ($api) {

            //All of these routes should be in the admin section below ---
			$api->post('pricing/state', "\App\Http\Controllers\Api\V1\PricingController@putStatePrice");
			$api->post('pricing/state/extra', "\App\Http\Controllers\Api\V1\PricingController@putStateExtra");
			$api->post('pricing/user', "\App\Http\Controllers\Api\V1\PricingController@putUserPrice");
			$api->post('pricing/base', "\App\Http\Controllers\Api\V1\PricingController@putBasePrice");
			$api->post('pricing/county/extra', "\App\Http\Controllers\Api\V1\PricingController@putCountyExtra");
			$api->post('pricing/adjustment', "\App\Http\Controllers\Api\V1\PricingController@adjustment");
			$api->post('pricing/minimum', "\App\Http\Controllers\Api\V1\PricingController@minimum");
			
	    	// Account
	    	$api->get('account/restore', 'App\Http\Controllers\Api\V1\AccountController@restore');

	    	// Settings
			$api->post('settings/contact', 'App\Http\Controllers\Api\V1\SettingsController@contact');
			$api->post('settings/password', 'App\Http\Controllers\Api\V1\SettingsController@password');
			$api->post('settings/billing', 'App\Http\Controllers\Api\V1\SettingsController@billing');
			$api->post('settings/appBilling', 'App\Http\Controllers\Api\V1\SettingsController@appBilling');
			$api->post('settings/ach', 'App\Http\Controllers\Api\V1\SettingsController@achRegister');
			$api->post('settings/limitedAdmin', 'App\Http\Controllers\Api\V1\SettingsController@limitedAdmin');
			$api->post('settings/removePaymentInfo', 'App\Http\Controllers\Api\V1\SettingsController@removePaymentInfo');
			
			$api->post('sandbox/enable', "\App\Http\Controllers\Api\V1\SettingsController@sandboxEnable");
			$api->post('sandbox/disable', "\App\Http\Controllers\Api\V1\SettingsController@sandboxDisable");
			
			$api->get('company/users/{id}/approve', 'App\Http\Controllers\Api\V1\CompanyController@approve');
			$api->get('company/users/{id}/disapprove', 'App\Http\Controllers\Api\V1\CompanyController@disapprove');
			
			// Company
		    $api->get('company/report', '\App\Http\Controllers\Api\V1\CompanyReportController@show');
			$api->get('report/limitedAdminCompanyReport', '\App\Http\Controllers\Api\V1\CompanyReportController@limitedAdminCompanyReport');
			
			$api->get('checks/{id}', 'App\Http\Controllers\Api\V1\CheckController@show');
			

			// After Setup Routes
			$api->group(['middleware' => ['setup.payment', 'setup.contact', 'setup.approved']], function ($api) {

				// Transactions
				$api->get('transactions/balance', 'App\Http\Controllers\Api\V1\TransactionController@balance');
				$api->get('transactions/{id}', 'App\Http\Controllers\Api\V1\TransactionController@show');
				$api->get('transactions', 'App\Http\Controllers\Api\V1\TransactionController@index');

				// Invoices
				$api->get('invoices/{id}', 'App\Http\Controllers\Api\V1\InvoiceController@show');
				$api->get('invoices', 'App\Http\Controllers\Api\V1\InvoiceController@index');
				$api->get('invoices/export/all', 'App\Http\Controllers\Api\V1\InvoiceController@export');
				$api->get('invoices/charge/{id}', 'App\Http\Controllers\Api\V1\InvoiceController@charge');

				// Logs
				$api->get('logs/{id}', 'App\Http\Controllers\Api\V1\LogController@show');
				$api->get('logs', 'App\Http\Controllers\Api\V1\LogController@index');

		    	// Checks
		    	$api->get('checks', 'App\Http\Controllers\Api\V1\CheckController@index');
				
				$api->group(['middleware' => 'isEnabled'], function ($api) {
		    		$api->post('checks', 'App\Http\Controllers\Api\V1\CheckController@store');
		    	});
				
				
		    	$api->get('checks/{id}', 'App\Http\Controllers\Api\V1\CheckController@show');

		    	// Counties
		    	$api->get('counties', 'App\Http\Controllers\Api\V1\CountyController@index');
				
				$api->get('users/company/{id}', 'App\Http\Controllers\Api\V1\Admin\UserController@company');
				$api->get('users/company/export/all', 'App\Http\Controllers\Api\V1\Admin\UserController@companyExport');
				$api->get('users/company/export/users', 'App\Http\Controllers\Api\V1\Admin\UserController@usersExport');
				$api->get('users/company/export/{id}', 'App\Http\Controllers\Api\V1\Admin\UserController@export');
				$api->put('users/company/id/update', 'App\Http\Controllers\Api\V1\Admin\UserController@update');
				
				$api->post('company/screen', '\App\Http\Controllers\Api\V1\CompanyController@screen');
				$api->get('company/screen/view', '\App\Http\Controllers\Api\V1\CompanyController@viewScreen');
				
				$api->post('import', 'App\Http\Controllers\Api\V1\CheckController@import');

		    });



			// Admin
			$api->group(['prefix' => 'admin', 'middleware' => 'admin'], function ($api) {
				
				//Companies - 
				$api->get('company', '\App\Http\Controllers\Api\V1\CompanyController@index');
				$api->put('company/_update', '\App\Http\Controllers\Api\V1\CompanyController@_update');
				$api->put('company/prices/_update', '\App\Http\Controllers\Api\V1\CompanyController@_updatePrices');
				$api->put('company/_totals', '\App\Http\Controllers\Api\V1\CompanyController@_totals');
				
				$api->get('companies', '\App\Http\Controllers\Api\V1\CompanyController@index');

				// Reports
				$api->get('reports', 'App\Http\Controllers\Api\V1\Admin\ReportController@index');
				
				$api->get('reports/checksForDay', 'App\Http\Controllers\Api\V1\Admin\ReportController@checksForDay');
				$api->get('reports/checksForMonth', 'App\Http\Controllers\Api\V1\Admin\ReportController@checksForMonth');
				$api->get('reports/checksForPriorMonth', 'App\Http\Controllers\Api\V1\Admin\ReportController@checksForPriorMonth');
				$api->get('reports/checksForYtd', 'App\Http\Controllers\Api\V1\Admin\ReportController@checksForYtd');
				$api->get('reports/custom', 'App\Http\Controllers\Api\V1\Admin\ReportController@createCustomTransactionsReport');
				
				$api->get('reports/rawtotals', 'App\Http\Controllers\Api\V1\Admin\ReportController@rawtotals');
				
				
				// Users
				$api->get('users/{id}/approve', 'App\Http\Controllers\Api\V1\Admin\UserController@approve');
				$api->get('users/{id}/disapprove', 'App\Http\Controllers\Api\V1\Admin\UserController@disapprove');
				$api->get('users/{id}/promote', 'App\Http\Controllers\Api\V1\Admin\UserController@promote');
				$api->get('users/{id}/demote', 'App\Http\Controllers\Api\V1\Admin\UserController@demote');
				$api->get('users/{id}/delete', 'App\Http\Controllers\Api\V1\Admin\UserController@delete');
				$api->get('users/{id}/apiuser', 'App\Http\Controllers\Api\V1\Admin\UserController@apiuser');
        		$api->get('users/{id}/notapiuser', 'App\Http\Controllers\Api\V1\Admin\UserController@notapiuser');
				$api->get('users/{id}/limitedadmin', 'App\Http\Controllers\Api\V1\Admin\UserController@limitedadmin');
        		$api->get('users/{id}/notlimitedadmin', 'App\Http\Controllers\Api\V1\Admin\UserController@notlimitedadmin');
				$api->get('users/{id}/authorizedrep', 'App\Http\Controllers\Api\V1\Admin\UserController@authorizedrep');
        		$api->get('users/{id}/notauthorizedrep', 'App\Http\Controllers\Api\V1\Admin\UserController@notauthorizedrep');
				$api->get('users/{id}', 'App\Http\Controllers\Api\V1\Admin\UserController@show');
				
				$api->get('users', 'App\Http\Controllers\Api\V1\Admin\UserController@index');
				
				// Checks
				$api->match(['get', 'post'], 'checks', 'App\Http\Controllers\Api\V1\Admin\CheckController@index');
				$api->post('checks/{id}/employment', 'App\Http\Controllers\Api\V1\Admin\CheckController@employment');
				$api->post('checks/{id}/education', 'App\Http\Controllers\Api\V1\Admin\CheckController@education');
				$api->post('checks/{id}/mvr', 'App\Http\Controllers\Api\V1\Admin\CheckController@mvr');
				$api->get('checks/{id}/complete', 'App\Http\Controllers\Api\V1\Admin\CheckController@complete');
	    		$api->get('checks/{id}/incomplete', 'App\Http\Controllers\Api\V1\Admin\CheckController@incomplete');
	    		$api->get('checks/{id}/delete', 'App\Http\Controllers\Api\V1\Admin\CheckController@delete');
	    		$api->get('checks/{check_id}/redo/{type_id}', 'App\Http\Controllers\Api\V1\Admin\CheckController@redo');
	    		$api->get('checks/{id}', 'App\Http\Controllers\Api\V1\Admin\CheckController@show');
				
				//Types
				$api->get("types/toggle", "App\Http\Controllers\Api\V1\TypeController@toggle");
				$api->get("types", "App\Http\Controllers\Api\V1\TypeController@index");
				
								
				$api->post('settings/updateInvoiceAmount', 'App\Http\Controllers\Api\V1\InvoiceController@updateInvoiceAmount');
				$api->post('settings/updateAdjustmentAmount', 'App\Http\Controllers\Api\V1\InvoiceController@updateAdjustmentAmount');
				$api->post('settings/updateMinimumAmount', 'App\Http\Controllers\Api\V1\InvoiceController@updateMinimumAmount');
				
				$api->post('invoices/reconcile/', 'App\Http\Controllers\Api\V1\InvoiceController@reconcile');
				$api->get('invoices/i/{id}/', 'App\Http\Controllers\Api\V1\InvoiceController@read');
				
				

			});

		});

	});
});