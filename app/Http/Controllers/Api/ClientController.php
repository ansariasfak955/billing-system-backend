<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientAttachment;
use App\Models\ClientSpecialPrice;
use App\Models\Reference;
use App\Models\Item;
use App\Exports\InvoiceExport;
use App\Models\InvoiceTable;
use App\Models\InvoiceReceipt;
use App\Models\PaymentOption;
use App\Models\Deposit;
use App\Models\PaymentTerm;
use App\Models\DeliveryOption;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(($request->company_id ==  NULL)||($request->company_id ==  0)){
            return response()->json([
                "status" => false,
                "message" =>  "Please select company"
            ]);
        }

        $table = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($table);
        
        $query = Client::query();
        
        //set reference table
        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);
        if($request->type){
            //get dynamic reference
            $refernce_ids = Reference::where('type', urldecode($request->type))->pluck('prefix')->toArray();
            $query = $query->whereIn('reference', $refernce_ids);
        }
        $query = $query->filter($request->all())->get();
        if (!count($query)) {
            return response()->json([
                "status" => false,
                "message" => "No clients found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "clients" =>  $query
            ]);  
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $table = 'company_'.$request->company_id.'_clients';
        $validator = Validator::make($request->all(), [
            'email'     => "required|unique:$table|email",
            'reference' => "required",
            'legal_name' => "required|unique:$table",
            'tin' => "required|alpha_num|unique:$table",
            'bank_account_account' => "sometimes|nullable|unique:$table",
            'phone_1' => "sometimes|nullable|unique:$table",
            'phone_2' => "sometimes|nullable|unique:$table",
            'client_category' => "integer"
        ],[
            'tin.required' => 'Ced/Ruc number is required',
            'tin.unique' => 'Ced/Ruc number must be unique',
            'tin.alpha_num' => 'Ced/Ruc special characters are not allowed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ]);
        }

        Client::setGlobalTable($table);
        if ($request->reference_number == '') {
            $client = Client::create($request->except(['company_id', 'contacts', 'addresses']));
            $client->reference_number = get_client_latest_ref_number($request->company_id, $request->reference, 1);
            $client->save();

            return response()->json([
                "status" => true,
                "client" => $client,
                "message" => "Client created successfully"
            ]);
        } else {
            $client = Client::where('reference', $request->reference)->where('reference_number', $request->reference_number)->first();

            if ($client == NULL) {
                $client = Client::create($request->except(['company_id', 'contacts', 'addresses']));
                $client->reference_number = get_client_latest_ref_number($request->company_id, $request->reference, 0);
                $client->save();

                return response()->json([
                    "status"  => true,
                    "client"  => $client,
                    "message" => "Client created successfully"
                ]);
            } else {
                return response()->json([
                    "status"  => false,
                    "client"  => $client,
                    "message" => "Please choose different reference number"
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Client  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($table);

        $clientTable = 'company_'.$request->company_id.'_client_attachments';
        ClientAttachment::setGlobalTable($clientTable);

        $client = Client::with(['client_attachments'])->where('id', $request->client)->first();

        if($client ==  NULL){
            return response()->json([
                "status" => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "client_address" => $client
        ]);
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
        $table = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($table);
        $validator = Validator::make($request->all(), [
            'tin' => 'required|alpha_num',
            'legal_name' => "required"
        ],[
            'tin.required' => 'Ced/Ruc number is required',
            'tin.alpha_num' => 'Ced/Ruc special characters are not allowed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $client = Client::where('id', $request->client)->first();
        $client->update($request->except('company_id', '_method'));
        $client->save();

        return response()->json([
            "status" => true,
            "client" => $client,
            "message" => "Client updated successfully"
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
        $table = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($table);
        $client = Client::where('id', $request->client)->first();
        if ($client == NULL) {
            return response()->json([
                'status' => false,
                'message' => "Client not exists!"
            ]);
        }

        if($client->delete()){
            return response()->json([
                'status' => true,
                'message' => "Client deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again!"
            ]);
        }
    }

    public function batchDelete(Request $request){
        $table = 'company_'.$request->company_id.'_clients';
        $validator = Validator::make($request->all(),[
            'ids'=>'required',
        ],[
            'ids.required' => 'Please select entry to delete'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        Client::setGlobalTable($table);
        $ids = explode(",", $request->ids);
        Client::whereIn('id', $ids)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Clients deleted successfully'
        ]);
    }
    public function duplicate(Request $request){
        $table = 'company_'.$request->company_id.'_clients';
        $attachmentTable = 'company_'.$request->company_id.'_client_attachments';

        $validator = Validator::make($request->all(),[
            'id'=>'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        
        Client::setGlobalTable($table);
        ClientAttachment::setGlobalTable($attachmentTable);

        $client = Client::with('client_attachments')->find($request->id);
        if(!$client){
            return response()->json([
                'status' => false,
                'message' => 'Client Not found!'
            ]);
        }
        // dd($client->client_attachments);
        $duplicatedClient = $client->replicate();
        $duplicatedClient->created_at = now();
        $duplicatedClient->reference_number = get_client_latest_ref_number($request->company_id, $client->reference, 1);
        $duplicatedClient->save();
        
        foreach($client->client_attachments as $attachment){
            $duplicatedAttachment = $attachment->replicate();
            $duplicatedAttachment->created_at = now();
            $duplicatedAttachment->client_id =  $duplicatedClient->id;
            $duplicatedAttachment->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Duplicate Clients successfully',
            'data' =>  Client::with('client_attachments')->find( $duplicatedClient->id)
        ]);
    }

    public function clientBalance(Request $request){
        $table = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($table);

        $invoiceReceiptTable = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($invoiceReceiptTable);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $deposit = 'company_'.$request->company_id.'_deposits';
        Deposit::setGlobalTable($deposit);
        // set reference table
        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);
        //get dynamic reference
        $refernce_ids = Reference::where('type', 'Ordinary Invoice')->pluck('prefix')->toArray();
        $refernce_id = Reference::where('type', 'Refund Invoice')->pluck('prefix')->toArray();

        $invoiceBalance = InvoiceTable::with('items')->where('client_id', $request->client_id)->whereIn('reference', $refernce_ids)->get()->sum('amount');
        $invoiceRefundBalance = InvoiceTable::with('items')->where('client_id', $request->client_id)->whereIn('reference', $refernce_id)->get()->sum('amount');
        $invoiceTotalBalance = InvoiceTable::with('items')->where('client_id', $request->client_id)->get()->sum('amount');
        $deposit = Deposit::where('client_id', $request->client_id)->where('type','deposit')->sum('amount');
        $invoiceWithdraw = Deposit::where('client_id', $request->client_id)->where('type','withdraw')->sum('amount');
        // dd($invoiceWithdraw);
        $data = [
                "unpaid_invoices" => "$ ". number_format($invoiceBalance, 2), 
                "unpaid_refunds" => "$ ". number_format($invoiceRefundBalance, 2), 
                "available_balance" => "$ ". number_format($deposit - $invoiceWithdraw, 2),  
                "total_balance" => "$ ". number_format(($invoiceTotalBalance - $deposit) + $invoiceWithdraw , 2)
            ];
        

        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
         
    }
    public function unpaidInvoices(Request $request){
        $table = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        // set reference table
        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);
        //get dynamic reference
        $refernce_ids = Reference::where('type', 'Ordinary Invoice')->pluck('prefix')->toArray();

        $unpaidInvoice = InvoiceTable::where('client_id', $request->client_id)->whereIn('reference', $refernce_ids)->get();

        return response()->json([
            "status" => true,
            "data" =>  $unpaidInvoice
        ]);
        
    }
    public function unpaidRefund(Request $request){
        $table = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        // set reference table
        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);
        //get dynamic reference
        $refernce_id = Reference::where('type', 'Refund Invoice')->pluck('prefix')->toArray();

        $unpaidRefund = InvoiceTable::where('client_id', $request->client_id)->whereIn('reference', $refernce_id)->get();

        return response()->json([
            "status" => true,
            "data" =>  $unpaidRefund
        ]);
    }
    public function exportReceipt(Request $request, $company_id){
        $validator = Validator::make($request->All(), [
            'ids' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
               'status' => false,
               'message' => $validator->errors()->first() 
            ]);
        }
        $table = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($table);
        $table = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($table);
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
        $invoices = InvoiceTable::with('client','payment_options','payment_terms','delivery_options')->whereIn('id', $ids)->get();
        Excel::store(new InvoiceExport($invoices), 'public/xlsx/'.$fileName);

        return response()->json([
            'status' => true,
            'url' => url('/storage/xlsx/'.$fileName),
         ]); 
    }
}
