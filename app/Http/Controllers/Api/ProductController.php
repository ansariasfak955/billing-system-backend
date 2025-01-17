<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Rate;
use App\Models\Item;
use App\Models\Reference;
use App\Models\ProductRate;
use Validator;

class ProductController extends Controller
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

        $table = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($table);
        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $products = Product::query();
        
        if($request->type){
            //set reference table
            $referenceTable = 'company_'.$request->company_id.'_references';
            Reference::setGlobalTable($referenceTable);
            //get dynamic reference
            $refernce_ids = Reference::where('type', urldecode($request->type))->pluck('prefix')->toArray();
            $products = $products->whereIn('reference', $refernce_ids);
        }
        $products = $products->filter($request->all())->get();
            
            
        if ( $products->count() == 0 ) {
            return response()->json([
                "status" => false,
                "message" => "No product found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "products" =>  $products
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
            'name' => 'required',
            'price' => 'required|numeric',  
            // 'category' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($table);

        $productRate = 'company_'.$request->company_id.'_product_rates';
        ProductRate::setGlobalTable($productRate);

        // dd($request->all());
        if ($request->reference_number == '') {
            $product = Product::create($request->except('image', 'company_id', 'images'));
            $product->reference_number = get_product_latest_ref_number($request->company_id, $request->reference, 1);
        } else {
            $product = Product::where('reference', $request->reference)->where('reference_number', $request->reference_number)->first();
            if ($product == NULL) {
                $product = Product::create($request->except('image', 'company_id', 'images'));
                $product->reference_number = get_product_latest_ref_number($request->company_id, $request->reference, 0);
            } else {
                return response()->json([
                    "status"  => false,
                    // "client"  => $product,
                    "message" => "Please choose different reference number"
                ]);
            }
        }

        if($request->image != NULL){
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(storage_path('app/public/products/images'), $imageName);
            $product->image = $imageName;
            $product->save();
        }

        $product->product_category_id = $request->product_category_id;
        $product->is_active = $request->is_active??'1';
        $product->active_margin = $request->active_margin??'0';
        $product->is_promotional = $request->is_promotional??'0';
        $product->manage_stock = $request->manage_stock??'0';
        if ($request->manage_stock == 1) {
            $product_stock_table = 'company_'.$request->company_id.'_product_stocks';
            ProductStock::setGlobalTable($product_stock_table);
            $product_stock = ProductStock::where('product_id', $product->id)->first();
            if ($product_stock == NULL) {
                ProductStock::create([
                    'product_id' => $product->id,
                    'warehouse' => 'Main Warehouse',
                    'stock' => $request->stock == NULL ? 0 : $request->stock,
                    'virtual_stock' => $request->virtual_stock == NULL ? 0 : $request->virtual_stock,
                    'minimum_stock' => $request->minimum_stock == NULL ? 0 : $request->minimum_stock,
                ]);
            }
        }
        $product->save();
        $rateTable = 'company_'.$request->company_id.'_rates';
        Rate::setGlobalTable($rateTable);

        $allRates = Rate::get();
        
        foreach($allRates as $rate){
            $product_rate = ProductRate::create(['name' =>  $rate->name, 'description' => $rate->description, 'product_id' => $product->id]);
            $product_rate->purchase_price = $product->purchase_price;
            $product_rate->sales_price = $product->price;
            $product_rate->discount = $product->discount;
            $product_rate->purchase_margin = $product->purchase_margin;
            $product_rate->sales_margin = $product->sales_margin;
            $product_rate->save();
        }

        return response()->json([
            "status" => true,
            "product" => $product,
            "message" => "Product created successfully"
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
        $table = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($table);
        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);
        $product = Product::where('id', $request->product)->first();
        if($product ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "product" => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'price' => 'required|numeric',
            // 'category' => 'required'  
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($table);
        $product = Product::where('id', $request->product)->update($request->except('image', 'company_id', 'images', '_method'));

        if ($request->reference_number == '') {
            $product->reference_number = get_product_latest_ref_number($request->company_id, $request->reference, 1);
        } else {
            $product = Product::where('reference', $request->reference)->where('reference_number', $request->reference_number)->first();
            if ($product == NULL) {
                $product->reference_number = get_product_latest_ref_number($request->company_id, $request->reference, 0);
            } elseif($product != NULL && \Route::currentRouteName()){
                $product->reference_number = get_product_latest_ref_number($request->company_id, $request->reference, 0);
            } else {
                return response()->json([
                    "status"  => false,
                    "client"  => $product,
                    "message" => "Please choose different reference number"
                ]);
            }
        }

        if($request->image != NULL){
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(storage_path('app/public/products/images'), $imageName);
            $product->image = $imageName;
            $product->save();
        }

        $product->product_category_id = $request->product_category_id??$product->product_category_id;
        $product->is_active = $request->is_active??$product->is_active;
        $product->active_margin = $request->active_margin??$product->active_margin;
        $product->is_promotional = $request->is_promotional??$product->is_promotional;
        $product->manage_stock = $request->manage_stock??$product->manage_stock;
        $product->save();

        $productRate = 'company_'.$request->company_id.'_product_rates';
        ProductRate::setGlobalTable($productRate);

        $product_rate = ProductRate::where('product_id',$product->id)->update([
            'purchase_price'=>$product->purchase_price,
            'purchase_margin'=>$product->purchase_margin,
            'sales_margin'=>$product->sales_margin
        ]);

        return response()->json([
            "status" => true,
            "product" => $product,
            "message" => "Product updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $table = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($table);
        $product = Product::where('id', $request->product)->first();
        if($product->delete()){
            return response()->json([
                'status' => true,
                'message' => "Product deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again!"
            ]);
        }
    }
    public function batchDelete(Request $request){
        $table = 'company_'.$request->company_id.'_products';
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

        Product::setGlobalTable($table);
        $ids = explode(",", $request->ids);
        Product::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Deleted successfully'
            ]);
    }
    public function duplicate(Request $request){
        $table = 'company_'.$request->company_id.'_products';
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        Product::setGlobalTable($table);
        $product = Product::find($request->id);
        if(!$product){
            return response()->json([
                'status' => false,
                'message' => 'Product not fiund',
            ]);
        }
        $duplicateProduct = $product->replicate();
        $duplicateProduct->created_at = now();
        $duplicateProduct->reference_number = get_product_latest_ref_number($request->company_id, $product->reference, 1);
        $duplicateProduct->save();
        return response()->json([
            'status' => true,
            'message' => 'Duplicate Product Successfully',
            'data' => $product
        ]);
    }
}