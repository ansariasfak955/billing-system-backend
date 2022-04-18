<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['namespace' => 'Api'], function() {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('forget-password', 'AuthController@forgetPassword');
    Route::post('reset-password', 'AuthController@resetPassword');
    Route::get('create-company-tables', function(){
        App\Helpers\TableHelper::createTables(10);
    });
   
    Route::group(['middleware' => ['auth:api']], function(){
        Route::apiResource('companies', CompanyController::class);
        Route::group(["prefix" => "{company_id}/"], function(){
            Route::apiResource('bank_accounts', BankAccountController::class);
            Route::apiResource('product_categories', ProductCategoryController::class);
            Route::apiResource('products', ProductController::class);
            Route::apiResource('roles', RoleController::class);
            Route::apiResource('custom_states', CustomStateController::class);
            Route::apiResource('my_templates', MyTemplateController::class);
            Route::apiResource('users', UserController::class);
            Route::post('update-profile', 'UserController@updateProfile');
            Route::apiResource('rates', RateController::class);
            Route::apiResource('services', ServiceController::class);
            Route::apiResource('expense_investments', ExpenseAndInvestmentController::class);
            Route::apiResource('expense_categories', ExpenseCategoryController::class);
            
            Route::get('custom_state_types', 'CustomStateController@getCustomStateTypes');
            Route::get('get-settings', 'SettingController@getSettings');
            Route::get('permissions', 'RoleController@getPermissions');
            Route::post('update-settings', 'SettingController@updateSettings');

            /* client routes */
            Route::apiResource('clients', ClientController::class);
            Route::apiResource('client-contacts', ClientContactController::class);
            Route::apiResource('client-special-prices', ClientSpecialPriceController::class);
            Route::apiResource('client-assets', ClientAssetController::class);
            Route::apiResource('client-attachments', ClientAttachmentController::class);
            Route::apiResource('client-addresses', ClientAddressController::class);

            /* Sales Routes */
            Route::apiResource('sales-estimates', SalesEstimateController::class);
            Route::apiResource('sales-attachments', SalesAttachmentController::class);

            /* Technical Service */
            Route::apiResource('technical-incidents', TechnicalIncidentController::class);
            Route::apiResource('technical-incident-attachments', TechnicalIncidentAttachmentController::class);

            /* Items */
            Route::apiResource('items', ItemController::class);

            /* Subscriptions */
            Route::apiResource('subscriptions', SubscriptionController::class);
        });
    });
});