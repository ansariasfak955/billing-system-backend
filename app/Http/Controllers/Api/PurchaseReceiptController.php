<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseReceipt;
use App\Models\PurchaseTable;
use App\Models\Item;
use App\Models\ItemMeta;
use App\Models\Supplier;
use Validator;
use Storage;

class PurchaseReceiptController extends Controller
{
    public function index( Request $request )
    {
        if(($request->company_id ==  NULL)||($request->company_id ==  0)){
            return response()->json([
                "status" => false,
                "message" =>  "Please select company"
            ]);
        }

        $table = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($table);
        $purchase_table = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($purchase_table);
        $supplier_table = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($supplier_table);
        $query = PurchaseReceipt::query();

        if($request->purchase_id){
            $query =  $query->where('purchase_id', $request->purchase_id);
        }
        if($request->type){
            $query =  $query->where('type', $request->type);
        }

        $query = $query->with('invoice')->filter($request->all())->get();
        if(!count($query)){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "purchase_receipts" =>  $query
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'purchase_ids' => 'required',                  
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($table);
        $receipt_table = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($receipt_table);

        $receiptIds = explode(',' , $request->purchase_ids);

        foreach($receiptIds as $receiptId){

            PurchaseReceipt::create([
                'purchase_id' => $receiptId,
                'concept' => $request->concept,
                'payment_option' => $request->payment_option,
                'bank_account' => $request->bank_account,
                'payment_option' => $request->payment_option,
                'payment_date' => $request->payment_date
            ]);
            PurchaseTable::where('id', $receiptId)->update([
                'status' => 'paid'
            ]);
        }

        return response()->json([
            "status" => true,
            "message" => "Purchase receipt created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($table);
        $receipt = PurchaseReceipt::where('id', $request->purchase_receipt)->first();

        if($receipt ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "Purchase" => $receipt
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $table = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($table);
        $receipt = PurchaseReceipt::where('id', $request->purchase_receipt)->first();
        
        $receipt->update($request->except('company_id', '_method'));


        return response()->json([
            "status" => true,
            "Purchase" => $receipt
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $table = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($table);
        $receipt = PurchaseReceipt::where('id', $request->purchase_receipt)->first();
        if($receipt->delete()){

            return response()->json([
                    'status' => true,
                    'message' => "Deleted successfully!"
            ]);
        } else {
            return response()->json([
                    'status' => false,
                    'message' => "Retry deleting again! "
            ]);
        }
    }

    public function bulkPay(Request $request){

        $validator = Validator::make($request->all(),[
            'ids' => 'required',                  
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $idsArr = explode(',', $request->ids);
        $table = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($table);
        $request['paid'] = '1';
        PurchaseReceipt::whereIn('id', $idsArr)->update($request->except(['ids', 'company_id']));

        return response()->json([
            'status' => true,
            'message' => "Operation Successful!"
        ]);
    }
}
