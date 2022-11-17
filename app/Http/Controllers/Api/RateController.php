<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rate;
use App\Models\ProductRate;
use App\Models\ServiceRate;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use Validator;

class RateController extends Controller
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
        Rate::setGlobalTable('company_'.$request->company_id.'_rates');
        if(Rate::count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "rates" =>  Rate::get()
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
        $table = 'company_'.$request->company_id.'_rates';
        Rate::setGlobalTable($table);

        $productRate = 'company_'.$request->company_id.'_product_rates';
        ProductRate::setGlobalTable($productRate);

        $serviceRate = 'company_'.$request->company_id.'_service_rates';
        ServiceRate::setGlobalTable($serviceRate);

        $productTable = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($productTable);

        $serviceTable = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($serviceTable);

        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:'.$table.'',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        
        $rate = Rate::create($request->except('company_id'));
        $products = Product::get();
        $services = Service::get();

        foreach($products as $product_rate){
            $rate = ProductRate::create(['name' => $request->name, 'description' =>$request->description, 'product_id' => $product_rate->id]);
            $rate->purchase_price = $product_rate->purchase_price;
            $rate->sales_price = $product_rate->price;
            $rate->discount = $product_rate->discount;
            $rate->purchase_margin = $product_rate->purchase_margin;
            $rate->sales_margin = $product_rate->sales_margin;
            $rate->save();
        }
        foreach($services as $service_rate){
            $serviceRate = ServiceRate::create(['name' => $request->name, 'description' =>$request->description, 'service_id' => $service_rate->id]);
            $serviceRate->purchase_price = $service_rate->purchase_price;
            $serviceRate->sales_price = $service_rate->price;
            $serviceRate->discount = $service_rate->discount;
            $serviceRate->purchase_margin = $service_rate->purchase_margin;
            $serviceRate->sales_margin = $service_rate->sales_margin;
            $serviceRate->save();
        }
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
        Rate::setGlobalTable('company_'.$request->company_id.'_rates');
        $rate = Rate::where('id', $request->rate)->first();

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
        Rate::setGlobalTable('company_'.$request->company_id.'_rates');
        $validator = Validator::make($request->all(),[
            'name' => 'required'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $rate = Rate::where('id', $request->rate)->first();
        
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
        Rate::setGlobalTable('company_'.$request->company_id.'_rates');
        $rate = Rate::where('id', $request->rate)->first();
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
