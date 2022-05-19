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
	return \App\Models\ProductStock::where('product_id', $product_stock_id)->first();
}

function get_client_latest_ref_number($company_id, $reference, $add)
{
	$table = 'company_'.$company_id.'_clients';
    \App\Models\Client::setGlobalTable($table);
    $client = \App\Models\Client::where('reference', $reference)->orderBy('reference_number', 'DESC')->first();
    $reference_number = str_replace('0', '', $client->reference_number);
    if ($client != NULL) {
    	return generate_reference_num($reference_number+$add,5);
    } else {
    	return '00001';
    }
}

function get_product_latest_ref_number($company_id, $reference, $add)
{
    $table = 'company_'.$company_id.'_products';
    \App\Models\Product::setGlobalTable($table);
    $product = \App\Models\Product::where('reference', $reference)->orderBy('reference_number', 'DESC')->first();
    $reference_number = str_replace('0', '', $product->reference_number);
    if ($product != NULL) {
        return generate_reference_num($reference_number+$add,5);
    } else {
        return '00001';
    }
}

function generate_reference_num ($input, $pad_len = 7) {
    return str_pad($input, $pad_len, "0", STR_PAD_LEFT);
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
        'clients' => 'Clients',
    ];

    $role_id = Auth::user()->roles->pluck('id')->first();

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

    return response()->json([
        'success' => true,
        'permissions' => $permission_arr,
    ]);
}