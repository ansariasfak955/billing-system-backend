<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientSpecialPrice;
use Illuminate\Http\Request;
use Validator;

class ClientSpecialPriceController extends Controller
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
        $table = 'company_'.$request->company_id.'_client_special_prices';
        ClientSpecialPrice::setGlobalTable($table);

        if($request->client_id == NULL){
            if(ClientSpecialPrice::count() == 0){
                return response()->json([
                    "status" => false,
                    "message" =>  "No data found"
                ]);
            }
            return response()->json([
                "status" => true,
                "client_special_prices" =>  ClientSpecialPrice::get()
            ]);
        }

        if(ClientSpecialPrice::where('client_id', $request->client_id)->count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        
        return response()->json([
            "status" => true,
            "client_special_prices" =>  ClientSpecialPrice::where('client_id', $request->client_id)->get()
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
            'product_id' => 'required'          
        ], [
            'client_id.required' => 'Please select client ',
            'product_id.required' => 'Please select product ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_client_special_prices';
        ClientSpecialPrice::setGlobalTable($table);
        $client_special_price = ClientSpecialPrice::create($request->except('company_id'));

        return response()->json([
            "status" => true,
            "client_special_price" => $client_special_price,
            "message" => "Client special price created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClientSpecialPrice  $clientSpecialPrice
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {

        $table = 'company_'.$request->company_id.'_client_special_prices';
        ClientSpecialPrice::setGlobalTable($table);
        $client_special_price = ClientSpecialPrice::where('id', $request->client_special_price)->first();

        if($client_special_price ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "client_special_price" => $client_special_price
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClientSpecialPrice  $clientSpecialPrice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $table = 'company_'.$request->company_id.'_client_special_prices';
        ClientSpecialPrice::setGlobalTable($table);

        $validator = Validator::make($request->all(),[
            'client_id' => 'required',          
            'product_id' => 'required'          
        ], [
            'client_id.required' => 'Please select client ',
            'product_id.required' => 'Please select product ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $client_special_price = ClientSpecialPrice::where('id', $request->client_special_price)->first();
        
        $client_special_price->update($request->except('company_id', '_method'));

        return response()->json([
            "status" => true,
            "client_special_price" => $client_special_price
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClientSpecialPrice  $clientSpecialPrice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $table = 'company_'.$request->company_id.'_client_special_prices';
        ClientSpecialPrice::setGlobalTable($table);
        $client_special_price = ClientSpecialPrice::where('id', $request->client_special_price)->first();
        if($client_special_price->delete()){
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
