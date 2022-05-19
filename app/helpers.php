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