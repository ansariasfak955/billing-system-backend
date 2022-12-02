<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesEstimate;
use App\Models\Item;
use App\Models\InvoiceTable;
use App\Models\Client;
use App\Models\ItemMeta;
use App\Models\Reference;
use Validator;
use Storage;

class SalesEstimateController extends Controller
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
        $table = 'company_'.$request->company_id.'_sales_estimates';

        SalesEstimate::setGlobalTable($table);
        $clientTable = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientTable);

        $query = SalesEstimate::query();
        // $sales_estimate = SalesEstimate::where('reference', $request->type)->get();

        if($request->type){
            $query = $query->where('reference', $request->type);
        }
        if($request->search){
            $query = $query->Where('reference_number', 'like', '%'.$request->search.'%')->orWhere('title', 'like', '%'.$request->search.'%')
            ->orWhere('status', 'like', '%'.$request->search.'%')->orWhere('date', 'like', '%'.$request->search.'%')
            ->orWhereHas('client', function($q) use ($request){
                $q->where('name',  'like','%'.$request->search.'%');
            });
        }
        if($request->type){
            //get dynamic reference
            $refernce_ids = Reference::where('type', $request->type)->pluck('prefix')->toArray();
            $query = $query->whereIn('reference', $refernce_ids);
        }
        $sales_estimate = $query->get();

        if ($sales_estimate->count() == 0) {
            return response()->json([
                "status" => false,
                "message" => "No sales estimate found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "sales_estimate" => $sales_estimate
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
        $validator = Validator::make($request->all(),[
            'client_id' => 'required',
            'item' => 'required',
        ], [
            'client_id.required' => 'Please select client.'
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

        if($request->scheduled_delivery_date){

            $request['scheduled_delivery_date'] = get_formatted_datetime($request->scheduled_delivery_date);
        }
        if($request->sent_date){

            $request['sent_date'] = get_formatted_datetime($request->sent_date);
        }

        $request['created_by'] =\Auth::id();
        $table = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        if ($request->reference_number == '') {
            $request['reference_number'] = get_sales_estimate_latest_ref_number($request->company_id, $request->reference, 1 );
        }else{

            $sales_estimate = SalesEstimate::where('reference', $request->reference)->where('reference_number', $request->reference_number)->first();

            if ($sales_estimate) {
                $request->reference_number = '';
            }
        }
        if( $request->reference_number ){
            
            $sales_estimate = SalesEstimate::create($request->except('company_id'));
            if ($request->signature) {
                $signature_name = time().'.'.$request->signature->extension();  
                $request->signature->move(storage_path('app/public/sales/signature'), $signature_name);
                $sales_estimate->signature = $signature_name;    
            }
            if($request->item){
                 $items = json_decode($request->item, true);

                $meta_discount    = $request->meta_discount;
                $meta_income_tax  = $request->meta_income_tax;
                //save item meta
                if ($meta_discount) {

                    ItemMeta::create([
                        'reference_id'  => $sales_estimate->id,
                        'parent_id'     => $sales_estimate->id,
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
                    $parent_id        = $sales_estimate->id;
                    $type             = $sales_estimate->reference;
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
            $sales_estimate->save();
        }else{
            return response()->json([
                "status" => false,
                "message" => "Please choose a different reference number"
            ]);
        }

        return response()->json([
            "status" => true,
            "sales_estimate" => SalesEstimate::with(['items', 'item_meta'])->find($sales_estimate->id),
            "message" => "Sales estimate created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SalesEstimate  $salesEstimate
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($table);
        
        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $sales_estimate = SalesEstimate::with(['items', 'item_meta'])->find($request->sales_estimate);

        if($sales_estimate ==  NULL){
            return response()->json([
                "status" => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "sales_estimate" => $sales_estimate
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SalesEstimate  $salesEstimate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        
        $validator = Validator::make($request->all(),[
            'client_id' => 'required',
            'item' => 'required',
        ], [
            'client_id.required' => 'Please select client.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);
        $table = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($table);
        $sales_estimate = SalesEstimate::with(['items' , 'item_meta'])->where('id', $request->sales_estimate)->first();
        
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

        if($request->scheduled_delivery_date){

            $request['scheduled_delivery_date'] = get_formatted_datetime($request->scheduled_delivery_date);
        }
        if($request->sent_date){

            $request['sent_date'] = get_formatted_datetime($request->sent_date);
        }

        $sales_estimate->update($request->except('company_id'));
        if ($request->signature) {
            $signature_name = time().'.'.$request->signature->extension();  
            $request->signature->move(storage_path('app/public/sales/signature'), $signature_name);
            $sales_estimate->signature = $signature_name;    
        }

        if($request->item){

            if($sales_estimate->items){
                $sales_estimate->items()->delete();
            }
            if($sales_estimate->item_meta){
                $sales_estimate->item_meta()->delete();
            }

            $items = json_decode($request->item, true);

           $meta_discount    = $request->meta_discount;
           $meta_income_tax  = $request->meta_income_tax;
           //save item meta
           if ($meta_discount) {

               ItemMeta::create([
                   'reference_id'  => $sales_estimate->id,
                   'parent_id'     => $sales_estimate->id,
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
               $parent_id        = $sales_estimate->id;
               $type             = $sales_estimate->reference;
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

        $sales_estimate->save();

        return response()->json([
            "status" => true,
            "sales_estimate" =>  SalesEstimate::with(['items' , 'item_meta'])->where('id', $request->sales_estimate)->first(),
            "message" => "Sales estimate updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SalesEstimate  $salesEstimate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $table = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($table);
        $sales_estimate = SalesEstimate::where('id', $request->sales_estimate)->first();

        if ($sales_estimate == NULL) {
            return response()->json([
                'status' => false,
                'message' => "Sales estimate not exist!"
            ]);
        } else {
            if($sales_estimate->delete()){
                Storage::delete('public/sales-estimate/signature/'.$sales_estimate->signature.'');
                return response()->json([
                    'status' => true,
                    'message' => "Sales estimate deleted successfully!"
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => "There is an error!"
                ]);
            }    
        }
    }
    public function batchDelete(Request $request){
        $table = 'company_'.$request->company_id.'_sales_estimates';
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

        SalesEstimate::setGlobalTable($table);
        $ids = explode(",", $request->ids);
        SalesEstimate::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Sales estimate deleted successfully'
            ]);
    }
    public function duplicate(Request $request){
        $table = 'company_'.$request->company_id.'_sales_estimates';
        $itemTable = 'company_'.$request->company_id.'_items';

        $validator = Validator::make($request->all(),[
            'id'=>'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        
        SalesEstimate::setGlobalTable($table);
        Item::setGlobalTable($itemTable);


        $salesEstimate = SalesEstimate::with('items')->find($request->id);
        if(!$salesEstimate){
            return response()->json([
                'status' => false,
                'message' => 'Sales Not found!'
            ]);
        }
        // dd($salesEstimate->_items);
        $duplicatedEstimate = $salesEstimate->replicate();
        $duplicatedEstimate->created_at = now();
        $duplicatedEstimate->reference_number = get_sales_estimate_latest_ref_number($request->company_id, $salesEstimate->reference, 1 );
        $duplicatedEstimate->save();
        
        foreach($salesEstimate->items as $salesItems){
            $duplicatedItem = $salesItems->replicate();
            $duplicatedItem->created_at = now();
            $duplicatedItem->parent_id =  $duplicatedEstimate->id;
            $duplicatedItem->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Duplicate Sales successfully',
            'data' =>  SalesEstimate::with('items')->find($duplicatedEstimate->id)
        ]);
        
    }

}