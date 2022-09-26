<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceSpecialPrice;
use Illuminate\Http\Request;
use Validator;

class ServiceSpecialPriceController extends Controller
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
        $table = 'company_'.$request->company_id.'_service_special_prices';
        ServiceSpecialPrice::setGlobalTable($table);

        $query = ServiceSpecialPrice::query();
        if($request->client_id ){
            $query->where('client_id' , $request->client_id);
        }

        if($request->type ){
            $query->where('type' , $request->type);
        }
        
        $data =  $query->get();

        if( count($data) ){

            return response()->json([
                "status" => true,
                "service_special_prices" => $data
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
            'client_id' => 'required',
            'service_id' => 'required',
            'type' => 'required',
        ], [
            'client_id.required' => 'Please select client ',
            'service_id.required' => 'Please select service ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_service_special_prices';
        ServiceSpecialPrice::setGlobalTable($table);
        $service_special_price = ServiceSpecialPrice::create($request->except('company_id'));

        return response()->json([
            "status" => true,
            "service_special_price" => $service_special_price,
            "message" => "Service special price created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ServiceSpecialPrice  $serviceSpecialPrice
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {

        $table = 'company_'.$request->company_id.'_service_special_prices';
        ServiceSpecialPrice::setGlobalTable($table);
        $service_special_price = ServiceSpecialPrice::where('id', $request->service_special_price)->first();

        if($service_special_price ==  NULL){
            return response()->json([
                "status" => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "service_special_price" => $service_special_price
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServiceSpecialPrice  $serviceSpecialPrice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $table = 'company_'.$request->company_id.'_service_special_prices';
        ServiceSpecialPrice::setGlobalTable($table);

        $validator = Validator::make($request->all(),[
            'client_id' => 'required',          
            'service_id' => 'required'          
        ], [
            'client_id.required' => 'Please select client ',
            'service_id.required' => 'Please select service ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $service_special_price = ServiceSpecialPrice::where('id', $request->service_special_price)->first();
        
        $service_special_price->update($request->except('company_id', '_method'));

        return response()->json([
            "status" => true,
            "service_special_price" => $service_special_price
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ServiceSpecialPrice  $serviceSpecialPrice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $table = 'company_'.$request->company_id.'_service_special_prices';
        ServiceSpecialPrice::setGlobalTable($table);
        $service_special_price = ServiceSpecialPrice::where('id', $request->service_special_price)->first();
        if($service_special_price->delete()){
            return response()->json([
                'status' => true,
                'message' => "Service special price deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again! "
            ]);
        }
    }
}
