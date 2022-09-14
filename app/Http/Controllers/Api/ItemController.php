<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemMeta;
use Illuminate\Http\Request;
use Validator;

class ItemController extends Controller
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
                "status"  => false,
                "message" => "Please select company"
            ]);
        }

        if($request->reference ==  NULL){
            return response()->json([
                "status"  => false,
                "message" => "Please select reference"
            ]);
        }

        $table = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($table);
        $items = Item::where('reference', $request->reference)->get();

        if ($items->count() == 0) {
            return response()->json([
                "status"  => false,
                "message" => "No items found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "items"  => $items
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
    	$table = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($table);
    	$items = json_decode($request->item, true);
    	foreach ($items as $item) {
    		$reference        = $item['reference'];
            if (isset($item['reference_id'])) {
                $reference_id     = $item['reference_id'];
            } else {
                $reference_id     = NULL;
            }
            
            $name             = isset($item['name']) ? $item['name'] : "";
			$description      = isset($item['description']) ? $item['description'] : "";
			$base_price       = isset($item['base_price']) ? $item['base_price'] : 0;
			$quantity         = isset($item['quantity']) ? $item['quantity'] : 1;
			$discount         = isset($item['discount']) ? $item['discount'] : 0;
			$tax              = isset($item['tax']) ? $item['tax'] : 0;
			$income_tax       = isset($item['income_tax']) ? $item['income_tax'] : 0;
            $meta_discount    = isset($item['meta_discount']) ? $item['meta_discount'] : 0;
            $meta_income_tax  = isset($item['meta_income_tax']) ? $item['meta_income_tax'] : 0;
            $subtotal        = isset($item['subtotal']) ? $item['subtotal'] : 0;
            $vat              = isset($item['vat']) ? $item['vat'] : 0;

			$createdItem = Item::create([
				'reference'     => $reference,
                'reference_id'  => $reference_id,
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

            if ($meta_discount) {
                $item_meta_table = 'company_'.$request->company_id.'_item_metas';
                ItemMeta::setGlobalTable($item_meta_table);

                if (isset($item['reference_id'])) {
                    ItemMeta::create([
                        'reference_id'  => $item['reference_id'],
                        'discount'      => $meta_discount,
                        'income_tax'    => $meta_income_tax
                    ]);
                }
            }
    	}

    	return response()->json([
            "status"  => true,
            "message" => "Item created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($table);
        $item = Item::where('reference', $request->reference)->where('id', $request->item)->first();

        if($item ==  NULL){
            return response()->json([
                "status"  => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "item"   => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
    	$table = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($table);
    	 $items = json_decode($request->item, true);
    	$item_ids = array_keys($items);
    	foreach ($items as $item_id => $item) {
    		Item::where('id', $item_id)->update([
				'name' 		  => $item['name'],
				'description' => $item['description'],
				'base_price'  => $item['base_price'],
				'quantity' 	  => $item['quantity'],
				'discount' 	  => $item['discount'],
				'tax' 	  	  => $item['tax'],
				'income_tax'  => $item['income_tax'],
                'subtotal'   => $item['subtotal'],
                'vat'         => $item['vat']
    		]);
    	}

    	$items = Item::whereIn('id', $item_ids)->get();
        return response()->json([
            "status"  => true,
            "items"   => $items,
            "message" => "Items updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $table = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($table);
        $item = Item::where('id', $request->item)->first();

        if($item == NULL) {
        	return response()->json([
	            "status"  => false,
	            "message" => "This entry does not exists"
	        ]);
        }

        $item->delete();
        return response()->json([
            "status"  => true,
            "message" => "Item deleted successfully"
        ]);
    }
}