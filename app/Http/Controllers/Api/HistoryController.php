<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Client;
use App\Models\InvoiceTable;
use App\Models\SalesEstimate;
use App\Models\TechnicalTable;
use App\Models\PurchaseTable;
use App\Models\Item;
use App\Models\ItemMeta;

class HistoryController extends Controller
{
    public function getHistory(Request $request){

        if(($request->company_id ==  NULL)||($request->company_id ==  0)){
            return response()->json([
                "status" => false,
                "message" =>  "Please select company"
            ]);
        }

        $validator = Validator::make($request->all(),[
            'id' => 'required',
            'reference' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        //setup tables
        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables); 

        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTable);

        $salesTable = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($salesTable);

        $technicalTable = 'company_'.$request->company_id.'_technical_tables';
        TechnicalTable::setGlobalTable($technicalTable);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $itemMetaTable = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($itemMetaTable);

        $referenceArr = ['SE', 'SO', 'SDN', 'WE', 'WO', 'WDN', 'INV', 'RET', 'PO', 'PDN', 'PINV'];
        $data = [];
        foreach($referenceArr as $type){
            $arr = [];
            $items = [];

            if($type == 'SE' || $type == 'SO' || $type == 'SDN'){

                $items = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($request) {
                    $query->where('reference_id', $request->id)->where('reference',   $request->reference);
                })->get();

            }elseif($type == 'WE' || $type == 'WO' || $type == 'WDN'){
                $items = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($request) {
                    $query->where('reference_id', $request->id)->where('reference',   $request->reference);
                })->get();
            } 
            elseif($type == 'WE' || $type == 'WO' || $type == 'WDN'){
                $items = InvoiceTable::with(['items'])->WhereHas('items', function ($query) use ($request) {
                    $query->where('reference_id', $request->id)->where('reference',   $request->reference);
                })->get();
            }
            elseif($type == 'PO' || $type == 'PDN' || $type == 'PINV'){
                
                $items = PurchaseTable::with(['items'])->WhereHas('items', function ($query) use ($request) {
                    $query->where('reference_id', $request->id)->where('reference',   $request->reference);
                })->get();
            }
            if(count($items)){

               foreach($items as $item){
                    $arr['id'] = $item->id;
                    $arr['reference_number'] = $item->reference_number;
                    $arr['reference'] = $item->reference;
                    $arr['client'] = $item->client_name;
                    $arr['title'] = $item->title;
                    $arr['created_by'] = $item->created_by;
                    $arr['status'] = $item->status;
                    $arr['type'] = '';
                    $arr['date'] = $item->date;
                    $arr['amount'] = $item->items->where('reference_id', $request->id)->where('reference',   $request->reference)->sum('amount');
                    $arr['activity'] = '';

                    $totalPrice = $item->items->where('reference_id', $request->id)->where('reference',   $request->reference)->sum('subtotal');

                    $unitPrice = 0;

                    $quantity = $item->items->where('reference_id', $request->id)->where('reference',   $request->reference)->sum('quantity');

                    if($totalPrice && $quantity){

                        $unitPrice = $totalPrice/$quantity;
                    }
                    $arr['unit_price'] = $unitPrice;
                    $arr['quantity'] = $quantity;
                    $arr['product_total'] = $totalPrice;

                    $data[] = $arr;
               }
            }
        }

        if( !empty($data) ){
            return response()->json([
            'success' => true,
            'data' => $data,
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => [],
            'message' => 'No data found!',
        ]);

    }
}