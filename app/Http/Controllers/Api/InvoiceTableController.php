<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvoiceTable;
use App\Models\Item;
use App\Models\ItemMeta;
use Validator;
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

        $invoice = InvoiceTable::where('reference', $request->type)->get();

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
                    $reference        = $item['reference'];
                    if (isset($item['reference_id'])) {
                        $reference_id     = $item['reference_id'];
                    } else {
                        $reference_id     = NULL;
                    }
                    
                    $name             = $item['name'];
                    $parent_id        = $invoice->id;
                    $type             = $invoice->reference;
                    $description      = $item['description'];
                    $base_price       = $item['base_price'];
                    $quantity         = $item['quantity'];
                    $discount         = $item['discount'];
                    $tax              = $item['tax'];
                    $income_tax       = $item['income_tax'];
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
                        'income_tax'    => $income_tax
                    ]);
                }
            }

            return response()->json([
                "status" => true,
                "data" => InvoiceTable::with(['items', 'itemMeta'])->find($invoice->id),
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

        $invoice = InvoiceTable::with(['items', 'itemMeta'])->where('id', $request->invoice)->first();

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
        $invoice = InvoiceTable::where('id', $request->invoice)->first();

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

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        Item::where('parent_id', $invoice->id)->where('type', $invoice->type)->delete();
        ItemMeta::where('parent_id', $invoice->id)->delete();

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
}
