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
use App\Models\TechnicalIncident;
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

                    $array = $salesEstimate->toArray();
                    $generateEstimate = InvoiceTable::create($array);
                    $generateEstimate->created_at = now();
                    $generateEstimate->reference = $referenceType ;
                    $generateEstimate->reference_number = get_invoice_table_latest_ref_number($request->company_id, $referenceType, 1 );
                    $generateEstimate->save();
                    
                    foreach($salesEstimate->items as $invoiceItems){
                        $generateInvoiceItem = $invoiceItems->replicate();
                        $generateInvoiceItem->created_at = now();
                        $generateInvoiceItem->type = $referenceType ;
                        $generateInvoiceItem->parent_id =  $generateEstimate->id;
                        $generateInvoiceItem->save();
                    }
            
                    return response()->json([
                        'status' => true,
                        'message' => 'Generate successfully',
                        'data' =>  InvoiceTable::with('items')->find($generateEstimate->id)
                    ]);
                }
        }elseif($request->from_type == 'Work Estimate'){

            $table = 'company_'.$request->company_id.'_technical_tables';
            TechnicalTable::setGlobalTable($table);

            $itemTable = 'company_'.$request->company_id.'_items';
            Item::setGlobalTable($itemTable);

            $technicalEstimate =  TechnicalTable::with('items')->find($request->id);

            if($request->to_type == 'Work Order' || $request->to_type == 'Work Delivery Note'){
                $generateTechnicalEstimate = $technicalEstimate->replicate();
                $generateTechnicalEstimate->created_at = now();
                $generateTechnicalEstimate->reference = $referenceType ;
                $generateTechnicalEstimate->reference_number = get_technical_table_latest_ref_number($request->company_id, $referenceType, 1 );
                $generateTechnicalEstimate->save();
                
                foreach($technicalEstimate->items as $technicalItems){
                    $generateItem = $technicalItems->replicate();
                    $generateItem->created_at = now();
                    $generateItem->type = $referenceType ;
                    $generateItem->parent_id =  $generateTechnicalEstimate->id;
                    $generateItem->save();
                }
        
                return response()->json([
                    'status' => true,
                    'message' => 'Generate successfully',
                    'data' =>  TechnicalTable::with('items')->find($generateTechnicalEstimate->id)
                ]);
            }elseif($request->to_type == 'Ordinary Invoice'){
                $table = 'company_'.$request->company_id.'_invoice_tables';
                InvoiceTable::setGlobalTable($table);

                $array = $technicalEstimate->toArray();
                $generateTechnicalEstimate = InvoiceTable::create($array);
                $generateTechnicalEstimate->created_at = now();
                $generateTechnicalEstimate->reference = $referenceType ;
                $generateTechnicalEstimate->reference_number = get_invoice_table_latest_ref_number($request->company_id, $referenceType, 1 );
                $generateTechnicalEstimate->save();
                
                foreach($technicalEstimate->items as $invoiceItems){
                    $generateInvoiceItem = $invoiceItems->replicate();
                    $generateInvoiceItem->created_at = now();
                    $generateInvoiceItem->type = $referenceType ;
                    $generateInvoiceItem->parent_id =  $generateTechnicalEstimate->id;
                    $generateInvoiceItem->save();
                }
        
                return response()->json([
                    'status' => true,
                    'message' => 'Generate successfully',
                    'data' =>  InvoiceTable::with('items')->find($generateTechnicalEstimate->id)
                ]);
            }
        }elseif($request->from_type == 'Ordinary Invoice'){

            $table = 'company_'.$request->company_id.'_invoice_tables';
            InvoiceTable::setGlobalTable($table);

            $itemTable = 'company_'.$request->company_id.'_items';
            Item::setGlobalTable($itemTable);

            $invoiceEstimate = InvoiceTable::with('items')->find($request->id);

            if($request->to_type == 'Refund Invoice'){
                $generateInvoiceEstimate = $invoiceEstimate->replicate();
                $generateInvoiceEstimate->created_at = now();
                $generateInvoiceEstimate->reference = $referenceType ;
                $generateInvoiceEstimate->reference_number = get_invoice_table_latest_ref_number($request->company_id, $referenceType, 1 );
                $generateInvoiceEstimate->save();
                
                foreach($invoiceEstimate->items as $invoiceItems){
                    $generateItem = $invoiceItems->replicate();
                    $generateItem->created_at = now();
                    $generateItem->type = $referenceType ;
                    $generateItem->parent_id =  $generateInvoiceEstimate->id;
                    $generateItem->save();
                }
        
                return response()->json([
                    'status' => true,
                    'message' => 'Generate successfully',
                    'data' =>  InvoiceTable::with('items')->find($generateInvoiceEstimate->id)
                ]);
            }
        }elseif($request->from_type == 'Purchase Order'){
            $table = 'company_'.$request->company_id.'_purchase_tables';
            PurchaseTable::setGlobalTable($table);
    
            $itemTable = 'company_'.$request->company_id.'_items';
            Item::setGlobalTable($itemTable);

            $purchaseOrderEstimate = PurchaseTable::with('items')->find($request->id);

            if($request->to_type == 'Purchase Delivery Note' || $request->to_type == 'Purchase Invoice'){

                $generatePurchaseEstimate = $purchaseOrderEstimate->replicate();
                $generatePurchaseEstimate->created_at = now();
                $generatePurchaseEstimate->reference = $referenceType ;
                $generatePurchaseEstimate->reference_number = get_purchase_table_latest_ref_number($request->company_id, $referenceType, 1 );
                $generatePurchaseEstimate->save();
                
                foreach($purchaseOrderEstimate->items as $purchaseItems){
                    $generateItem = $purchaseItems->replicate();
                    $generateItem->created_at = now();
                    $generateItem->type = $referenceType ;
                    $generateItem->parent_id =  $generatePurchaseEstimate->id;
                    $generateItem->save();
                }
        
                return response()->json([
                    'status' => true,
                    'message' => 'Generate successfully',
                    'data' =>  PurchaseTable::with('items')->find($generatePurchaseEstimate->id)
                ]);
            }
        }if($request->from_type == 'Incident'){
            $table = 'company_'.$request->company_id.'_technical_incidents';
            TechnicalIncident::setGlobalTable($table);

            $technicalIncident = TechnicalIncident::find($request->id);

            if($request->to_type == 'Work Estimate' || $request->to_type == 'Work Order' || $request->to_type == 'Work Delivery Note'){
                $table = 'company_'.$request->company_id.'_technical_tables';
                TechnicalTable::setGlobalTable($table);

                $array = $technicalIncident->toArray();
                $generateTechnicalIncident = TechnicalTable::create($array);
                $generateTechnicalIncident->created_at = now();
                $generateTechnicalIncident->reference = $referenceType ;
                $generateTechnicalIncident->reference_number = get_technical_table_latest_ref_number($request->company_id, $referenceType, 1 );
                $generateTechnicalIncident->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Generate successfully',
                    'data' =>  TechnicalTable::find($generateTechnicalIncident->id)
                ]);
            }elseif($request->to_type == 'Ordinary Invoice'){
                $table = 'company_'.$request->company_id.'_invoice_tables';
                InvoiceTable::setGlobalTable($table);

                $array = $technicalIncident->toArray();
                $generateInvoice = InvoiceTable::create($array);
                $generateInvoice->created_at = now();
                $generateInvoice->reference = $referenceType ;
                $generateInvoice->reference_number = get_invoice_table_latest_ref_number($request->company_id, $referenceType, 1 );
                $generateInvoice->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Generate successfully',
                    'data' =>  InvoiceTable::find($generateInvoice->id)
                ]);

            }
        }

    }
}
