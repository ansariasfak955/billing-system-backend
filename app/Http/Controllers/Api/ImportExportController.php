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
    //   $headings = [
    //         'Id',
    //        'Reference',
    //        'Legal_name',
    //        'Tin',
    //        'Phone_1',
    //        'Address',
    //        'State',
    //        'Country',
    //        'Name',
    //        'Email',
    //        'City',
    //        'Zip_code',
    //        'Address_latitude',
    //        'Address_longitude',
    //        'Fax',
    //        'Website',
    //        'Comments',
    //        'Popup_notice',
    //        'Created_from',
    //        'Phone_2',
    //        'Client_category',
    //        'Payment_option_id',
    //        'Payment_date',
    //        'Discount',
    //        'Rate',
    //        'Currency',
    //        'Subject_to_vat',
    //        'Maximum_risk',
    //        'Payment_terms_id',
    //        'Payment_adjustment',
    //        'Agent',
    //        'Invoice_to',
    //        'Subject_to_income_tax',
    //        'Bank_account_format',
    //        'Bank_account_account',
    //        'Bank_account_bic',
    //        'Bank_account_name',
    //        'Bank_account_description',
    //        'Created_at',
    //        'Updated_at',
    //        'Reference_number',
    //        'Ced_ruc',
    //        'Swift_aba'
    //   ];
    //   $headings = [
    //     "id",
    //     "reference",
    //     "reference_number",
    //     "legal_name",
    //     "name",
    //     "tin",
    //     "email",
    //     "phone_1",
    //     "address",
    //     "city",
    //     "state",
    //     "zip_code",
    //     "country",
    //     "address_latitude",
    //     "address_longitude",
    //     "fax",
    //     "phone_2",
    //     "website",
    //     "supplier_category",
    //     "comments",
    //     "popup_notice",
    //     "created_from",
    //     "payment_option_id",
    //     "payment_terms_id",
    //     "payment_date",
    //     "payment_adjustment",
    //     "discount",
    //     "agent",
    //     "rate",
    //     "currency",
    //     "subject_to_vat",
    //     "maximum_risk",
    //     "invoice_to",
    //     "subject_to_income_tax",
    //     "bank_account_format",
    //     "bank_account_account",
    //     "bank_account_bic",
    //     "bank_account_name",
    //     "bank_account_description",
    //     "created_at",
    //     "updated_at"
    //   ];
    //   $headings = [
    //     "id",
    //     "reference",
    //     "reference_number",
    //     "name",
    //     "price",
    //     "purchase_price",
    //     "barcode",
    //     "image",
    //     "product_category_id",
    //     "is_active",
    //     "description",
    //     "private_comments",
    //     "created_from",
    //     "active_margin",
    //     "purchase_margin",
    //     "sales_margin",
    //     "discount",
    //     "minimum_price",
    //     "tax",
    //     "is_promotional",
    //     "manage_stock",
    //     "images",
    //     "created_at",
    //     "updated_at"
    //   ];
    //   $headings = [
    //     "id",
    //     "reference",
    //     "name",
    //     "price",
    //     "purchase_price",
    //     "image",
    //     "vat",
    //     "is_active",
    //     "description",
    //     "private_comments",
    //     "created_from",
    //     "active_margin",
    //     "purchase_margin",
    //     "sales_margin",
    //     "discount",
    //     "minimum_price",
    //     "tax",
    //     "is_promotional",
    //     "manage_stock",
    //     "images",
    //     "created_at",
    //     "updated_at",
    //     "reference_number"
    //   ];
    //   $headings = [
    //     "id",
    //     "reference",
    //     "reference_number",
    //     "client_id",
    //     "address",
    //     "name",
    //     "identifier",
    //     "serial_number",
    //     "brand",
    //     "description",
    //     "private_comments",
    //     "model",
    //     "subject_to_maintenance",
    //     "start_of_warranty",
    //     "end_of_warranty",
    //     "main_image",
    //     "created_at",
    //     "updated_at"
    //   ];

   
        if($type == 'client' || $type == "potential_client"){
            $fileName = 'client-export-'.time().$company_id.'.xlsx';
            $table = 'company_'.$request->company_id.'_clients';
            Client::setGlobalTable($table);
            $data =   Client::get($headings);
            Excel::store(new ClientExport($headings, $data),'public/xlsx/'.$fileName);

        }elseif($type == "suppliers"){
            $fileName = 'supplier-export-'.time().$company_id.'.xlsx';
            $table = 'company_'.$request->company_id.'_suppliers';
            Supplier::setGlobalTable($table);
            $data =   Supplier::get($headings);
            Excel::store(new SupplierExport($headings, $data),'public/xlsx/'.$fileName);

        }elseif($type == 'products'){
            $fileName = 'product-export-'.time().$company_id.'.xlsx';
            $table = 'company_'.$request->company_id.'_products';
            Product::setGlobalTable($table);
            $data =   Product::get($headings);
            Excel::store(new ProductExport($headings, $data),'public/xlsx/'.$fileName);

        }elseif($type == 'service'){
            $fileName = 'service-export-'.time().$company_id.'.xlsx';
            $table = 'company_'.$request->company_id.'_services';
            Service::setGlobalTable($table);
            $data =   Service::get($headings);
            Excel::store(new ServiceExport($headings, $data),'public/xlsx/'.$fileName);

        }elseif($type == 'assets'){
            $fileName = 'assets-export-'.time().$company_id.'.xlsx';
            $table = 'company_'.$request->company_id.'_client_assets';
            ClientAsset::setGlobalTable($table); 
            $data =   ClientAsset::get($headings);
            Excel::store(new AssetsExport($headings, $data),'public/xlsx/'.$fileName);
               
        }
        return response()->json([
            'status' => true,
            'url' => url('/storage/xlsx/'.$fileName),
         ]); 
    }
    public function import(Request $request, $company_id, $type){
        if(!$request->hasFile('file')){
            return response()->json([
                'status' => false,
                'message' => 'Please choose a file!',
            ]);
        }
        if($type == 'client' || $type == "potential_client"){
            $path = Storage::putFile('public/file', $request->file('file'));
            $import = new ClientImport($company_id);

        }elseif($type == "suppliers"){
            $path = Storage::putFile('public/file', $request->file('file'));
            $import = new SupplierImport($company_id);

        }elseif($type == 'products'){
            $path = Storage::putFile('public/file', $request->file('file'));
            $import = new ProductImport($company_id);

        }elseif($type == 'service'){
            $path = Storage::putFile('public/file', $request->file('file'));
            $import = new ServiceImport($company_id);

        }elseif($type == 'assets'){
            $path = Storage::putFile('public/file', $request->file('file'));
            $import = new ClientAssetImport($company_id);
        }
        Excel::import($import, $path);
        Storage::delete($path);
        return response()->json([
            'status' => true,
            'message' => 'Exported Sucessfully!',
        ]);
    }
}
