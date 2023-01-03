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
            $itemTable = 'company_'.$request->company_id.'_items';
            Item::setGlobalTable($itemTable);
            
                $salesEstimate = SalesEstimate::with('items')->find($request->id);

                if($request->to_type == 'Sales Order' || $request->to_type == 'Sales Delivery Note'){
                    $salesEstimate = SalesEstimate::with('items')->find($request->id);

                    $generateEstimate = $salesEstimate->replicate();
                    $generateEstimate->created_at = now();
                    $generateEstimate->reference = $referenceType ;
                    $generateEstimate->reference_number = get_sales_estimate_latest_ref_number($request->company_id, $referenceType, 1 );
                    $generateEstimate->save();
                    
                    foreach($salesEstimate->items as $salesItems){
                        $generateItem = $salesItems->replicate();
                        $generateItem->created_at = now();
                        $generateItem->type = $referenceType ;
                        $generateItem->parent_id =  $generateEstimate->id;
                        $generateItem->save();
                    }
            
                    return response()->json([
                        'status' => true,
                        'message' => 'Generate successfully',
                        'data' =>  SalesEstimate::with('items')->find($generateEstimate->id)
                    ]);
                }elseif($request->to_type == 'Ordinary Invoice'){
                    $table = 'company_'.$request->company_id.'_invoice_tables';
                    InvoiceTable::setGlobalTable($table);
                    // $invoiceGenerate = InvoiceTable::create($request->all());

                    // $generateEstimate = $salesEstimate->replicate();
                    $array = $salesEstimate->toArray();
                    $generateEstimate = InvoiceTable::create($array);
                    $generateEstimate->created_at = now();
                    $generateEstimate->reference = $referenceType ;
                    $generateEstimate->reference_number = get_sales_estimate_latest_ref_number($request->company_id, $referenceType, 1 );
                    $generateEstimate->save();
                    
                    foreach($salesEstimate->items as $salesItems){
                        $generateItem = $salesItems->replicate();
                        $generateItem->created_at = now();
                        $generateItem->type = $referenceType ;
                        $generateItem->parent_id =  $generateEstimate->id;
                        $generateItem->save();
                    }
            
                    return response()->json([
                        'status' => true,
                        'message' => 'Generate successfully',
                        'data' =>  InvoiceTable::with('items')->find($generateEstimate->id)
                    ]);
                }
        }

    }
}
