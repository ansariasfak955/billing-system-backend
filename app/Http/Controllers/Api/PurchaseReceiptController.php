<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseReceipt;
use App\Models\PurchaseTable;
use App\Models\Item;
use App\Models\PaymentOption;
use App\Models\ItemMeta;
use App\Models\Supplier;
use App\Models\Company;
use App\Exports\PurchaseReceiptExport;
use Maatwebsite\Excel\Facades\Excel;
use App;
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
        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);
        if($request->type){
            $query =  $query->where('type', $request->type);
        }

        $query = $query->with('invoice','supplier','items')->filter($request->all())->get();
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
        $purchaseTable = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($purchaseTable);
        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);
 
        $purchaseReceipt = PurchaseReceipt::where('id', $request->purchase_receipt)->first();
        
        $purchase  = PurchaseTable::find($purchaseReceipt->purchase_id);

        $fullAmount = 0;
        $CreateRec = 1;
        $paid = '0';
        $amountToCut = $request->amount ?? 0;
        if(round($request->amount,2) == round($purchase->amount_due, 2)){
            if($request->paid){

                $CreateRec = 0;
                $paid = '1';
            }

            $amountToCut = 0;
            $fullAmount =  1;
        }
        if( (!$purchase->amount_due || $purchase->amount_due == 0.00) && $request->amount){
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
        $amount = $purchaseReceipt->amount;
        // $purchaseReceipt->amount = $amount-$amountToSub;
        if( round($amount,2) - round($request->amount, 2) != 0.00){

            $purchaseReceipt->amount = $amount-$amountToCut;
        }else{
            $CreateRec = 0;
            $fullAmount = 1;
        }
        if($request->paid){
            if($fullAmount){

                $purchaseReceipt->paid = '1';
            }
            if($purchase){

                $purchase->status =  $status;
                $purchase->save();
            }
        }
        $purchaseReceipt->save(); 
        $receipt =null;
        $paidRec = '0';
        if($request->paid){
            $paidRec = '1';
        }
        if($CreateRec){
            $receipt = PurchaseReceipt::create([
                'purchase_id' => $purchaseReceipt->purchase_id,
                'concept' => $request->concept,
                'payment_option' => $request->payment_option,
                'bank_account' => $request->bank_account,
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'expiration_date' => $request->expiration_date,
                'paid' => $paidRec,
                'paid_by' => $request->paid_by
            ]);
        }
        $id =  ($receipt ? $receipt->id : $purchaseReceipt->id);
        return response()->json([
            "status" => true,
            "data" => PurchaseReceipt::with('invoice','items')->where('id', $request->purchase_receipt)->first()
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

    public function download(Request $request){

        $validator = Validator::make($request->all(),[
            'ids' => 'required',                  
        ]);
        $company = Company::where('id', $request->company_id)->first();
        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $pdf = App::make('dompdf.wrapper');
        $idsArr = explode(',', $request->ids);
        $table = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($table);
        $receipt_table = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($receipt_table);
        $clientTable = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($clientTable);
        foreach($idsArr as $id){
            $receipt = PurchaseReceipt::with(['invoice', 'client'])->find($id);
            if($request->view == 1){
    
                return view('pdf.receipt', compact('receipt', 'company'));
            }
            $pdf->loadView('pdf.receipt', compact('receipt', 'company'));
            return   $pdf->stream();
        }
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

        $table = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($table);
        $purchaseTable = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($purchaseTable);
        $purchaseReceipt = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($purchaseReceipt);
        $paymentOptions = 'company_'.$request->company_id.'_payment_options';
        PaymentOption::setGlobalTable($paymentOptions);

        $fileName = 'Receipts-'.time().$company_id.'.xlsx';
        $ids = explode(',', $request->ids);
        $purchaseReceipts = PurchaseReceipt::with('invoice','supplier','payment_options')->whereIn('id', $ids)->get();
        Excel::store(new PurchaseReceiptExport($purchaseReceipts), 'public/xlsx/'.$fileName);

        return response()->json([
            'status' => true,
            'url' => url('/storage/xlsx/'.$fileName),
        ]);
    }
}
