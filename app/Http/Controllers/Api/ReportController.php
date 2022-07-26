<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Item;
use App\Models\ItemMeta;
use App\Models\InvoiceTable;
use App\Models\PaymentOption;
use App\Models\SalesAttachment;
use App\Models\SalesEstimate;
use App\Models\PurchaseTable;
use App\Models\TechnicalTable;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // public function overview(Request $request){
    //     $clientsTables = 'company_'.$request->company_id.'_clients';
    //     Client::setGlobalTable($clientsTables); 

    //     $purchaseTables = 'company_'.$request->company_id.'_purchase_tables';
    //     PurchaseTable::setGlobalTable($purchaseTables); 

    //     $itemTable = 'company_'.$request->company_id.'_items';
    //     Item::setGlobalTable($itemTable);

    //     $item_meta_table = 'company_'.$request->company_id.'_item_metas';
    //     ItemMeta::setGlobalTable($item_meta_table);

    //     $paids = PurchaseTable::where('status', 'paid')->get();
    //     $unpaids = PurchaseTable::where('status', 'unpaid')->get();
    //     $invoiceds = PurchaseTable::where('status', 'invoiced')->get();
        
    //     $data = [];
    //     $client_ids  = Client::pluck('id')->toArray();
        
    //     foreach($paids as $paid){
    //         $purchaseDatas = PurchaseTable::with(['items', 'itemMeta'])->where('id', $client_id)->get();
    //         $sum = 0;
    //         $arr['client_id'] = $client_id;
    //         foreach($purchaseDatas as $purchaseData){
    //            $sum = $sum + $purchaseData->items()->sum('base_price');
    //         }
    //         $arr['sum'] = $sum;
    //         $data[] = $arr;
    //     }

    //     foreach($unpaids as $unpaid){
    //         $purchaseDatas = PurchaseTable::with(['items', 'itemMeta'])->where('id', $client_id)->get();
    //         $sum = 0;
    //         $arr['client_id'] = $client_id;
    //         foreach($purchaseDatas as $purchaseData){
    //            $sum = $sum + $purchaseData->items()->sum('base_price');
    //         }
    //         $arr['sum'] = $sum;
    //         $data[] = $arr;
    //     }
    // }

    public function invoicing(Request $request){
        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables); 

        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTable);
        
        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);
        
        if($request->type == "agent"){
            $agent_ids  = InvoiceTable::pluck('agent_id')->toArray();        
            $client_ids = Client::whereIn('id', $agent_ids)->pluck('id')->toArray();
        }else{
            $client_ids  = Client::pluck('id')->toArray();
        }

        $data = [];
        foreach($client_ids as $client_id){
            $invoiceDatas = InvoiceTable::with(['items', 'itemMeta'])->where('client_id', $client_id)->get();
            $sum = 0;
            $arr['client_id'] = $client_id;
            foreach($invoiceDatas as $invoiceData){
               $sum = $sum + $invoiceData->items()->sum('base_price');
            }
            $arr['sum'] = $sum;
            $data[] = $arr;
        }

        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
    }
    
    public function cashFlow(){

    }

    public function sales( Request $request ){
        $salesTables = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($salesTables); 

        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables); 

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        if( $request->type == "overview" ){
            $data['pending'] = PurchaseTable::where('status', 'pending')->count();
            $data['closed'] = PurchaseTable::where('status', 'closed')->count();
            $data['resolved'] = PurchaseTable::where('status', 'resolved')->count();
            $data['refused'] = PurchaseTable::where('status', 'refused')->count();
            return $data;

        }elseif( $request->type == "agent" ){
            $agent_ids  = PurchaseTable::pluck('agent_id')->toArray();
            $client_ids  = Client::whereIn('id', $agent_ids)->pluck('id')->toArray();

        }else{
            $client_ids  = Client::pluck('id')->toArray();
        }
        $data = [];
        foreach($client_ids as $client_id){
            $purchaseDatas = PurchaseTable::with(['items', 'itemMeta'])->where('id', $client_id)->get();
            $sum = 0;
            $arr['client_id'] = $client_id;
            foreach($purchaseDatas as $purchaseData){
               $sum = $sum + $purchaseData->items()->sum('base_price');
            }
            $arr['sum'] = $sum;
            $data[] = $arr;
        }

        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);

    }

    public function technicalService( Request $request ){
        $technicalTables = 'company_'.$request->company_id.'_technical_tables';
        TechnicalTable::setGlobalTable($technicalTables); 

        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables); 

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        if( $request->type == "overview" ){
            $data['pending'] = TechnicalTable::where('status', 'pending')->count();
            $data['closed'] = TechnicalTable::where('status', 'closed')->count();
            $data['resolved'] = TechnicalTable::where('status', 'resolved')->count();
            $data['refused'] = TechnicalTable::where('status', 'refused')->count();
            return $data;

        }elseif( $request->type == "agent" ){
            $agent_ids  = TechnicalTable::pluck('agent_id')->toArray();
            $client_ids  = Client::whereIn('id', $agent_ids)->pluck('id')->toArray();

        }else{
            $client_ids  = Client::pluck('id')->toArray();
        }

        $data = [];
        foreach($client_ids as $client_id){
            $technicalDatas = TechnicalTable::with(['items', 'itemMeta'])->where('client_id', $client_id)->get();
            $sum = 0;
            $arr['client_id'] = $client_id;
            foreach($technicalDatas as $technicalData){
               $sum = $sum + $technicalData->items()->sum('base_price');
            }
            $arr['sum'] = $sum;
            $data[] = $arr;
        }

        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
    }

    public function purchases(){

    }

    public function stockValuation(){

    }

    public function ofEvolution(){

    }

    public function taxSummary(){

    }
}
