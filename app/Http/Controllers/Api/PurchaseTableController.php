<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseTable;
use App\Models\Item;
use App\Models\ItemMeta;
use App\Models\PurchaseReceipt;
use Validator;
use Storage;

class PurchaseTableController extends Controller
{/**
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

        $table = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($table);

        $purchase_table = PurchaseTable::where('reference', $request->type)->get();

        if($purchase_table->count() == 0) {
            return response()->json([
                "status" => false,
                "message" => "No data found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "data" =>  $purchase_table
            ]);  
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
            'supplier_id' => 'required',
            'reference' => 'required',
        ], [
            'supplier_id.required' => 'Please select supplier.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        //change format of date
        if($request->date){

            $request['date'] = get_formatted_datetime($request->date);
        }

        if($request->end_of_warranty){

            $request['end_of_warranty'] = get_formatted_datetime($request->end_of_warranty);
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

        $table = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $invoiceReceiptTable = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($invoiceReceiptTable);

        if ($request->reference_number == '') {
            $request['reference_number'] = get_purchase_table_latest_ref_number($request->company_id, $request->reference, 1 );
        }else{

            $purchase_table = PurchaseTable::where('reference', $request->reference)->where('reference_number', $request->reference_number)->first();

            if ($purchase_table) {
                $request->reference_number = '';
            }
        }

        if( $request->reference_number  ){
            $purchase_table = PurchaseTable::create($request->except('company_id'));
            // $purchase_table->created_by = \Auth::id();
            $purchase_table->save();
            // dd($request->all());
            if($request->item){
                $items = json_decode($request->item, true);

               $meta_discount    = $request->meta_discount;
               $meta_income_tax  = $request->meta_income_tax;
               //save item meta
               if ($meta_discount) {

                   ItemMeta::create([
                       'reference_id'  => $purchase_table->id,
                       'parent_id'     => $purchase_table->id,
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
                   $parent_id        = $purchase_table->id;
                   $type             = $purchase_table->reference;
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
                $insertedInvoice = PurchaseTable::with(['items', 'item_meta'])->find($purchase_table->id);
                $status = ($request->status == 'paid') ? '1' : '0';
                if($request->payment_term == 'immediate'){

                    PurchaseReceipt::create([
                        'expiration_date' => date('Y-m-d'),
                        'purchase_id' => $insertedInvoice->id,
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

                        PurchaseReceipt::create([
                            'expiration_date' => $expirationDate,
                            'purchase_id' => $insertedInvoice->id,
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
                "data" => PurchaseTable::with(['items', 'item_meta'])->find($purchase_table->id),
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
        $table = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($table);

        // $itemTable = 'company_'.$request->company_id.'_items';
        // Item::setGlobalTable($itemTable);

        // $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        // ItemMeta::setGlobalTable($item_meta_table);
        $invoiceReceiptTable = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($invoiceReceiptTable);
        $purchase_table = PurchaseTable::with(['items', 'item_meta', 'receipts'])->where('id', $request->purchase_table)->first();

        if($purchase_table ==  NULL){
            return response()->json([
                "status" => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "data" => $purchase_table
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $table = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($table);
        
        // $purchase_table = PurchaseTable::where('id', $request->purchase_table)->first();
        $purchase_table = PurchaseTable::with(['items' , 'item_meta'])->where('id', $request->purchase_table)->first();
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

        $purchase_table->update($request->except('company_id', 'technical_table', '_method'));
        // $purchase_table->created_by = \Auth::id();
        if($request->item){

            if($purchase_table->items){
                $purchase_table->items()->delete();
            }
            if($purchase_table->item_meta){
                $purchase_table->item_meta()->delete();
            }

            $items = json_decode($request->item, true);

           $meta_discount    = $request->meta_discount;
           $meta_income_tax  = $request->meta_income_tax;
           //save item meta
           if ($meta_discount) {

               ItemMeta::create([
                   'reference_id'  => $purchase_table->id,
                   'parent_id'     => $purchase_table->id,
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
               $parent_id        = $purchase_table->id;
               $type             = $purchase_table->reference;
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
        $purchase_table->save();

        return response()->json([
            "status" => true,
            "data" => PurchaseTable::with(['items' , 'item_meta'])->where('id', $request->purchase_table)->first(),
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
        $table = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($table);
        $purchase_table = PurchaseTable::where('id', $request->purchase_table)->first();

        if ($purchase_table == NULL) {
            return response()->json([
                'status' => false,
                'message' => "Entry not exist!"
            ]);
        }
        
        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        Item::where('parent_id', $purchase_table->id)->delete();
        ItemMeta::where('parent_id', $purchase_table->id)->delete();

        if($purchase_table->delete()){
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
        $table = 'company_'.$request->company_id.'_purchase_tables';
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
        PurchaseTable::setGlobalTable($table);
        $ids = explode(",", $request->ids);
        PurchaseTable::whereIn('id', $ids)->delete(); 
        return response()->json([
            'status' => true,
            'message' => 'Purchase orders deleted successfully'
        ]);
    }

    public function sentInvoices( Request $request){
        if(($request->company_id ==  NULL)||($request->company_id ==  0)){
            return response()->json([
                "status" => false,
                "message" =>  "Please select company"
            ]);
        }
        
        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $table = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($table);
        $data = PurchaseTable::where(['payment_term' => 'immediate', 'reference' => 'pinv'])->get();

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
    
    public function Duplicate(Request $request){
        $table = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $invoiceReceiptTable = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($invoiceReceiptTable);
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $purchaseTable = PurchaseTable::with('receipts','items')->find($request->id);
        if(!$purchaseTable){
            return response()->json([
                'status' => false,
                'message' => 'Purchase not found'
            ]);
        }

        $duplicatePurchase = $purchaseTable->replicate();
        $duplicatePurchase->created_at = now();
        $duplicatePurchase->reference_number = get_purchase_table_latest_ref_number($request->company_id, $purchaseTable->reference, 1 );
        $duplicatePurchase->save();

        foreach($purchaseTable->receipts as $purchaseTables){
            $duplicatePurchaseTable = $purchaseTables->replicate();
            $duplicatePurchaseTable->created_at = now();
            $duplicatePurchaseTable->purchase_id = $duplicatePurchase->id;
            $duplicatePurchaseTable->save();
            foreach($purchaseTable->items as $purchase){
                $purchaseDuplicate = $purchase->replicate();
                $purchaseDuplicate->created_at = now();
                $purchaseDuplicate->parent_id = $duplicatePurchaseTable->id;
                $purchaseDuplicate->save();
            }
        }
            return response()->json([
                'status' => true,
                'message' => 'Duplicate Purchase Successfully',
                'data' => PurchaseTable::with('receipts','items')->find($duplicatePurchase->id)
            ]);
    }
}
