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
    public function overview(){

    }

    public function invoicing(Request $request){
        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables);

        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTable);

        $client_ids  = Client::orderBy('id', 'desc')->take(2)->pluck('id')->toArray();

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $invoiceDatas = InvoiceTable::with(['items', 'itemMeta'])->where('client_id',$client_ids)->get(); 
    
        $sum = 0;

        foreach($invoiceDatas as $invoiceData){
           $sum = $sum + $invoiceData->items()->sum('base_price');
        }

        return $sum;

        //return response()->json([
            //"status" => true,
            //"data" =>  $sum
        //]);
    }
    
    public function cashFlow(){

    }

    public function sales(){

    }

    public function technicalService(){

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
