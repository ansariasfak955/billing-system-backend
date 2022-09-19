<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Client;
use App\Models\InvoiceTable;
use App\Models\SalesEstimate;
use App\Models\TechnicalTable;
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

        $historyItems= Item::where('reference_id', $request->id)->where('reference', $request->reference)->groupBy('parent_id')->get();

        if( $historyItems->count() ){
            return response()->json([
            'success' => true,
            'data' => $historyItems,
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => [],
            'message' => 'No data found!',
        ]);

    }
}
