<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvoiceReceipt;
use App\Models\InvoiceTable;
use App\Models\Item;
use App\Models\ItemMeta;
use Validator;
use Storage;
class InvoiceReceiptController extends Controller
{
    public function index( Request $request )
    {
        if(($request->company_id ==  NULL)||($request->company_id ==  0)){
            return response()->json([
                "status" => false,
                "message" =>  "Please select company"
            ]);
        }
        $table = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($table);

        if($request->invoice_id == NULL){
            if(InvoiceReceipt::count() == 0){
                return response()->json([
                    "status" => false,
                    "message" =>  "No data found"
                ]);
            }
            return response()->json([
                "status" => true,
                "invoice_receipts" =>  InvoiceReceipt::get()
            ]);
        }

        if(InvoiceReceipt::where('invoice_id', $request->invoice_id)->count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "invoice_receipts" =>  InvoiceReceipt::where('invoice_id', $request->invoice_id)->get()
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
            'invoice_ids' => 'required',                  
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($table);
        $invoice_table = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoice_table);

        $invoiceIds = explode(',' , $request->invoice_ids);

        foreach($invoiceIds as $invoiceId){

            InvoiceReceipt::create([
                'invoice_id' => $invoiceId,
                'concept' => $request->concept,
                'payment_option' => $request->payment_option,
                'bank_account' => $request->bank_account,
                'payment_option' => $request->payment_option,
                'payment_date' => $request->payment_date
            ]);
            InvoiceTable::where('id', $invoiceId)->update([
                'status' => 'paid'
            ]);
        }

        return response()->json([
            "status" => true,
            "message" => " Invoice created successfully"
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
        $table = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($table);
        $invoice = InvoiceReceipt::where('id', $request->invoice_receipt)->first();

        if($invoice ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "invoice" => $invoice
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
        $table = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($table);
        $invoice = InvoiceReceipt::where('id', $request->invoice_receipt)->first();
        
        $invoice->update($request->except('company_id', '_method'));


        return response()->json([
            "status" => true,
            "invoice" => $invoice
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
        $table = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($table);
        $invoice = InvoiceReceipt::where('id', $request->invoice_receipt)->first();
        if($invoice->delete()){

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
}