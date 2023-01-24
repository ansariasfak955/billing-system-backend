<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Exports\CatalogExport;
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

        $fileName = 'product-'.time().$company_id.'.xlsx';
        $ids = explode(',', $request->ids);
        $products = Product::whereIn('id', $ids)->get();
        Excel::store(new CatalogExport($products), 'public/xlsx/'.$fileName);

        return response()->json([
            'status' => true,
            'url' => url('/storage/xlsx/'.$fileName),
        ]);

    }
}
