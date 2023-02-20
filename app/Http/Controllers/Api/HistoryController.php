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
use App\Models\DeliveryOption;
use App\Models\PaymentOption;
use App\Models\PurchaseReceipt;
use App\Models\PaymentTerm;
use App\Models\Supplier;
use App\Models\Reference;
use App\Models\ItemMeta;
use App\Exports\ExpenseExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

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
        
        $table = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($table);

        $itemMetaTable = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($itemMetaTable);

        $referenceArr = ['SE','WE','INV','PO'];
        $data = [];
        foreach($referenceArr as $type){
            $arr = [];
            $items = [];

            if($type == 'SE'){

                $items = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($request) {
                    $query->where('reference_id', $request->id)->where('reference',   $request->reference);
                })->get();

            }elseif($type == 'WE'){
                $items = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($request) {
                    $query->where('reference_id', $request->id)->where('reference',   $request->reference);
                })->get();
            } 
            elseif($type == 'INV'){
                $items = InvoiceTable::with(['items'])->WhereHas('items', function ($query) use ($request) {
                    $query->where('reference_id', $request->id)->where('reference',   $request->reference);
                })->get();
            }
            elseif($type == 'PO'){
                
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
                    $arr['type'] = $item->reference_type;
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
    public function expenseHistory(Request $request){

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

        $table = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($table);
        
        $table = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($table);

        $invoiceReceiptTable = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($invoiceReceiptTable);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        //set reference table
        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);

        $referenceType = Reference::where('type', 'Purchase Invoice')->get()->toArray();
        // dd($referenceType);
        $referenceArr = ['PO'];
        $data = [];
        foreach($referenceArr as $type){
            $arr = [];
            $items = [];

            if($type == 'PO'){
                
                $items = PurchaseTable::with(['items'])->WhereHas('items', function ($query) use ($request) {
                    $query->where('reference_id', $request->id)->where('reference',   $request->reference);
                })->get();
            }
            if(count($items)){

               foreach($items as $item){
                    $arr['id'] = $item->id;
                    $arr['reference_number'] = $item->reference_number;
                    $arr['reference'] = $item->reference;
                    $arr['supplier'] = $item->supplier_name;
                    $arr['title'] = $item->title;
                    $arr['created_by'] = $item->created_by;
                    $arr['status'] = $item->status;
                    $arr['type'] = $item->reference_type;
                    $arr['date'] = $item->date;
                    // $arr['amount'] = $item->items->where('reference_id', $request->id)->where('reference',   $request->reference)->sum('amount');
                    $arr['activity'] = '';

                    $amount = $item->items->where('reference_id', $request->id)->where('reference',   $request->reference)->sum('amount');

                    $totalPrice = $item->items->where('reference_id', $request->id)->where('reference',   $request->reference)->sum('subtotal');

                    $unitPrice = 0;

                    $quantity = $item->items->where('reference_id', $request->id)->where('reference',   $request->reference)->sum('quantity');

                    if($totalPrice && $quantity){

                        $unitPrice = $totalPrice/$quantity;
                    }
                    $arr['amount'] = number_format($amount,2);
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
    public function expenseHistoryExport(Request $request, $company_id){
        $validator = Validator::make($request->All(), [
            'ids' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
               'status' => false,
               'message' => $validator->errors()->first() 
            ]);
        }
        $table = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($table);
        $table = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($table);
        $paymentOption = 'company_'.$request->company_id.'_payment_options';
        PaymentOption::setGlobalTable($paymentOption);
        $table = 'company_'.$request->company_id.'_payment_terms';
        PaymentTerm::setGlobalTable($table);
        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);
        $table = 'company_'.$request->company_id.'_delivery_options';
        DeliveryOption::setGlobalTable($table);

        $fileName = 'invoices-'.time().$company_id.'.xlsx';
        $ids = explode(',', $request->ids);
        $expenseHistorys = PurchaseTable::with('items','supplier','payment_options','payment_terms','delivery_options','category')->whereIn('id', $ids)->get();
        Excel::store(new ExpenseExport($expenseHistorys), 'public/xlsx/'.$fileName);

        return response()->json([
            'status' => true,
            'url' => url('/storage/xlsx/'.$fileName),
         ]); 
    }
}
