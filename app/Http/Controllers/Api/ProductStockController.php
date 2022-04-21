<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductStock;
use Validator;

class ProductStockController extends Controller
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

        $table = 'company_'.$request->company_id.'_product_stocks';
        ProductStock::setGlobalTable($table);
        $product_stock = ProductStock::get();

        if ($product_stock->count() == 0) {
            return response()->json([
                "status" => false,
                "message" => "No product stock found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "product_stock" => $product_stock
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
            'product_id' => 'required'
        ], [
            'product_id.required' => 'Please select product.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_product_stocks';
        ProductStock::setGlobalTable($table);
        $product_stock = ProductStock::where('product_id', $request->product_id)->first();
        if($product_stock == NULL) {
            $product_stock = ProductStock::create($request->except('company_id'));
            $product_stock->warehouse = 'Main Warehouse';
            $product_stock->save();
        }

        return response()->json([
            "status" => true,
            "product_stock" => $product_stock,
            "message" => "Product stock created successfully"
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
        $table = 'company_'.$request->company_id.'_product_stocks';
        ProductStock::setGlobalTable($table);

        $product_stock = ProductStock::where('id', $request->product_stock)->first();
        if ($product_stock == NULL) {
            return response()->json([
                "status"  => false,
                "message" => "Product stock not exists!"
            ]);
        }

        $product_stock = ProductStock::where('id', $request->product_stock)->first();
        $product_stock->update($request->except('company_id', 'product_stock', '_method'));
        $product_stock->virtual_stock = $request->stock;
        $product_stock->warehouse = 'Main Warehouse';
        $product_stock->save();

        return response()->json([
            "status"  => true,
            "client"  => $product_stock,
            "message" => "Product stock updated successfully"
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
        
    }
}