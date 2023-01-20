<?php
  
function active_class($path, $active = 'active') {
  return call_user_func_array('Request::is', (array)$path) ? $active : '';
}

function is_active_route($path) {
  return call_user_func_array('Request::is', (array)$path) ? 'true' : 'false';
}

function show_class($path) {
  return call_user_func_array('Request::is', (array)$path) ? 'show' : '';
}

function get_product_name($company_id,$product_id)
{
	$table = 'company_'.$company_id.'_products';
    \App\Models\Product::setGlobalTable($table);
	return \App\Models\Product::where('id', $product_id)->pluck('name')->first();
}

function get_service_name($company_id,$service_id)
{
	$table = 'company_'.$company_id.'_services';
    \App\Models\Service::setGlobalTable($table);
	return \App\Models\Service::where('id', $service_id)->pluck('name')->first();
}

function get_category_name($company_id,$category_id)
{
    $table = 'company_'.$company_id.'_client_categories';
    \App\Models\ClientCategory::setGlobalTable($table);
    return \App\Models\ClientCategory::where('id', $category_id)->pluck('name')->first();
}

function get_client_name($company_id,$client_id)
{
    $table = 'company_'.$company_id.'_clients';
    \App\Models\Client::setGlobalTable($table);
    return \App\Models\Client::where('id', $client_id)->pluck('legal_name')->first();
}
function get_payment_terms_name($company_id,$payment_terms_id){
    $table = 'company_'.$company_id.'_payment_terms';
    \App\Models\PaymentTerm::setGlobalTable($table);
    return \App\Models\PaymentTerm::where('id', $payment_terms_id)->pluck('name')->first();
}
function get_user_name($company_id,$client_id)
{
    $table = 'company_'.$company_id.'users';
    \App\Models\User::setGlobalTable($table);
    return \App\Models\User::where('id', $client_id)->pluck('name')->first();
}
function get_user_email($company_id,$client_id)
{
    $table = 'company_'.$company_id.'users';
    \App\Models\User::setGlobalTable($table);
    return \App\Models\User::where('id', $client_id)->pluck('email')->first();
}
function get_supplier_name($company_id,$sup_id)
{
    $table = 'company_'.$company_id.'_suppliers';
    \App\Models\Supplier::setGlobalTable($table);
    return \App\Models\Supplier::where('id', $sup_id)->pluck('legal_name')->first();
}
function get_product_category_name($company_id,$product_category_id)
{
    $table = 'company_'.$company_id.'_product_categories';
    \App\Models\Supplier::setGlobalTable($table);
    return \App\Models\Supplier::where('id', $product_category_id)->pluck('name')->first();
}

function get_asset_name($company_id,$asset_id)
{
    $table = 'company_'.$company_id.'_client_assets';
    \App\Models\ClientAsset::setGlobalTable($table);
    return \App\Models\ClientAsset::where('id', $asset_id)->pluck('name')->first();
}

function get_payment_option_name($company_id,$payment_option_id)
{
    if($company_id){

        $table = 'company_'.$company_id.'_payment_options';
        \App\Models\PaymentOption::setGlobalTable($table);
        return \App\Models\PaymentOption::where('id', $payment_option_id)->pluck('name')->first();
    }
}

function get_company_country_name($company_id)
{
	if ($company_id != '') {
		return \App\Models\Company::where('id', $company_id)->pluck('country')->first();
	}
	return '';
}

function get_product_stock($company_id,$product_stock_id)
{
	if ($company_id == '') {
		return '';
	}
	$table = 'company_'.$company_id.'_product_stocks';
    \App\Models\ProductStock::setGlobalTable($table);
	return \App\Models\ProductStock::where('product_id', $product_stock_id)->get();
}

function get_client_latest_ref_number($company_id, $reference, $add)
{
	$table = 'company_'.$company_id.'_clients';
    \App\Models\Client::setGlobalTable($table);
    $client = \App\Models\Client::where('reference', $reference)->orderBy('reference_number', 'DESC')->first();
   
    if ($client != NULL) {
        $reference_number = ltrim($client->reference_number, '0');
    	return generate_reference_num((int)$reference_number+$add,5);
    } else {
    	return '00001';
    }
}
function get_client_asset_latest_ref_number($company_id, $reference, $add)
{
    $table = 'company_'.$company_id.'_client_assets';
    \App\Models\ClientAsset::setGlobalTable($table);
    $clientAsset = \App\Models\ClientAsset::where('reference', $reference)->orderBy('reference_number', 'DESC')->first();
    if ($clientAsset != NULL) {
        $reference_number = ltrim($clientAsset->reference_number, '0');
        return generate_reference_num((int)$reference_number+$add,5);
    } else {
        return '00001';
    }
}
function get_supplier_latest_ref_number($company_id, $reference, $add)
{
    $table = 'company_'.$company_id.'_suppliers';
    \App\Models\Supplier::setGlobalTable($table);
    $Supplier = \App\Models\Supplier::where('reference', $reference)->orderBy('reference_number', 'DESC')->first();
    if ($Supplier != NULL) {
        $reference_number = ltrim($Supplier->reference_number, '0');
        return generate_reference_num((int)$reference_number+$add,5);
    } else {
        return '00001';
    }
}
function get_technical_incident_latest_ref_number($company_id, $reference, $add)
{
    $table = 'company_'.$company_id.'_technical_incidents';
    \App\Models\TechnicalIncident::setGlobalTable($table);
    $TechnicalIncident = \App\Models\TechnicalIncident::where('reference', $reference)->orderBy('reference_number', 'DESC')->first();
    if ($TechnicalIncident != NULL) {
        $reference_number = ltrim($TechnicalIncident->reference_number, '0');
        return generate_reference_num((int)$reference_number+$add,5);
    } else {
        return '00001';
    }
}
function get_sales_estimate_latest_ref_number($company_id, $reference, $add)
{
    $table = 'company_'.$company_id.'_sales_estimates';
    \App\Models\SalesEstimate::setGlobalTable($table);
    $SalesEstimate = \App\Models\SalesEstimate::where('reference', $reference)->orderBy('reference_number', 'DESC')->first();
    if ($SalesEstimate != NULL) {
        $reference_number = ltrim($SalesEstimate->reference_number, '0');
        return generate_reference_num((int)$reference_number+$add,5);
    } else {
        return '00001';
    }
}

function get_technical_table_latest_ref_number($company_id, $reference, $add)
{
    $table = 'company_'.$company_id.'_technical_tables';
    \App\Models\TechnicalTable::setGlobalTable($table);
    $TechnicalTable = \App\Models\TechnicalTable::where('reference', $reference)->orderBy('reference_number', 'DESC')->first();
    if ($TechnicalTable != NULL) {
        $reference_number = ltrim($TechnicalTable->reference_number, '0');
        return generate_reference_num((int)$reference_number+$add,5);
    } else {
        return '00001';
    }
}
function get_purchase_table_latest_ref_number($company_id, $reference, $add)
{
    $table = 'company_'.$company_id.'_purchase_tables';
    \App\Models\PurchaseTable::setGlobalTable($table);
    $PurchaseTable = \App\Models\PurchaseTable::where('reference', $reference)->orderBy('reference_number', 'DESC')->first();
    if ($PurchaseTable != NULL) {
        $reference_number =ltrim($PurchaseTable->reference_number, '0');
        return generate_reference_num((int)$reference_number+$add,5);
    } else {
        return '00001';
    }
}
function get_purchase_ticket_table_latest_ref_number($company_id, $reference, $add)
{
    $table = 'company_'.$company_id.'_purchase_tickets';
    \App\Models\PurchaseTicket::setGlobalTable($table);
    $PurchaseTicket = \App\Models\PurchaseTicket::where('reference', $reference)->orderBy('reference_number', 'DESC')->first();
    if ($PurchaseTicket != NULL) {
        $reference_number = ltrim($PurchaseTicket->reference_number, '0');
        return generate_reference_num((int)$reference_number+$add,5);
    } else {
        return '00001';
    }
}
function get_invoice_table_latest_ref_number($company_id, $reference, $add)
{
    $table = 'company_'.$company_id.'_invoice_tables';
    \App\Models\InvoiceTable::setGlobalTable($table);
    $InvoiceTable = \App\Models\InvoiceTable::where('reference', $reference)->orderBy('reference_number', 'DESC')->first();
    if ($InvoiceTable != NULL) {
        $reference_number = ltrim($InvoiceTable->reference_number, '0');
        return generate_reference_num((int)$reference_number+$add,5);
    } else {
        return '00001';
    }
}

function get_product_latest_ref_number($company_id, $reference, $add)
{
    $table = 'company_'.$company_id.'_products';
    \App\Models\Product::setGlobalTable($table);
    $product = \App\Models\Product::where('reference', $reference)->orderBy('reference_number', 'DESC')->first();
    $reference_number = '';
    
    if ($reference_number != NULL) {
        return '00001';
    } else {
        if ($product != NULL) {
            $reference_number = ltrim($product->reference_number, '0');
            return generate_reference_num((int)$reference_number+$add,5);
        } else {
            return '00001';
        }
    }
}
function get_service_latest_ref_number($company_id, $reference, $add){
    $table = 'company_'.$company_id.'_services';
    \App\Models\Service::setGlobalTable($table);
    $service = \App\Models\Service::where('reference', $reference)->orderBy('reference_number', 'DESC')->first();
    $reference_number = '';
    if ($reference_number != NULL) {
        return '00001';
    } else {
        if ($service != NULL) {
            $reference_number = ltrim($service->reference_number, '0');
            return generate_reference_num((int)$reference_number+$add,5);
        } else {
            return '00001';
        }
    }
}

function get_expense_and_investment_latest_ref_number($company_id, $reference, $add){
    $table = 'company_'.$company_id.'_expense_and_investments';
    \App\Models\ExpenseAndInvestment::setGlobalTable($table);
    $expense_and_investment = \App\Models\ExpenseAndInvestment::where('reference', $reference)->orderBy('reference_number', 'DESC')->first();
    $reference_number = ltrim($expense_and_investment->reference_number, '0');
    if ($reference_number == NULL) {
        return '00001';
    } else {
        if ($expense_and_investment != NULL) {
            return generate_reference_num($reference_number+$add,5);
        } else {
            return '00001';
        }
    }
}

function generate_reference_num ($input, $pad_len = 7) {
    return str_pad($input, $pad_len, "0", STR_PAD_LEFT);
}

function get_user_role($company_id, $user_id)
{
    $model_has_roles_table = "company_".$company_id."_model_has_roles";
    $role_id = \DB::table($model_has_roles_table)->where('model_id', $user_id)->pluck('role_id')->first();
    $table = 'company_'.$company_id.'_roles';
    \App\Models\Role::setGlobalTable($table);
    return \App\Models\Role::where('id', $role_id)->pluck('name')->first();
}

function get_roles_permissions($company_id)
{
    $table = 'company_'.$company_id.'_permissions';
    \App\Models\Permission::setGlobalTable($table);
    
    $permissions_arr = [
        'users'    => 'Users',
        'roles'    => 'Roles',
        'bank accounts'    => 'Bank accounts',
        'my templates'    => 'My templates',
        'custom states'    => 'Custom states',
        'price rates'    => 'Price Rates',
        'services'    => 'Services',
        'email configuration'    => 'Email Configuration',
        'products' => 'Products',
        'product categories' => 'Product Categories',
        'products' => 'Products',
        'services' => 'Services',
        'expenses and investments' => 'Expenses & Investments',
        'client assets' => 'Client Assets',
        'clients' => 'Client',
        'potential clients' => 'Potential Clients',
        'contacts' => 'Contacts',
        'client bank account' => 'Client bank account',
        'estimates' => 'Estimates',
        'orders' => 'Orders',
        'delivery notes' => 'Delivery Notes',
        'incidents' => 'Incidents',
        'work estimate' => 'Work Estimate',
        'work orders' => 'Work Orders',
        'work delivery notes' => 'Work Delivery Notes',
        'invoices' => 'Invoices',
        'refunds' => 'Refunds',
        'ordinary invoice receipts' => 'Ordinary Invoice Receipts',
        'refund receipts' => 'Refund Receipts',
        'invoice summary' => 'Invoice Summary',
        'tickets and other expenses' => 'Tickets and other expenses',
        'purchase orders' => 'Purchase Orders',
        'purchase delivery notes' => 'Purchase Delivery Notes',
        'purchase invoices' => 'Purchase Invoices',
        'purchase invoice receipts' => 'Purchase Invoice Receipts',
        'purchase invoice summary' => 'Purchase Invoice Summary',
        'suppliers' => 'Suppliers',
        'supplier bank account' => 'Supplier Bank Account',
        'debtor clients' => 'Debtor clients',
        'management of incidents' => 'Management of incidents',
        'my business' => 'My business',
        'references' => 'References',
        'payment terms' => 'Payment Terms',
        'price rates' => 'Price Rates',
        'payment options' => 'Payment Options',
        'client and supplier categories' => 'Client and Supplier Categories',
        'product categories' => 'Product Categories',
        'delivery options' => 'Delivery Options',
        'event types' => 'Event Types',
        'expense categories' => 'Expense Categories',
        'taxes' => 'Taxes',
        'advanced settings' => 'Advanced Settings',
        'automatic tasks' => 'Automatic Tasks',
    ];
    $reports_arr = [
        'Overview',
        'Invoicing by Client',
        'Invoicing by Agent',
        'Invoicing by Item',
        'Cash Flow Overview',
        'Cash Flow by Payment Options',
        'Cash Flow by Agent',
        'Sales Overview',
        'Sales by Client',
        'Sales by Agent',
        'Sales by Item',
        'Technical Service Overview',
        'Incidents by Client',
        'Incidents by Agent',
        'Technical Service by Client',
        'Technical Service by Agent',
        'Technical Service by Item',
        'Purchases by Provider',
        'Purchases by Item',
        'Stock Valuation',
        'View tax reports',
    ];
    $model_has_roles_table = "company_".$company_id."_model_has_roles";
    $role_id = \DB::table($model_has_roles_table)->where('model_id', Auth::user()->id)->pluck('role_id')->first();

    $permission_arr = [];
    foreach ($permissions_arr as $permission_key => $permission_value) {
        $permission_key_new = \App\Models\Permission::where('name', $permission_value)->with('children')->get();
        $role_has_permissions = 'company_'.$company_id.'_role_has_permissions';
        $view = \DB::table($role_has_permissions)
            ->where('role_id', $role_id)
            ->where('permission_id', $permission_key_new[0]->children->where('name', "view $permission_key")->pluck('id')->first())
            ->first();

        $edit = \DB::table($role_has_permissions)
            ->where('role_id', $role_id)
            ->where('permission_id', $permission_key_new[0]->children->where('name', "edit $permission_key")->pluck('id')->first())
            ->first();

        $create = \DB::table($role_has_permissions)
            ->where('role_id', $role_id)
            ->where('permission_id', $permission_key_new[0]->children->where('name', "create $permission_key")->pluck('id')->first())
            ->first();
        // to fix new client permission
        // if($permission_key == 'clients'){
        //     $create = \DB::table($role_has_permissions)
        //     ->where('role_id', $role_id)
        //     ->where('permission_id', $permission_key_new[0]->children[0]->children->where('name', "create $permission_key")->pluck('id')->first())
        //     ->first();
        // }

        $delete = \DB::table($role_has_permissions)
            ->where('role_id', $role_id)
            ->where('permission_id', $permission_key_new[0]->children->where('name', "delete $permission_key")->pluck('id')->first())
            ->first();

        $new_key = str_replace(' ','_', $permission_key);

        $permission_arr[$new_key] = array(
            "$new_key" => array(
                'view'   => array(
                    'is_checked' => $view != NULL ? 1 : 0
                ),
                'edit'   => array(
                    'is_checked' => $edit != NULL ? 1 : 0
                ),
                'create' => array(
                    'is_checked' => $create != NULL ? 1 : 0
                ),
                'delete' => array(
                    'is_checked' => $delete != NULL ? 1 : 0
                )
            )
        );
    }
    foreach ($reports_arr as $permission_key) {

        $permission_key_new = \App\Models\Permission::where('name', $permission_key)->first();
        $role_has_permissions = 'company_'.$company_id.'_role_has_permissions';
        $is_checked = 0;
        if( $permission_key_new ){
            
            $access = \DB::table($role_has_permissions)
                ->where('role_id', $role_id)
                ->where('permission_id', $permission_key_new->id)
                ->first();
            if( $access ){
                $is_checked = 1;
            }
        }

        $filtered_key = str_replace(' ','_', strtolower( $permission_key ) );

        $permission_arr['reports']["$filtered_key"] = array(
                'is_checked' =>  $is_checked

        );
    }

    return response()->json($permission_arr);
}
function get_formatted_datetime($date_time){
    // $strDate = substr($date_time,4,20);
    $date = strtotime($date_time);
    return date('Y-m-d H:i:s',$date);
}

function getSettingValue($key)
{
    return App\Models\Setting::where('option_name', $key)->pluck('option_value')->first();
}

function getCompanySetting($company , $key){
    App\Models\Setting::setGlobalTable('company_'.$company.'_settings');
    return App\Models\Setting::where('option_name', $key)->pluck('option_value')->first();
}
function getReferenceTypes($type){
    if($type == 'template'){
        return ['Ordinary Invoice', 'Purchase Delivery Note', 'Purchase Invoice', 'Purchase Order', 'Refund Invoice', 'Sales Delivery Note', 'Sales Estimate', 'Sales Order', 'Work Delivery Note', 'Work Estimate', 'Work Order'];
    }
    elseif($type == 'nottemplate'){

        return ['Client', 'Client Asset', 'Expense', 'Expense and investment', 'Incident', 'Potential Client', 'Product', 'Service', 'Supplier'];
    }
    return ['Client', 'Client Asset', 'Expense', 'Expense and investment', 'Incident', 'Potential Client', 'Product', 'Service', 'Supplier', 'Ordinary Invoice', 'Purchase Delivery Note', 'Purchase Invoice', 'Purchase Order', 'Refund Invoice', 'Sales Delivery Note', 'Sales Estimate', 'Sales Order', 'Work Delivery Note', 'Work Estimate', 'Work Order'];
}
function getReferenceTypePrefix($index){
    $refrencePrefix = ['Client' => 'CLI', 'Client Asset' => 'AST', 'Expense' => 'EXP', 'Expense and investment' => 'EAI', 'Incident' => 'INC', 'Potential Client' => 'PCL', 'Product' => 'PRO', 'Service' => 'SER', 'Supplier' =>'SUP','Ordinary Invoice' => 'INV', 'Purchase Delivery Note' => 'PDN', 'Purchase Invoice' => 'PINV', 'Purchase Order' => 'PO', 'Refund Invoice' => 'RET', 'Sales Delivery Note' => 'SDN', 'Sales Estimate' => 'SE', 'Sales Order' => 'SO', 'Work Delivery Note' => 'WDN', 'Work Estimate' => 'WE', 'Work Order' => 'WO'];
    return  $refrencePrefix[$index];
}
function generateReferences($company_id){
    if (Schema::hasTable('company_'.$company_id.'_references')) {
        \App\Models\Reference::setGlobalTable('company_'.$company_id.'_references');
        \App\Models\MyTemplate::setGlobalTable('company_'.$company_id.'_my_templates');
        foreach(getReferenceTypes('all') as $referenceType){
            if(!\App\Models\Reference::where('type', $referenceType)->first()){
                $refrence = \App\Models\Reference::create([
                    'name' => $referenceType,
                    "type" => $referenceType,
                    'prefix' => getReferenceTypePrefix($referenceType),
                    'number_of_digit' => 5,
                    "by_default" => "1"
                ]);
                $template_id = 0;
                if(in_array($referenceType, getReferenceTypes('template'))){
                    $refrence->title = $referenceType;
                    $template_id = \App\Models\MyTemplate::where('document_type', $referenceType)->pluck('id')->first() ?? 0;
                    $refrence->template_id = $template_id;
                    $refrence->save();
                }
            }
        }
    }
}
function get_reference_type($company_id, $reference)
{
    $refernceTable = "company_".$company_id."_references";
    \App\Models\Reference::setGlobalTable($refernceTable);
    return \App\Models\Reference::where('prefix', $reference)->pluck('type')->first();
}