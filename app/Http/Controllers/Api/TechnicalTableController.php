<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TechnicalTable;
use App\Models\Item;
use App\Models\ItemMeta;
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

        $technical_incidents = TechnicalTable::where('reference', $request->type)->get();

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

        $table = 'company_'.$request->company_id.'_technical_tables';
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
                $items = $request->all()['item'];

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
                    $reference        = $item['reference'];
                    if (isset($item['reference_id'])) {
                        $reference_id     = $item['reference_id'];
                    } else {
                        $reference_id     = NULL;
                    }
                    
                    $name             = $item['name'];
                    $parent_id        = $technical_incidents->id;
                    $type             = $technical_incidents->reference;
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
                "data" => TechnicalTable::with(['items', 'itemMeta'])->find($technical_incidents->id),
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

        $technical_incident = TechnicalTable::with(['items', 'itemMeta'])->where('id', $request->technical_table)->first();

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
        $technical_incident = TechnicalTable::where('id', $request->technical_table)->first();
        
        $technical_incident->update($request->except('company_id', 'technical_table', '_method'));
        $technical_incident->created_by = \Auth::id();
        $technical_incident->save();

        return response()->json([
            "status" => true,
            "data" => $technical_incident,
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
}