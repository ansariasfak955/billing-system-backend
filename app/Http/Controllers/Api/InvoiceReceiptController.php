<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvoiceReceipt;
use App\Models\InvoiceTable;
use App\Models\PaymentOption;
use App\Models\Item;
use App\Models\Client;
use App\Models\Supplier;
use App\Models\ItemMeta;
use App\Models\Company;
use App\Exports\InvoiceReceiptExport;
use Maatwebsite\Excel\Facades\Excel;
use App;
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
        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTable);
        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);
        $clientTable = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientTable);
        $query = InvoiceReceipt::query();

        if($request->invoice_id){
            $query =  $query->where('invoice_id', $request->invoice_id);
        }
        if($request->type){
            $query =  $query->where('type', $request->type);
        }

        $query = $query->with('invoice','client','items')->filter($request->all())->get();
        if(!count($query)){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "invoice_receipts" =>  $query
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
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'expiration_date' => $request->expiration_date,
                'paid' => isset($invoiceId['paid']) ? $invoiceId['paid'] : 0,
                'paid_by' => $request->paid_by,
                'type' => 'INV'
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
        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTable);
        $invoice = InvoiceReceipt::with('invoice')->where('id', $request->invoice_receipt)->first();

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

        if($request->amount <= 0){
            return response()->json([
                "status" => false,
                "message" => 'The amount of the receipt cannot be equal to 0'
            ]);
        }

        $table = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($table);
        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTable);
        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);
        $invoiceReceipt = InvoiceReceipt::where('id', $request->invoice_receipt)->first();
        $invoice  = InvoiceTable::find($invoiceReceipt->invoice_id);

        $fullAmount = 0;
        $CreateRec = 1;
        $paid = '0';
        $amountToCut = $request->amount ?? 0;
        if(round($request->amount,2) == round($invoice->amount_due, 2)){
            if($request->paid){

                $CreateRec = 0;
                $paid = '1';
            }

            $amountToCut = 0;
            $fullAmount =  1;
        }
        if( (!$invoice->amount_due || $invoice->amount_due == 0.00) && $request->amount && $request->paid){
            return response()->json([
                "status" => false,
                "message" => 'All pending amount is already  been paid!'
            ]);
        }
        if($fullAmount){
            $status =  'paid';
        }else{
            $status =  'partially paid';
        }
        // return [$CreateRec, $amountToCut, $status];
        $amount = $invoiceReceipt->amount;
        // $invoiceReceipt->amount = $amount-$amountToSub;
        if( round($amount,2) - round($request->amount, 2) != 0.00){

            $invoiceReceipt->amount = $amount-$amountToCut;
        }else{
            $CreateRec = 0;
            $fullAmount = 1;
        }
        if($request->paid){
            if($fullAmount){

                $invoiceReceipt->paid = '1';
            }
            if($invoice){

                $invoice->status =  $status;
                $invoice->save();
            }
        }
        $invoiceReceipt->save(); 
        $receipt =null;
        $paidRec = '0';
        if($request->paid){
            $paidRec = '1';
        }
        if($CreateRec){
            $receipt = InvoiceReceipt::create([
                'invoice_id' => $invoiceReceipt->invoice_id,
                'concept' => $request->concept,
                'payment_option' => $request->payment_option,
                'bank_account' => $request->bank_account,
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'expiration_date' => $request->expiration_date,
                'paid' => $paidRec,
                'paid_by' => $request->paid_by,
                'type' => 'INV'
            ]);
        }
        $id =  ($receipt ? $receipt->id : $invoiceReceipt->id);

        // if (!$request->paid) {
        //     $receipt->paid = '0';
        //     $receipt->save();
        // }
        return response()->json([
            "status" => true,
            "data" => InvoiceReceipt::with('invoice')->find($id)
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
        $table = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($table);
        $request['paid'] = '1';
        InvoiceReceipt::whereIn('id', $idsArr)->update($request->except(['ids', 'company_id']));

        return response()->json([
            'status' => true,
            'message' => "Operation Successful!"
        ]);
    }
    public function download(Request $request){

        $validator = Validator::make($request->all(),[
            'ids' => 'required',                  
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $company = Company::where('id', $request->company_id)->first();

        $pdf = App::make('dompdf.wrapper');
        $idsArr = explode(',', $request->ids);
        $table = 'company_'.$request->company_id.'_invoice_receipts';
        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        $clientTable = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientTable);
        InvoiceTable::setGlobalTable($invoiceTable);
        InvoiceReceipt::setGlobalTable($table);
        foreach($idsArr as $id){
            $receipt = InvoiceReceipt::with(['invoice', 'client'])->find($id);
            if($request->view == 1){
    
                return view('pdf.receipt', compact('receipt', 'company'));
            }
            $pdf->loadView('pdf.receipt', compact('receipt', 'company'));
            return   $pdf->stream();
        }
        // return response()->json([
        //     'status' => true,
        //     'message' => "Operation Successful!",
        //     'data' => $pdfs
        // ]);
    }
    public function createSubReceipt(Request $request){
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        $table = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($table);

        $invoice = InvoiceReceipt::where('id', $request->invoice_id)->first();
        $amount = $invoice->amount;
        $invoice->amount = $amount-($request->amount ?? 0);
        $invoice->save();
        // $invoice->update($request->except('company_id'));

        $receipt = InvoiceReceipt::create([
            'invoice_id' => $invoice->id,
            'concept' => $request->concept,
            'payment_option' => $request->payment_option,
            'bank_account' => $request->bank_account,
            'payment_date' => $request->payment_date,
            'amount' => $request->amount,
            'expiration_date' => $request->expiration_date,
            'paid' => isset($invoice['paid']) ? $invoice['paid'] : 0,
            'paid_by' => $request->paid_by
        ]);

        return response()->json([
            "status" => true,
            "data" => $receipt
        ]);

    }
    public function receiptExport(Request $request, $company_id){
        $validator = Validator::make($request->all(),[
            'ids' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        $table = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($table);
        $client = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($client);
        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTable);
        $paymentOptions = 'company_'.$request->company_id.'_payment_options';
        PaymentOption::setGlobalTable($paymentOptions);

        $fileName = 'Receipts-'.time().$company_id.'.xlsx';
        $ids = explode(',', $request->ids);
        $invoiceReceipts = InvoiceReceipt::with('client','invoice','payment_options')->whereIn('id', $ids)->get();
        Excel::store(new InvoiceReceiptExport($invoiceReceipts), 'public/xlsx/'.$fileName);

        return response()->json([
            'status' => true,
            'url' => url('/storage/xlsx/'.$fileName),
        ]);
    }
}
