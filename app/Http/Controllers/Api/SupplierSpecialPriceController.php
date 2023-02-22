<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupplierSpecialPrice;
use Illuminate\Http\Request;
use Validator;

class SupplierSpecialPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        $table = 'company_'.$request->company_id.'_supplier_special_prices';
        SupplierSpecialPrice::setGlobalTable($table);

        $query = SupplierSpecialPrice::query();
        if($request->client_id ){
            $query->where('supplier_id' , $request->client_id);
        }
        if($request->product_id ){
            $query->where('product_id' , $request->product_id);
        }

        if($request->type ){
            $query->where('type' , $request->type);
        }
        
        $data =  $query->get();

        if( count($data) ){

            return response()->json([
                "status" => true,
                "supplier_special_prices" =>   $data
            ]);
        }
        return response()->json([
            "status" => false,
            "message" =>  'No data found!'
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
            'supplier_id' => 'required',
            'product_id' => 'required',
            'product_type' => 'required',
        ], [
            'supplier_id.required' => 'Please select supplier ',
            'product_id.required' => 'Please select product ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_supplier_special_prices';
        SupplierSpecialPrice::setGlobalTable($table);
        $supplier_special_price = SupplierSpecialPrice::create($request->except('company_id'));

        return response()->json([
            "status" => true,
            "supplier_special_price" => $supplier_special_price,
            "message" => "Client special price created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SupplierSpecialPrice  $SupplierSpecialPrice
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {

        $table = 'company_'.$request->company_id.'_supplier_special_prices';
        SupplierSpecialPrice::setGlobalTable($table);
        $supplier_special_price = SupplierSpecialPrice::where('id', $request->supplier_special_price)->first();

        if($supplier_special_price ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "supplier_special_price" => $supplier_special_price
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SupplierSpecialPrice  $SupplierSpecialPrice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $table = 'company_'.$request->company_id.'_supplier_special_prices';
        SupplierSpecialPrice::setGlobalTable($table);

        $validator = Validator::make($request->all(),[
            'supplier_id' => 'required',          
            'product_id' => 'required' ,
            'product_type' => 'required'         
        ], [
            'supplier_id.required' => 'Please select client ',
            'product_id.required' => 'Please select product ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $supplier_special_price = SupplierSpecialPrice::where('id', $request->supplier_special_price)->first();
        
        $supplier_special_price->update($request->except('company_id', '_method'));

        return response()->json([
            "status" => true,
            "supplier_special_price" => $supplier_special_price
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SupplierSpecialPrice  $SupplierSpecialPrice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $table = 'company_'.$request->company_id.'_supplier_special_prices';
        SupplierSpecialPrice::setGlobalTable($table);
        $supplier_special_price = SupplierSpecialPrice::where('id', $request->supplier_special_price)->first();
        if($supplier_special_price->delete()){
            return response()->json([
                'status' => true,
                'message' => "Client special price deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again! "
            ]);
        }
    }
}
