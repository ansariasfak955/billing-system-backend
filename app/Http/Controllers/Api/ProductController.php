<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
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
        $product =  new Product;
        return response()->json([
            "status" => true,
            "products" =>  $product->setTable('company_'.$request->company_id.'_products')->get()
        ]);
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
            'price' => 'required|numeric'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $product =  new Product;
        $product = $product->setTable('company_'.$request->company_id.'_products')->create($request->except('image', 'company_id', 'images'));

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
        $product->save();

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
        $product =  new Product;
        $product = $product->setTable('company_'.$request->company_id.'_products')->where('id', $request->product)->first();
 
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
            'price' => 'required|numeric'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $product =  new Product;
        $product = $product->setTable('company_'.$request->company_id.'_products')->where('id', $request->product)->first();
        $product->setTable('company_'.$request->company_id.'_products')->where('id', $request->product)->update($request->except('image', 'company_id', 'images', '_method'));

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

        return response()->json([
            "status" => true,
            "product" => $product,
            "message" => "Product created successfully"
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
        $product =  new Product;
        $product = $product->setTable('company_'.$request->company_id.'_products')->where('id', $request->product)->first();
        if($product->delete()){
            return response()->json([
                    'status' => true,
                    'message' => "Product deleted successfully!"
            ]);
        } else {
            return response()->json([
                    'status' => false,
                    'message' => "Retry deleting again! "
            ]);
        }
    }
}
