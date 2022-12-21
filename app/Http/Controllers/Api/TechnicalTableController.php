<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TechnicalTable;
use App\Models\Item;
use App\Models\Client;
use App\Models\ItemMeta;
use App\Models\Reference;
use Validator;
use Storage;

class TechnicalTableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

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

        $table = 'company_'.$request->company_id.'_technical_tables';
        TechnicalTable::setGlobalTable($table);
        $table = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($table);

        // $technical_incidents = TechnicalTable::where('reference', $request->type)->get();
        $query = TechnicalTable::query();

        if($request->search){
            $query = $query->where('reference_number', 'like', '%'.$request->search.'%')->orWhere('status', 'like', '%'.$request->search.'%')
            ->orWhere('title', 'like', '%'.$request->search.'%')->orWhereHas('client', function($q) use ($request){
                $q->where('name',  'like','%'.$request->search.'%');
            });
        }
        if($request->type){
            //set reference table
            $referenceTable = 'company_'.$request->company_id.'_references';
            Reference::setGlobalTable($referenceTable);
            //get dynamic reference
            $refernce_ids = Reference::where('type', urldecode($request->type))->pluck('prefix')->toArray();
            $query = $query->whereIn('reference', $refernce_ids);
        }
        $technical_incidents = $query->get();

        if($technical_incidents->count() == 0) {
            return response()->json([
                "status" => false,
                "message" => "No data found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "data" =>  $technical_incidents
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
        $table = 'company_'.$request->company_id.'_technical_tables';
        $validator = Validator::make($request->all(),[
            'title' => "required|unique:$table",
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

        if($request->email_sent_date){
            $request['email_sent_date'] = get_formatted_datetime($request->email_sent_date);
        }

        if($request->valid_until){
            $request['valid_until'] = get_formatted_datetime($request->valid_until);
        }

        TechnicalTable::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        if ($request->reference_number == '') {
            $request['reference_number'] = get_technical_table_latest_ref_number($request->company_id, $request->reference, 1 );
        }else{

            $technical_incident = TechnicalTable::where('reference', $request->reference)->where('reference_number', $request->reference_number)->first();

            if ($technical_incident) {
                $request->reference_number = '';
            }
        }

        if( $request->reference_number  ){
            $technical_incidents = TechnicalTable::create($request->except('company_id'));
            $technical_incidents->created_by = \Auth::id();
            $technical_incidents->save();
            // dd($request->all());
            if($request->item){
                $items = json_decode($request->item, true);

               $meta_discount    = $request->meta_discount;
               $meta_income_tax  = $request->meta_income_tax;
               //save item meta
               if ($meta_discount) {

                   ItemMeta::create([
                       'reference_id'  => $technical_incidents->id,
                       'parent_id'     => $technical_incidents->id,
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
                   $parent_id        = $technical_incidents->id;
                   $type             = $technical_incidents->reference;
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

            return response()->json([
                "status" => true,
                "data" => TechnicalTable::with(['items', 'item_meta'])->find($technical_incidents->id),
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
        $table = 'company_'.$request->company_id.'_technical_tables';
        TechnicalTable::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $technical_incident = TechnicalTable::with(['items', 'item_meta'])->where('id', $request->technical_table)->first();

        if($technical_incident ==  NULL){
            return response()->json([
                "status" => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "data" => $technical_incident
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
        $table = 'company_'.$request->company_id.'_technical_tables';
        TechnicalTable::setGlobalTable($table);
        $technical_incident = TechnicalTable::with(['items', 'item_meta'])->where('id', $request->technical_table)->first();
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
        if($request->email_sent_date){

            $request['email_sent_date'] = get_formatted_datetime($request->email_sent_date);
        }
        if($request->valid_until){

            $request['valid_until'] = get_formatted_datetime($request->valid_until);
        }

        $technical_incident->update($request->except('company_id', 'technical_table', '_method'));
        $technical_incident->created_by = \Auth::id();
        $technical_incident->save();

        if($request->item){

            if($technical_incident->items){
                $technical_incident->items()->delete();
            }
            if($technical_incident->item_meta){
                $technical_incident->item_meta()->delete();
            }

            $items = json_decode($request->item, true);

           $meta_discount    = $request->meta_discount;
           $meta_income_tax  = $request->meta_income_tax;
           //save item meta
           if ($meta_discount) {

               ItemMeta::create([
                   'reference_id'  => $technical_incident->id,
                   'parent_id'     => $technical_incident->id,
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
               $parent_id        = $technical_incident->id;
               $type             = $technical_incident->reference;
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

        return response()->json([
            "status" => true,
            "data" => TechnicalTable::with(['items', 'item_meta'])->where('id', $request->technical_table)->first(),
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
        $table = 'company_'.$request->company_id.'_technical_tables';
        TechnicalTable::setGlobalTable($table);
        $technical_incident = TechnicalTable::where('id', $request->technical_table)->first();

        if ($technical_incident == NULL) {
            return response()->json([
                'status' => false,
                'message' => "Entry not exist!"
            ]);
        }
        
        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        if($technical_incident->status == 'closed'){
            return response()->json([
                'status' => false,
                'message' => "Unable to delete documents"
            ]);
        }
        if($technical_incident->status == 'invoiced'){
            return response()->json([
                'status' => false,
                'message' => "Unable to delete documents"
            ]);
        }

        Item::where('parent_id', $technical_incident->id)->delete();
        ItemMeta::where('parent_id', $technical_incident->id)->delete();

        if($technical_incident->delete()){
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
        $table = 'company_'.$request->company_id.'_technical_tables';
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

        TechnicalTable::setGlobalTable($table);
        $ids = explode(",", $request->ids);
        TechnicalTable::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Deleted successfully'
            ]);
    }

    public function duplicate(Request $request){
        $table = 'company_'.$request->company_id.'_technical_tables';
        TechnicalTable::setGlobalTable($table);
        
        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

       $technicalTable = TechnicalTable::with('items')->find($request->id);

       if(!$technicalTable){
        return response()->json([
            'status' => false,
            'message' => 'Estimate not found!'
        ]);
       }

       $technicalTables = $technicalTable->replicate();
       $technicalTables->created_at = now();
       $technicalTables->reference_number = get_technical_table_latest_ref_number($request->company_id, $technicalTable->reference, 1 );
       $technicalTables->save();

       foreach($technicalTable->items as $technicalEstimate){
            $technicalService = $technicalEstimate->replicate();
            $technicalService->created_at = now();
            $technicalService->parent_id = $technicalTables->id;
            $technicalService->save();
        }

       return response()->json([
            'status' => true,
            'message' => 'Duplicate Technical Estimate Successfully',
            'data' => TechnicalTable::with('items')->find($technicalTables->id)
       ]);
        
    }
}
