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
   
    Route::group(['middleware' => ['auth:api']], function(){
        Route::apiResource('companies', CompanyController::class);
        Route::group(["prefix" => "{company_id}/"], function(){
            Route::apiResource('bank_accounts', BankAccountController::class);
            Route::apiResource('product_categories', ProductCategoryController::class);
            Route::apiResource('products', ProductController::class);
        });
    });
});
