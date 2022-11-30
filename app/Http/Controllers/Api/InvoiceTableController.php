<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvoiceTable;
use App\Models\InvoiceReceipt;
use App\Models\Item;
use App\Models\ItemMeta;
use App\Models\Client;
use Validator;
use App\Jobs\SendInvoiceMail;
use Storage;

class InvoiceTableController extends Controller
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

        if(!$request->type){
            return response()->json([
                "status" => false,
                "message" =>  "Please select type"
            ]);
        }

        $table = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($table);
        $clientTable = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientTable);

        // $invoice = InvoiceTable::where('reference', $request->type)->get();

        $query = InvoiceTable::query();

        if($request->type){
            $query = $query->where('reference', $request->type);
        }
        if($request->search){
            $query = $query->where('reference_number', 'like', '%'.$request->search.'%')->orWhereHas('client', function($q) use ($request){
                $q->where('name',  'like','%'.$request->search.'%');
            });
        }

        $invoice = $query->get();

        if( !$request->invoice_id ){

            if($invoice->count() == 0) {
                return response()->json([
                    "status" => false,
                    "message" => "No data found!"
                ]);
            } else {
                return response()->json([
                    "status" => true,
                    "data" =>  $invoice
                ]);  
            }
        }else{
            $invoice = InvoiceTable::where('reference', $request->type)->where('id', $request->invoice_id)->first();
            if( $invoice ) {
                return response()->json([
                    "status" => false,
                    "message" => "No data found!"
                ]);
            } else {
                return response()->json([
                    "status" => true,
                    "data" =>  $invoice
                ]);  
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'client_id' => 'required',
            'reference' => 'required',
        ], [
            'client_id.required' => 'Please select client.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $invoiceReceiptTable = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($invoiceReceiptTable);
        //change format of date
        if($request->date){

            $request['date'] = get_formatted_datetime($request->date);
        }

        if($request->assigned_date){

            $request['assigned_date'] = get_formatted_datetime($request->assigned_date);
        }

        if($request->start_date){

            $request['start_date'] = get_formatted_datetime($request->start_date);
        }

        if($request->end_date){

            $request['end_date'] = get_formatted_datetime($request->end_date);
        }
        if($request->due_date){

            $request['due_date'] = get_formatted_datetime($request->due_date);
        }
        if($request->valid_until){

            $request['valid_until'] = get_formatted_datetime($request->valid_until);
        }
        if($request->sent_date){

            $request['sent_date'] = get_formatted_datetime($request->sent_date);
        }
        if ($request->reference_number == '') {
            $request['reference_number'] = get_invoice_table_latest_ref_number($request->company_id, $request->reference, 1 );
        }else{

            $invoice = InvoiceTable::where('reference', $request->reference)->where('reference_number', $request->reference_number)->first();

            if ($invoice) {
                $request->reference_number = '';
            }
        }

        if( $request->reference_number  ){
            $invoice = InvoiceTable::create($request->except('company_id'));
            $invoice->created_by = \Auth::id();
            $invoice->save();
            // dd($request->all());
            if($request->item){
                $items = json_decode($request->item, true);

               $meta_discount    = $request->meta_discount;
               $meta_income_tax  = $request->meta_income_tax;
               //save item meta
               if ($meta_discount) {

                   ItemMeta::create([
                       'reference_id'  => $invoice->id,
                       'parent_id'     => $invoice->id,
                       'discount'      => $meta_discount,
                       'income_tax'    => $meta_income_tax
                   ]);
               }
               // items
               foreach ($items as $item) {
                   $reference = $item['reference'];
                   if (isset($item['reference_id'])) {
                       $reference_id = $item['reference_id'];
                   } else {
                       $reference_id = NULL;
                   }
                   
                   $name             = isset($item['name']) ? $item['name'] : "";
                   $parent_id        = $invoice->id;
                   $type             = $invoice->reference;
                   $description      = isset($item['description']) ? $item['description'] : "";
                   $base_price       = isset($item['base_price']) ? $item['base_price'] : 0;
                   $quantity         = isset($item['quantity']) ? $item['quantity'] : 1;
                   $discount         = isset($item['discount']) ? $item['discount'] : 0;
                   $tax              = isset($item['tax']) ? $item['tax'] : 0;
                   $income_tax       = isset($item['income_tax']) ? $item['income_tax'] : 0;
                   $subtotal        = isset($item['subtotal']) ? $item['subtotal'] : 0;
                   $meta_discount    = isset($item['meta_discount']) ? $item['meta_discount'] : 0;
                   $meta_income_tax  = isset($item['meta_income_tax']) ? $item['meta_income_tax'] : 0;
                   $vat              = isset($item['vat']) ? $item['vat'] : 0;
                   $createdItem = Item::create([
                       'reference'     => $reference,
                       'reference_id'  => $reference_id,
                       'parent_id'     => $parent_id,
                       'type'          => $type,
                       'name'          => $name,
                       'description'   => $description,
                       'base_price'    => $base_price,
                       'quantity'      => $quantity,
                       'discount'      => $discount,
                       'tax'           => $tax,
                       'income_tax'    => $income_tax,
                       'subtotal'     => $subtotal,
                       'vat'           => $vat
                   ]);
                }
                $insertedInvoice = InvoiceTable::with(['items', 'item_meta'])->find($invoice->id);
                $status = ($request->status == 'paid') ? '1' : '0';
                if($request->payment_term == 'immediate'){

                    InvoiceReceipt::create([
                        'expiration_date' => date('Y-m-d'),
                        'invoice_id' => $insertedInvoice->id,
                        'amount' =>  ($insertedInvoice->amount) ? $insertedInvoice->amount  :0,
                        'payment_option' => $request->payment_option,
                        'paid' => $status,
                        'type' => $insertedInvoice->reference
                    ]);
                    
                }else{
                    $partialAmount = 0 ;

                    if($insertedInvoice->amount){
                        $partialAmount  = $insertedInvoice->amount/3;
                    }

                    for($i=1 ;$i<=3; $i++){
                        $daysToBeAdded = $i*30;
                        $expirationDate = Date('Y-m-d', strtotime("+$daysToBeAdded days"));

                        InvoiceReceipt::create([
                            'expiration_date' => $expirationDate,
                            'invoice_id' => $insertedInvoice->id,
                            'amount' =>  $partialAmount,
                            'payment_option' => $request->payment_option,
                            'paid' => $status,
                            'type' => $insertedInvoice->reference
                        ]);
                    }
                }
            }

            return response()->json([
                "status" => true,
                "data" => InvoiceTable::with(['items', 'item_meta', 'receipts'])->find($invoice->id),
                "message" => "Saved successfully"
            ]);
        }else{
            return response()->json([
                "status" => false,
                "message" => "Please choose different reference number"
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);
        $invoiceReceiptTable = 'company_'.$request->company_id.'_invoice_receipts';

        InvoiceReceipt::setGlobalTable($invoiceReceiptTable);
        $invoice = InvoiceTable::with(['items', 'item_meta', 'receipts'])->where('id', $request->invoice)->first();

        if($invoice ==  NULL){
            return response()->json([
                "status" => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "data" => $invoice
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
        $table = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($table);
        $invoice = InvoiceTable::with('items', 'item_meta')->where('id', $request->invoice)->first();
        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);
        //change format of date
        if($request->date){

            $request['date'] = get_formatted_datetime($request->date);
        }

        if($request->assigned_date){

            $request['assigned_date'] = get_formatted_datetime($request->assigned_date);
        }

        if($request->start_date){

            $request['start_date'] = get_formatted_datetime($request->start_date);
        }

        if($request->end_date){

            $request['end_date'] = get_formatted_datetime($request->end_date);
        }
        if($request->due_date){

            $request['due_date'] = get_formatted_datetime($request->due_date);
        }
        if($request->valid_until){

            $request['valid_until'] = get_formatted_datetime($request->valid_until);
        }
        $invoice->update($request->except('company_id', 'invoice', '_method'));
        $invoice->created_by = \Auth::id();
        if($request->item){

            if( $invoice->items){
                 $invoice->items()->delete();
            }
            if( $invoice->item_meta){
                 $invoice->item_meta()->delete();
            }

            $items = json_decode($request->item, true);

           $meta_discount    = $request->meta_discount;
           $meta_income_tax  = $request->meta_income_tax;
           //save item meta
           if ($meta_discount) {

               ItemMeta::create([
                   'reference_id'  =>  $invoice->id,
                   'parent_id'     =>  $invoice->id,
                   'discount'      => $meta_discount,
                   'income_tax'    => $meta_income_tax
               ]);
           }
           // items
           foreach ($items as $item) {
               $reference = $item['reference'];
               if (isset($item['reference_id'])) {
                   $reference_id = $item['reference_id'];
               } else {
                   $reference_id = NULL;
               }
               
               $name             = isset($item['name']) ? $item['name'] : "";
               $parent_id        =  $invoice->id;
               $type             =  $invoice->reference;
               $description      = isset($item['description']) ? $item['description'] : "";
               $base_price       = isset($item['base_price']) ? $item['base_price'] : 0;
               $quantity         = isset($item['quantity']) ? $item['quantity'] : 1;
               $discount         = isset($item['discount']) ? $item['discount'] : 0;
               $tax              = isset($item['tax']) ? $item['tax'] : 0;
               $income_tax       = isset($item['income_tax']) ? $item['income_tax'] : 0;
               $subtotal        = isset($item['subtotal']) ? $item['subtotal'] : 0;
               $meta_discount    = isset($item['meta_discount']) ? $item['meta_discount'] : 0;
               $meta_income_tax  = isset($item['meta_income_tax']) ? $item['meta_income_tax'] : 0;
               $vat              = isset($item['vat']) ? $item['vat'] : 0;
               $createdItem = Item::create([
                   'reference'     => $reference,
                   'reference_id'  => $reference_id,
                   'parent_id'     => $parent_id,
                   'type'          => $type,
                   'name'          => $name,
                   'description'   => $description,
                   'base_price'    => $base_price,
                   'quantity'      => $quantity,
                   'discount'      => $discount,
                   'tax'           => $tax,
                   'income_tax'    => $income_tax,
                   'subtotal'     => $subtotal,
                   'vat'           => $vat
               ]);
           }
        }
        $invoice->save();

        return response()->json([
            "status" => true,
            "data" => $invoice,
            "message" => "Updated successfully"
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
        $table = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($table);
        $invoice = InvoiceTable::where('id', $request->invoice)->first();

        if ($invoice == NULL) {
            return response()->json([
                'status' => false,
                'message' => "Entry not exist!"
            ]);
        }
        
        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        
        $invoiceReceiptTable = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($invoiceReceiptTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);
        

        Item::where('parent_id', $invoice->id)->where('type', $invoice->type)->delete();
        ItemMeta::where('parent_id', $invoice->id)->delete();
        InvoiceReceipt::where('invoice_id', $invoice->id)->delete();
        if($invoice->delete()){
            return response()->json([
                'status' => true,
                'message' => "Entry deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "There is an error!"
            ]);
        }
    }

    public function batchDelete(Request $request){
        $table = 'company_'.$request->company_id.'_invoice_tables';
        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);
        $invoiceReceiptTable = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($invoiceReceiptTable);
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

        InvoiceTable::setGlobalTable($table);
        $ids = explode(",", $request->ids);
        $invoiceTable = InvoiceTable::whereIn('id', $ids)->delete();
        InvoiceReceipt::whereIn('invoice_id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Clients deleted successfull'
            ]);
    }

    public function sentInvoices(Request $request){
        
        if(($request->company_id ==  NULL)||($request->company_id ==  0)){
            return response()->json([
                "status" => false,
                "message" =>  "Please select company"
            ]);
        }
        $table = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($table);
        
        $data = InvoiceTable::where(['payment_term' => 'immediate', 'reference' => 'inv'])->get();

        if(!count($data)){

            return response()->json([
                "status" => false,
                "message" =>  "No data found!"
            ]);
        }

        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
    }
    public function sendInvoiceMail(Request $request){

        $validator = Validator::make($request->all(),[
            'invoice_id' => 'required',
            'to' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($table);

        $invoice = InvoiceTable::find($request->invoice_id);

        if(!$invoice){

            return response()->json([
                "status" => false,
                "message" =>  "Not found!"
            ]);
        }
        
        SendInvoiceMail::dispatch($request->to , $invoice);
        
        return response()->json([
            "status" => true,
            "data" => [],
            "message" => 'Sent!',
        ]);
    }
    public function duplicate(Request $request){
        $table = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $invoiceReceiptTable = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($invoiceReceiptTable);

        $validator = Validator::make($request->all(),[
            'id'=>'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $invoiceTable = InvoiceTable::with('items','receipts')->find($request->id);
        // dd($invoiceTable);
        if(!$invoiceTable){
            return response()->json([
                'status' => false,
                'message' => 'Invoices Not found!'
            ]);
        }
        $duplicateInvoice = $invoiceTable->replicate();
        $duplicateInvoice->created_at = now();
        $duplicateInvoice->reference_number = get_invoice_table_latest_ref_number($request->company_id, $invoiceTable->reference, 1 );
        $duplicateInvoice->save(); 

        foreach($invoiceTable->items as $invoiceTables){
            $duplicateInvoiceItems = $invoiceTables->replicate();
            $duplicateInvoiceItems->created_at = now();
            $duplicateInvoiceItems->parent_id = $duplicateInvoice->id;
            $duplicateInvoiceItems->save();
        }
        foreach($invoiceTable->receipts as $invoiceTables){
            $duplicateInvoiceReceipts = $invoiceTables->replicate();
            $duplicateInvoiceReceipts->created_at = now();
            $duplicateInvoiceReceipts->invoice_id = $duplicateInvoiceItems->id;
            $duplicateInvoiceReceipts->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Duplicate Invoive Successfully',
            'data' => InvoiceTable::with('items','receipts')->find($duplicateInvoice->id)
        ]);
    }
}
