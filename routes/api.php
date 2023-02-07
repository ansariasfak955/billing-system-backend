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

Route::get('test-pdf', function(){
    $pdf = App::make('dompdf.wrapper');
    $html = '
        <body>
            <div style="position:relative;">
                <img src="watermark.png" alt="" style="position: absolute; z-index: -1; opacity: 0.3; top:50%; left: 50%; transform: translate(-50%); width: 600px">
                <div style="margin-top: 20px;">
                    <table style="width:100%">
                        <tr>
                            <td>
                                <img src="logoinimg.png" alt="" srcset="" style="width: 340px; height: 120px; object-fit: cover;">
                            </td>
                            <td style="border-left: 2px solid orange;">
                                <span style="margin-left: 15px;">Company Demo</span> <br>
                                <span style="margin-left: 15px;">India</span>
                            </td>
                            <td style="border-left: 2px solid orange;">
                                <span style="margin-left: 15px;">email</span> <br>
                                <span style="margin-left: 15px;">website</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <h2>DOCUMENT TYPE</h2>
                    <span>You can insert a title here</span>
                </div>
                <div style="margin-top: 20px;font-size: 13px">
                    <table style="width:50%; float: right; padding: 10px">
                        <th style="color: orange; border-bottom: 1px solid gray;text-align: left">CLIENT INFO</th>
                        <tr><td>Number: 34234 <b>INV00006</b></td></tr>
                        <tr><td>Date: <b>24 May 2022 01/12/2015</b></td></tr>
                        <tr><td>Payment Option: <b>Online Bank Transfer</b></td></tr>
                        <tr><td>Status: <b>Test Pending</b></td></tr>
                        <tr><td>Created by: <b>Test View Account</b></td></tr>
                        <tr><td>Delivery to: <b>HongKong 9205 Olive Ave., 10977, Spring Valley, NY, United States</b></td></tr>
                        <tr><td>Delivery Option: <b>test delivery A domicilio</b></td></tr>
                        <tr><td>Agent: <b>Test View Account</b></td></tr>
                    </table>
                    <table style="width:50%;border-right: 2px solid orange; padding: 10px">
                        <th style="color: orange; border-bottom: 1px solid gray;text-align: left">PURCHASE DELIVERY NOTE INFO</th>
                        <tr><td>Number: 34234 <b>INV00006</b></td></tr>
                        <tr><td>Date: <b>24 May 2022 01/12/2015</b></td></tr>
                        <tr><td>Payment Option: <b>Online Bank Transfer</b></td></tr>
                        <tr><td>Status: <b>Test Pending</b></td></tr>
                        <tr><td>Created by: <b>Test View Account</b></td></tr>
                        <tr><td>Delivery to: <b>HongKong 9205 Olive Ave., 10977, Spring Valley, NY, United States</b></td></tr>
                        <tr><td>Delivery Option: <b>test delivery A domicilio</b></td></tr>
                        <tr><td>Agent: <b>Test View Account</b></td></tr>
                    </table>
                </div>
                <div style="margin-top: 20px;">
                    <table style="width:100%">
                        <tr style="color: orange; border-bottom: 1px solid gray;">
                            <th style="color: orange;">REF.</th>
                            <th style="color: orange;">NAME</th>
                            <th style="color: orange;">PRICE</th>
                            <th style="color: orange;">DISC.</th>
                            <th style="color: orange;">QTY.</th>
                            <th style="color: orange;">SUBTOTAL</th>
                            <th style="color: orange;">TAXES</th>
                        </tr>
                        <tr style="border-bottom: 1px solid gray;">
                            <td>
                                <p>PRO00012</p>
                                <img src="refimg.png" alt="" srcset="">
                            </td>
                            <td>
                                <p>Heading</p>
                                <span>this is description </span>
                            </td>
                            <td>
                                <p>29.00</p>
                            </td>
                            <td>
                                <p>0.00%</p>
                            </td>
                            <td>
                                <p>5.00</p>
                            </td>
                            <td>
                                <p>145.00</p>
                            </td>
                            <td>
                                <p>VAT 21.00%</p>
                            </td>

                        </tr>
                    </table>
                </div>

                <div>
                    <p style="font-weight: bold;">Signed:</p>
                </div>
                <div>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <div style="border: 1px solid gray">
                                    <img width="210" height="100" object-fit="cover"
                                        src="https://camo.githubusercontent.com/fcd5a5ab2be5419d00fcb803f14c55652cf60696d7f6d9828b99c1783d9f14a3/68747470733a2f2f662e636c6f75642e6769746875622e636f6d2f6173736574732f393837332f3236383034362f39636564333435342d386566632d313165322d383136652d6139623137306135313030342e706e67" />
                                    <p style="font-weight: bold; position: relative; bottom: 0;">Name:</p>
                                    <p style="font-weight: bold; position: relative; bottom: 0;">TIN:</p>
                                </div>
                            </td>
                            <td>
                                <div style="padding-left:150px">
                                    <table style="border-bottom: 1px solid gray;">
                                        <tr style="border-bottom: 1px solid gray;">
                                            <th style="color: orange;">BASE</th>
                                            <th></th>
                                            <th style="color: orange; padding-left: 30px;">$ 1,386.80</th>
                                        </tr>
                                        <tr style="border-bottom: 1px solid gray;">
                                            <td>1,386.80</td>
                                            <td style="padding-left: 30px; text-align: right">VAT 21.00%</td>
                                            <td style="padding-left: 30px; text-align: right">291.23</td>
                                        </tr>
                                        <tr>
                                            <th style="color: orange;">TOTAL</th>
                                            <td></td>
                                            <th style="padding-left: 30px">$ 1,678.03</th>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div style="margin-top: 20px;">
                    <h5 style="border-bottom: 1px solid black ;">ADDENDUM</h5>
                    <ul>
                        <li>
                            You can also type in more detailed comments which will be included as an Addendum at the bottom of
                            your
                            documents.
                        </li>
                        <li>
                            You can use this to include contracts, conditions, promotions and legal writings.
                        </li>
                        <li>
                            From Settings > Management Listings > References, you can define Addendums that you want to add into
                            your estimates,
                            orders, invoices and any other commercial document reference.
                            2 / 2
                        </li>
                    </ul>
                </div>
            </div>
        </body>
    ';

    $pdf->loadHTML($html);
    return $pdf->stream();
});

Route::group(['namespace' => 'Api'], function() {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('forget-password', 'AuthController@forgetPassword');
    Route::post('reset-password', 'AuthController@resetPassword');
    Route::get('create-company-tables', function(){
        App\Helpers\TableHelper::createTables(95);
    });

    Route::get('subscriptions', 'SubscriptionController@index');
    Route::get('{company_id}/template-preview/{template_id}', 'MyTemplateController@getTemplatePreview');
   
    
    Route::group(['middleware' => ['auth:api']], function(){
        Route::apiResource('companies', CompanyController::class);
        Route::post('update-profile', 'UserController@updateProfile');
        Route::group(["prefix" => "{company_id}/"], function(){
            Route::get('dashboard-counts', 'DashboardController@index');
            Route::apiResource('bank_accounts', BankAccountController::class);
            Route::apiResource('product_categories', ProductCategoryController::class);
            Route::apiResource('products', ProductController::class);
            Route::post('products/batch-delete', 'ProductController@batchDelete');
            Route::post('products/duplicate-product', 'ProductController@duplicate');
            Route::apiResource('product-stock', ProductStockController::class);
            Route::apiResource('product-rates', ProductRateController::class);
            Route::apiResource('product-attachments', ProductAttachmentController::class);
            Route::apiResource('roles', RoleController::class);
            Route::get('get-master-role-permissions', 'RoleController@getMasterRolePermissions');
            Route::apiResource('custom_states', CustomStateController::class);
            Route::apiResource('my_templates', MyTemplateController::class);
            Route::post('my_templates-bulk-delete', 'MyTemplateController@bulkDelete');
            Route::get('my_templates-duplicate/{id}', 'MyTemplateController@duplicateTemplate');
            Route::get('template-fields', 'MyTemplateController@getTemplateFields');
            Route::post('update-template-field', 'MyTemplateController@updateTemplateField');
            Route::apiResource('users', UserController::class);
            Route::apiResource('references', ReferenceController::class);
            Route::post('references/batch-delete', 'ReferenceController@batchDelete');
            Route::apiResource('service-attachments', ServiceAttachmentController::class);
            Route::apiResource('service-special-prices', ServiceSpecialPriceController::class);
            
            Route::apiResource('rates', RateController::class);
            Route::apiResource('services', ServiceController::class);
            Route::post('services/batch-delete', 'ServiceController@batchDelete');
            Route::post('services/duplicate-service', 'ServiceController@duplicate');
            Route::apiResource('service-rates', ServiceRateController::class);
            Route::apiResource('expense_investments', ExpenseAndInvestmentController::class);
            Route::post('expense_investments/batch-delete', 'ExpenseAndInvestmentController@batchDelete');
            Route::post('expense_investments/duplicate-investment', 'ExpenseAndInvestmentController@duplicate');
            Route::apiResource('expense-attachments', ExpenseAttachmentController::class);
            Route::apiResource('expense_categories', ExpenseCategoryController::class);
            Route::post('expense_categories/batch-delete', 'ExpenseCategoryController@batchDelete');
            
            Route::get('custom_state_types', 'CustomStateController@getCustomStateTypes');
            Route::get('get-settings', 'SettingController@getSettings');
            Route::get('permissions', 'RoleController@getPermissions');
            Route::post('duplicate-role', 'RoleController@duplicateRole');
            Route::get('role-permissions', 'RoleController@getRolePermissions');
            Route::post('update-settings', 'SettingController@updateSettings');
            Route::get('send-test-email', 'SettingController@sendTestEmail');

            /* client routes */
            Route::apiResource('clients', ClientController::class);
            Route::post('clients/batch-delete', 'ClientController@batchDelete');
            Route::post('clients/duplicate-client', 'ClientController@duplicate');
            Route::post('clients/client-export', 'ClientController@clientExport');
            Route::post('clients/export-receipt', 'ClientController@exportReceipt');
            Route::get('client-balance', 'ClientController@clientBalance');
            Route::get('unpaid-invoices', 'ClientController@unpaidInvoices');
            Route::get('unpaid-refund', 'ClientController@unpaidRefund');
            Route::apiResource('client-contacts', ClientContactController::class);
            Route::apiResource('client-special-prices', ClientSpecialPriceController::class);
            Route::apiResource('client-assets', ClientAssetController::class);
            Route::post('client-assets/batch-delete', 'ClientAssetController@batchDelete');
            Route::post('client-assets/duplicate-asset', 'ClientAssetController@duplicate');
            Route::apiResource('client-asset-attachments', ClientAssetAttachmentController::class);
            Route::apiResource('client-attachments', ClientAttachmentController::class);
            Route::apiResource('client-addresses', ClientAddressController::class);
            Route::apiResource('client-categories', ClientCategoryController::class);
            Route::post('client-categories/batch-delete', 'ClientCategoryController@batchDelete');
            Route::apiResource('client-rates', ClientRateController::class);

            /* Sales Routes */
            Route::apiResource('sales-estimates', SalesEstimateController::class);
            Route::post('sales-estimates/batch-delete', 'SalesEstimateController@batchDelete');
            Route::post('sales-estimates/duplicate_sales', 'SalesEstimateController@duplicate');
            Route::post('sales-estimates-export', 'SalesEstimateController@salesEstimateExport');
            Route::apiResource('sales-attachments', SalesAttachmentController::class);

            /* Technical Service */
            Route::apiResource('technical-incidents', TechnicalIncidentController::class);
            Route::post('technical-incidents/batch-delete', 'TechnicalIncidentController@batchDelete');
            Route::post('technical-incidents/duplicate_incident', 'TechnicalIncidentController@duplicate');
            Route::post('technical-incidents-export', 'TechnicalIncidentController@technicalIncidentExport');
            Route::apiResource('technical-incident-attachments', TechnicalIncidentAttachmentController::class);
            Route::apiResource('technical-tables', TechnicalTableController::class);
            Route::post('technical-tables/batch-delete', 'TechnicalTableController@batchDelete');
            Route::post('technical-tables/duplicate_service', 'TechnicalTableController@duplicate');
            Route::post('technical-tables/estimate-export', 'TechnicalTableController@technicalEstimateExport');
            Route::apiResource('technical-table-attachments', TechnicalTableAttachmentController::class);

            // Invoicing 
            Route::apiResource('invoices', InvoiceTableController::class);
            Route::post('invoices/batch-delete', 'InvoiceTableController@batchDelete');
            Route::get('sent-invoice-summary', 'InvoiceTableController@sentInvoices');
            Route::get('send-invoice-mail', 'InvoiceTableController@sendInvoiceMail');
            Route::post('invoices/duplicate_invoice', 'InvoiceTableController@duplicate');
            Route::apiResource('invoice-attachments', InvoiceAttachmentController::class);
            Route::apiResource('invoice-receipts', InvoiceReceiptController::class);
            Route::post('invoice-receipts-bulk-pay', 'InvoiceReceiptController@bulkPay');
            Route::post('invoice-receipts-download', 'InvoiceReceiptController@download');
            Route::post('invoice-receipts-export', 'InvoiceReceiptController@receiptExport');
            Route::post('create-sub-receipt', 'InvoiceReceiptController@createSubReceipt');

            // Purchases 
            Route::apiResource('suppliers', SupplierController::class);
            Route::post('suppliers/batch-delete', 'SupplierController@batchDelete');
            Route::post('suppliers/duplicate-supplier', 'SupplierController@duplicate');
            Route::post('suppliers-export', 'SupplierController@supplierExport');
            Route::get('supplier-balance', 'SupplierController@supplierBalance');
            Route::apiResource('purchase-attachments', PurchaseAttachmentController::class);
            Route::apiResource('supplier-addresses', SupplierAddressController::class);
            Route::apiResource('supplier-special-prices', SupplierSpecialPriceController::class);
            Route::apiResource('supplier-contacts', SupplierContactController::class);
            Route::apiResource('supplier-attachments', SupplierAttachmentController::class);
            Route::apiResource('purchase-tables', PurchaseTableController::class);
            Route::post('purchase-tables/batch-delete', 'PurchaseTableController@batchDelete');
            Route::post('purchase-tables/duplicate_supplier', 'PurchaseTableController@Duplicate');
            Route::post('purchase-tables-export', 'PurchaseTableController@purchaseOrderExport');
            Route::apiResource('purchase-receipts', PurchaseReceiptController::class);
            Route::apiResource('purchase-tickets', PurchaseTicketController::class);
            Route::post('purchase-tickets/batch-delete', 'PurchaseTicketController@batchDelete');
            Route::post('purchase-tickets/duplicate-tickets', 'PurchaseTicketController@duplicate');
            Route::post('purchase-receipts-bulk-pay', 'PurchaseReceiptController@bulkPay');
            Route::post('purchase-receipts-download', 'PurchaseReceiptController@download');
            Route::post('purchase-receipts-export', 'PurchaseReceiptController@receiptExport');
            Route::get('purchase-sent-invoice-summary', 'PurchaseTableController@sentInvoices');
            
            /* Items */
            Route::apiResource('items', ItemController::class);
            
            // Default pdf send option
            Route::apiResource('default-pdf', DefaultPdfSendController::class);
            Route::get('get-default-pdf', 'DefaultPdfSendController@getDefaultPdfOption');
            //payment option
            Route::apiResource('payment-options', PaymentOptionController::class);
            Route::post('payment-options/batch-delete', 'PaymentOptionController@batchDelete');
            //delivery options
            Route::apiResource('delivery-options', DeliveryOptionController::class);
            Route::post('delivery-options/batch-delete', 'DeliveryOptionController@batchDelete');
            // Related
            Route::get('related', 'RelatedController@related');
            //history
            Route::get('history', 'HistoryController@getHistory');
            Route::get('expense-history', 'HistoryController@expenseHistory');
            Route::post('expense-history-export', 'HistoryController@expenseHistoryExport');
            //taxes 
            Route::apiResource('cosumption-taxes', ConsumptionTaxController::class);
            Route::apiResource('income-taxes', IncomeTaxController::class);
            //payment terms
            Route::apiResource('payment-terms', PaymentTermController::class);
            Route::post('payment-terms/batch-delete', 'PaymentTermController@batchDelete');
            // Reports
            Route::prefix('reports')->group(function () {
                Route::get('invoices', 'ReportController@invoicing');
                Route::get('technical', 'ReportController@technicalService');
                Route::get('sales', 'ReportController@sales');
                Route::get('overview', 'ReportController@overview');
                Route::get('purchases', 'ReportController@purchases');
                Route::get('cash-flow', 'ReportController@cashFlow');
                Route::get('stock-valuation', 'ReportController@stockValuation');
                Route::get('tax-summary', 'ReportController@taxSummary');
            });
            Route::get('get-columns/{type}', 'ImportExportController@getColumns');
            Route::post('export/{type}', 'ImportExportController@export');
            Route::post('import/{type}', 'ImportExportController@import');
            Route::post('send-email', 'SendEmailController@sendEmail');
            Route::get('get-message', 'SendEmailController@getMessage');
            Route::post('send-attachments-email', 'SendEmailController@sendAttachmentEmail');
            Route::post('generates', 'GenerateController@generate');

            // Invoice Deposit
            Route::apiResource('deposit', DepositController::class);
            Route::post('deposit-export', 'DepositController@export');
            
            // Catalog Export
            Route::post('product-export', 'CatalogExportController@productExport');
            Route::post('service-export', 'CatalogExportController@serviceExport');
            Route::post('expense-investment-export', 'CatalogExportController@expenseInvestmentsExport');
            Route::post('client-assets-export', 'CatalogExportController@clientAssetExport');

            // supplier bank account
            Route::apiResource('supplier-bank-account', SupplierBankAccountController::class);


            // Other configuration
            Route::apiResource('other-configuration', OtherConfigurationController::class);
            Route::get('technical-module', function(){
                return response()->json([
                    'success' => true,
                    'enable_technical_module' => \Auth::user()->enable_technical_module,
                ]);
            });
        });

        /* Activity Type */
        Route::get('activity-types', 'ActivityTypeController@index');
    });

});