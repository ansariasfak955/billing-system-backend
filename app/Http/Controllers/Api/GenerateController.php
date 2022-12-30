<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MyTemplate;
use App\Models\Company;
use App\Models\Product;
use App\Models\Item;
use App\Models\SalesEstimate;
use App\Models\TechnicalTable;
use App\Models\InvoiceTable;
use App\Models\PurchaseTable;
use App\Models\MyTemplateMeta;
use App\Models\Reference;
use Validator;

class GenerateController extends Controller
{
    public function generate(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        $type = urldecode($request->to_type);

        Reference::setGlobalTable('company_'.$request->company_id.'_references');
        $referenceType = Reference::where('type', $type)->where('by_default', '1')->pluck('prefix')->first();

        if($request->from_type == 'Sales Estimate'){
            $table = 'company_'.$request->company_id.'_sales_estimates';
            SalesEstimate::setGlobalTable($table);
            
                $salesEstimate = SalesEstimate::with('items')->find($request->id);

                if($request->to_type){
                    $salesEstimate = SalesEstimate::with('items')->find($request->id);
                    $duplicatedEstimate = $salesEstimate->replicate();
                    $duplicatedEstimate->created_at = now();
                    $duplicatedEstimate->reference = $referenceType ;
                    $duplicatedEstimate->reference_number = get_sales_estimate_latest_ref_number($request->company_id, $referenceType, 1 );
                    $duplicatedEstimate->save();
                    
                    foreach($salesEstimate->items as $salesItems){
                        $duplicatedItem = $salesItems->replicate();
                        $duplicatedItem->created_at = now();
                        $duplicatedItem->parent_id =  $duplicatedEstimate->id;
                        $duplicatedItem->save();
                    }
            
                    return response()->json([
                        'status' => true,
                        'message' => 'Generate successfully',
                        'data' =>  SalesEstimate::with('items')->find($duplicatedEstimate->id)
                    ]);
                }
        }elseif($request->from_type == 'Sales Estimate'){
            $table = 'company_'.$request->company_id.'_sales_estimates';
            SalesEstimate::setGlobalTable($table);
            
                $salesEstimate = SalesEstimate::with('items')->find($request->id);

                if($request->to_type){
                    $table = 'company_'.$request->company_id.'_invoice_tables';
                    InvoiceTable::setGlobalTable($table);
                    $salesEstimate = InvoiceTable::with('items')->find($request->id);
                    $duplicatedEstimate = $salesEstimate->replicate();
                    $duplicatedEstimate->created_at = now();
                    $duplicatedEstimate->reference = $referenceType ;
                    $duplicatedEstimate->reference_number = get_sales_estimate_latest_ref_number($request->company_id, $referenceType, 1 );
                    $duplicatedEstimate->save();
                    
                    foreach($salesEstimate->items as $salesItems){
                        $duplicatedItem = $salesItems->replicate();
                        $duplicatedItem->created_at = now();
                        $duplicatedItem->parent_id =  $duplicatedEstimate->id;
                        $duplicatedItem->save();
                    }
            
                    return response()->json([
                        'status' => true,
                        'message' => 'Generate successfully',
                        'data' =>  $duplicatedEstimate
                    ]);
                }
        }

    }
}
