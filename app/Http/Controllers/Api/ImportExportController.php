<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Service;
use App\Models\ClientAsset;
use App\Exports\ClientExport;
use App\Exports\SupplierExport;
use App\Exports\ProductExport;
use App\Exports\ServiceExport;
use App\Exports\AssetsExport;
use App\Imports\ClientImport;
use App\Imports\SupplierImport;
use App\Imports\ProductImport;
use App\Imports\ServiceImport;
use App\Imports\ClientAssetImport;
use Illuminate\Support\File;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class ImportExportController extends Controller
{
    public function getColumns($company_id,$type){
        $columns = [];
        if($type == "client" || $type == "potential_client"){
            $getClientColumn = new Client;
            $columns = $getClientColumn->getTableColumns();
        }elseif($type == "suppliers"){
            $getSupplierColumn = new Supplier;
            $columns = $getSupplierColumn->getTableColumns();
        }elseif($type == "products"){
            $getProductColumn = new Product;
            $columns = $getProductColumn->getTableColumns();
        }elseif($type == "service"){
            $getServiceColumn = new Service;
            $columns = $getServiceColumn->getTableColumns();
        }elseif($type == "assets"){
            $getAssetsColumn = new ClientAsset;
            $columns = $getAssetsColumn->getTableColumns();
        }
        return response()->json([
            'status' => true,
            'columns' => $columns
        ]);
    }
    public function export(Request $request, $company_id, $type){
      $headings = $request->headings;
        if($type == 'client' || $type == "potential_client"){
            $fileName = 'client-export-'.time().$company_id.'.xlsx';
            $table = 'company_'.$request->company_id.'_clients';
            Client::setGlobalTable($table);

            $haveClientCategoryId = '';
            $havePaymentTermsId = '';
            $havePaymentOptionId = '';
            if( in_array('client_category', $headings) ){
                $haveClientCategoryId = 1;
            }
            if( in_array('payment_terms_id', $headings) ){
                $havePaymentTermsId = 1;
            }
            if( in_array('payment_option_id', $headings) ){
                $havePaymentOptionId = 1;
            }

            $data =  Client::get($headings)->toArray();

            if($haveClientCategoryId){
                $headings[] = 'client_category_name';
                if (($key = array_search('client_category', $headings)) !== false) {
                    unset($headings[$key]);
                }
            }
            if($havePaymentTermsId){
                $headings[] = 'payment_terms_name';
                if (($key = array_search('payment_terms_id', $headings)) !== false) {
                    unset($headings[$key]);
                }
            }
            if($havePaymentOptionId){
                $headings[] = 'payment_option_name';
                if (($key = array_search('payment_option_id', $headings)) !== false) {
                    unset($headings[$key]);
                }
            }

            $finalData = [];
            if(!empty($data)){
                foreach($data as $arr){
                   if($haveClientCategoryId){
                    unset($arr['client_category']);
                   }
                   if($havePaymentTermsId){
                    unset($arr['payment_terms_id']);
                   }
                   if($havePaymentOptionId){
                    unset($arr['payment_option_id']);
                   }
                   $finalData[] = $arr;
                }
            }
            Excel::store(new ClientExport($headings, $finalData),'public/xlsx/'.$fileName);

        }elseif($type == "suppliers"){
            $fileName = 'supplier-export-'.time().$company_id.'.xlsx';
            $table = 'company_'.$request->company_id.'_suppliers';
            Supplier::setGlobalTable($table);

            $haveSupplierCategoryId = '';
            $havePaymentTermsId = '';
            $havePaymentOptionId = '';

            if( in_array('supplier_category', $headings) ){
                $haveSupplierCategoryId = 1;
            }
            if( in_array('payment_terms_id', $headings) ){
                $havePaymentTermsId = 1;
            }
            if( in_array('payment_option_id', $headings) ){
                $havePaymentOptionId = 1;
            }

            $data =  Supplier::get($headings)->toArray();
            if($haveSupplierCategoryId){
                $headings[] = 'supplier_category_name';
                if (($key = array_search('supplier_category', $headings)) !== false) {
                    unset($headings[$key]);
                }
            }
            if($havePaymentTermsId){
                $headings[] = 'payment_terms_name';
                if (($key = array_search('payment_terms_id', $headings)) !== false) {
                    unset($headings[$key]);
                }
            }
            if($havePaymentOptionId){
                $headings[] = 'payment_option_name';
                if (($key = array_search('payment_option_id', $headings)) !== false) {
                    unset($headings[$key]);
                }
            }

            $finalData = [];
            if(!empty($data)){
                foreach($data as $arr){
                   if($haveSupplierCategoryId){
                    unset($arr['supplier_category']);
                   }
                   if($havePaymentTermsId){
                    unset($arr['payment_terms_id']);
                   }
                   if($havePaymentOptionId){
                    unset($arr['payment_option_id']);
                   }
                   $finalData[] = $arr;
                }
            }

            Excel::store(new SupplierExport($headings, $finalData),'public/xlsx/'.$fileName);

        }elseif($type == 'products'){
            $fileName = 'product-export-'.time().$company_id.'.xlsx';
            $table = 'company_'.$request->company_id.'_products';
            Product::setGlobalTable($table);

            $haveProductCategoryId = '';

            if( in_array('product_category_id', $headings) ){
                $haveProductCategoryId = 1;
            }

            $data =  Product::get($headings)->toArray();
            if($haveProductCategoryId){
                $headings[] = 'product_category_name';
                if (($key = array_search('product_category_id', $headings)) !== false) {
                    unset($headings[$key]);
                }
            }

            $finalData = [];
            if(!empty($data)){
                foreach($data as $arr){
                   if($haveProductCategoryId){
                    unset($arr['product_category_id']);
                   }
                   $finalData[] = $arr;
                }
            }
            
            Excel::store(new ProductExport($headings, $finalData),'public/xlsx/'.$fileName);

        }elseif($type == 'service'){
            $fileName = 'service-export-'.time().$company_id.'.xlsx';
            $table = 'company_'.$request->company_id.'_services';
            Service::setGlobalTable($table);

            $haveProductCategoryId = '';

            if( in_array('product_category_id', $headings) ){
                $haveProductCategoryId = 1;
            }

            $data =  Service::get($headings)->toArray();
            if($haveProductCategoryId){
                $headings[] = 'product_category_name';
                if (($key = array_search('product_category_id', $headings)) !== false) {
                    unset($headings[$key]);
                }
            }

            $finalData = [];
            if(!empty($data)){
                foreach($data as $arr){
                   if($haveProductCategoryId){
                    unset($arr['product_category_id']);
                   }
                   $finalData[] = $arr;
                }
            }

            Excel::store(new ServiceExport($headings, $finalData),'public/xlsx/'.$fileName);

        }elseif($type == 'assets'){
            $fileName = 'assets-export-'.time().$company_id.'.xlsx';
            $table = 'company_'.$request->company_id.'_client_assets';
            ClientAsset::setGlobalTable($table); 

            $haveClientId = '';

            if( in_array('client_id', $headings) ){
                $haveClientId = 1;
            }

            $data =  ClientAsset::get($headings)->toArray();

            if($haveClientId){
                $headings[] = 'client_name';
                if (($key = array_search('client_id', $headings)) !== false) {
                    unset($headings[$key]);
                }
            }

            $finalData = [];
            if(!empty($data)){
                foreach($data as $arr){
                   if($haveClientId){
                    unset($arr['client_id']);
                   }
                   $finalData[] = $arr;
                }
            }
            Excel::store(new AssetsExport($headings, $finalData),'public/xlsx/'.$fileName);
               
        }
        return response()->json([
            'status' => true,
            'url' => url('/storage/xlsx/'.$fileName),
         ]); 
    }
    public function import(Request $request, $company_id, $type){

        $validator = Validator::make($request->all(),[
            'reference' => "required",
            'file' => 'required'
        ],[
            'file.required' => 'Please choose a file!'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ]);
        }

        if($type == 'client' || $type == "potential_client"){
            $path = Storage::putFile('public/file', $request->file('file'));
            $import = new ClientImport($company_id, $request);

        }elseif($type == "suppliers"){
            $path = Storage::putFile('public/file', $request->file('file'));
            $import = new SupplierImport($company_id, $request);

        }elseif($type == 'products'){
            $path = Storage::putFile('public/file', $request->file('file'));
            $import = new ProductImport($company_id, $request);

        }elseif($type == 'service'){
            $path = Storage::putFile('public/file', $request->file('file'));
            $import = new ServiceImport($company_id, $request);

        }elseif($type == 'assets'){
            $path = Storage::putFile('public/file', $request->file('file'));
            $import = new ClientAssetImport($company_id, $request);
        }
        Excel::import($import, $path);
        Storage::delete($path);
        return response()->json([
            'status' => true,
            'message' => 'Exported Sucessfully!',
        ]);
    }
}
