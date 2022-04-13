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
    	$items = $request->all()['item'];
    	foreach ($items as $item) {
    		$reference        = $item['reference'];
            if (isset($item['reference_id'])) {
                $reference_id     = $item['reference_id'];
            } else {
                $reference_id     = NULL;
            }
            
            $name             = $item['name'];
			$description      = $item['description'];
			$base_price       = $item['base_price'];
			$quantity         = $item['quantity'];
			$discount         = $item['discount'];
			$tax              = $item['tax'];
			$income_tax       = $item['income_tax'];
            $meta_discount    = $item['meta_discount'];
            $meta_income_tax  = $item['meta_income_tax'];

			Item::create([
				'reference'     => $reference,
                'reference_id'  => $reference_id,
				'name'          => $name,
				'description'   => $description,
				'base_price'    => $base_price,
				'quantity'      => $quantity,
				'discount'      => $discount,
				'tax'           => $tax,
				'income_tax'    => $income_tax
			]);

            if ($meta_discount) {
                $item_meta_table = 'company_'.$request->company_id.'_item_metas';
                ItemMeta::setGlobalTable($item_meta_table);

                if (isset($item['reference_id'])) {
                    ItemMeta::create([
                        'reference_id'  => $item['reference_id'],
                        'discount'      => $item['meta_discount'],
                        'income_tax'    => $item['meta_income_tax']
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
    	$items = $request->all()['item'];
    	$item_ids = array_keys($items);
    	foreach ($items as $item_id => $item) {
    		Item::where('id', $item_id)->update([
				'name' 		  => $item['name'],
				'description' => $item['description'],
				'base_price'  => $item['base_price'],
				'quantity' 	  => $item['quantity'],
				'discount' 	  => $item['discount'],
				'tax' 	  	  => $item['tax'],
				'income_tax'  => $item['income_tax']
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