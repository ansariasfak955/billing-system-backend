<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Validator;

class ProductCategoryController extends Controller
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
        // $product_category =  new ProductCategory;
        $product_category_table = 'company_'.$request->company_id.'_product_categories';
        ProductCategory::setGlobalTable($product_category_table);

        if(ProductCategory::count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "product_categories" => ProductCategory::get()
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
            'name' => 'required'        
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $product_category_table = 'company_'.$request->company_id.'_product_categories';
        ProductCategory::setGlobalTable($product_category_table);

        $product_category = ProductCategory::create($request->except('company_id'));

        return response()->json([
            "status" => true,
            "product_category" => $product_category,
            "message" => "Product category created successfully"
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
        $product_category =  new ProductCategory;
        $product_category = $product_category->setTable('company_'.$request->company_id.'_product_categories')->where('id', $request->product_category)->first();

        if($product_category ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "product_category" => $product_category
        ]);
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
        $validator = Validator::make($request->all(),[
            'name' => 'required'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        /*$product_category =  new ProductCategory;
        $product_category = $product_category->setTable('company_'.$request->company_id.'_product_categories')->where('id', $request->product_category)->first();*/

        $product_category_table = 'company_'.$request->company_id.'_product_categories';
        ProductCategory::setGlobalTable($product_category_table);
        $product_category = ProductCategory::where('id', $request->product_category)->first();
        
        $product_category->update($request->except('company_id', '_method'));

        return response()->json([
            "status" => true,
            "product_category" => $product_category
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
        /*$product_category =  new ProductCategory;
        $product_category = $product_category->setTable('company_'.$request->company_id.'_product_categories')->where('id', $request->product_category)->first();*/

        $product_category_table = 'company_'.$request->company_id.'_product_categories';
        ProductCategory::setGlobalTable($product_category_table);
        $product_category->where('id', $request->product_category)->first();

        if($product_category->delete()){
            return response()->json([
                'status' => true,
                'message' => "Product category deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again! "
            ]);
        }
    }
}
