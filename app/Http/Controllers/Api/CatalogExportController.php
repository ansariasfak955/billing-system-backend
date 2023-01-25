<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Client;
use App\Models\Service;
use App\Models\ExpenseAndInvestment;
use App\Models\ClientAsset;
use App\Exports\CatalogProductExport;
use App\Exports\CatalogServiceExport;
use App\Exports\ExpenseInvestmentExport;
use App\Exports\ClientAssetsExport;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class CatalogExportController extends Controller
{
    public function productExport(Request $request, $company_id){
        $validator = Validator::make($request->all(), [
            'ids' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($table);

        $fileName = 'Product-'.time().$company_id.'.xlsx';
        $ids = explode(',', $request->ids);
        $products = Product::whereIn('id', $ids)->get();
        Excel::store(new CatalogProductExport($products), 'public/xlsx/'.$fileName);

        return response()->json([
            'status' => true,
            'url' => url('/storage/xlsx/'.$fileName),
        ]);

    }
    public function serviceExport(Request $request, $company_id){
        $validator = Validator::make($request->all(), [
            'ids' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($table);

        $fileName = 'Service-'.time().$company_id.'.xlsx';
        $ids = explode(',', $request->ids);
        $services = Service::whereIn('id', $ids)->get();
        Excel::store(new CatalogServiceExport($services), 'public/xlsx/'.$fileName);

        return response()->json([
            'status' => true,
            'url' => url('/storage/xlsx/'.$fileName),
        ]);
    }
    public function expenseInvestmentsExport(Request $request, $company_id){
        $validator = Validator::make($request->all(), [
            'ids' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_expense_and_investments';
        ExpenseAndInvestment::setGlobalTable($table);

        $fileName = 'Expense-And-Investment-'.time().$company_id.'.xlsx';
        $ids = explode(',', $request->ids);
        $expenseInvestments = ExpenseAndInvestment::whereIn('id', $ids)->get();
        Excel::store(new ExpenseInvestmentExport($expenseInvestments), 'public/xlsx/'.$fileName);

        return response()->json([
            'status' => true,
            'url' => url('/storage/xlsx/'.$fileName),
        ]);
    }
    public function clientAssetExport(Request $request, $company_id){
        $validator = Validator::make($request->all(), [
            'ids' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_client_assets';
        ClientAsset::setGlobalTable($table);
        $table = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($table);

        $fileName = 'ClientAsstes-'.time().$company_id.'.xlsx';
        $ids = explode(',', $request->ids);
        $clientAssets = ClientAsset::with('client')->whereIn('id', $ids)->get();
        Excel::store(new ClientAssetsExport($clientAssets), 'public/xlsx/'.$fileName);

        return response()->json([
            'status' => true,
            'url' => url('/storage/xlsx/'.$fileName),
        ]);
    }
}
