<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductRate;
use Validator;

class ProductRateController extends Controller
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
        ProductRate::setGlobalTable('company_'.$request->company_id.'_product_rates');
        $query = ProductRate::query();

        if($request->product_id){
            $query = $query->where('product_id', ($request->product_id));
        }

        $products = $query->get();
        if( !count($products)){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "rates" => $products
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
        $table = 'company_'.$request->company_id.'_product_rates';
       ProductRate::setGlobalTable($table);
        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:'.$table.'',
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        
        $rate =ProductRate::create($request->except('company_id'));

        return response()->json([
            "status" => true,
            "rate" => $rate,
            "message" => "Rate created successfully"
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
       ProductRate::setGlobalTable('company_'.$request->company_id.'_product_rates');
        $rate =ProductRate::where('id', $request->product_rate)->first();

        if($rate ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "rate" => $rate
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
       ProductRate::setGlobalTable('company_'.$request->company_id.'_product_rates');
        $validator = Validator::make($request->all(),[
            'name' => 'required' ,
            'product_id' => 'required',        
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $rate = ProductRate::where('id', $request->product_rate)->first();
        
        $updateRate = $rate->update($request->except('company_id', '_method'));
        // dd($updateRate);

        return response()->json([
            "status" => true,
            "rate" => $rate
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
        ProductRate::setGlobalTable('company_'.$request->company_id.'_product_rates');
        $rate =ProductRate::where('id', $request->product_rate)->first();
        if($rate->delete()){
            return response()->json([
                'status' => true,
                'message' => "Rate deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again! "
            ]);
        }
    }
}
