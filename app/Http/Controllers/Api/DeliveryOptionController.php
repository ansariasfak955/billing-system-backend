<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOption;
use Illuminate\Http\Request;
use Validator;

class DeliveryOptionController extends Controller
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

        $table = 'company_'.$request->company_id.'_delivery_options';

        DeliveryOption::setGlobalTable($table);
        $query =  DeliveryOption::query();

        if($request->client_id){
            $query->where('client_id' , $request->client_id);
        }

        $deliveryOptions = $query->get();

        if ($deliveryOptions->count() == 0) {
            return response()->json([
                "status" => false,
                "message" => "No delivery option found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "delivery_options" =>  $deliveryOptions
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
       
        $table = 'company_'.$request->company_id.'_delivery_options';
        $validator = Validator::make($request->all(), [
            'name'     => "required",
            // 'client_id'     => "required|numeric",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        DeliveryOption::setGlobalTable($table);
        $delivery_option =  DeliveryOption::create($request->all());

        return response()->json([
            "status" => true,
            "delivery_option" => $delivery_option,
            "message" => "Delivery Option created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PaymentOption  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_delivery_options';
        DeliveryOption::setGlobalTable($table);
        $deliveryOption = DeliveryOption::where('id', $request->delivery_option)->first();

        if($deliveryOption ==  NULL){
            return response()->json([
                "status" => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "PaymentOption_address" => $deliveryOption
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
        $table = 'company_'.$request->company_id.'_delivery_options';
        DeliveryOption::setGlobalTable($table);
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $deliveryOption = DeliveryOption::where('id', $request->delivery_option)->first();
        $deliveryOption->update($request->except('company_id', '_method'));
        $deliveryOption->save();

        return response()->json([
            "status" => true,
            "delivery_option" => $deliveryOption,
            "message" => "Delivery option updated successfully"
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
        $table = 'company_'.$request->company_id.'_delivery_options';
        DeliveryOption::setGlobalTable($table);
        $deliveryOption = DeliveryOption::where('id', $request->delivery_option)->first();
        if ($deliveryOption == NULL) {
            return response()->json([
                'status' => false,
                'message' => "Delivery option not exists!"
            ]);
        }

        if($deliveryOption->delete()){
            return response()->json([
                'status' => true,
                'message' => "Delivery option deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again!"
            ]);
        }
    }
    public function batchDelete(Request $request){
        $table = 'company_'.$request->company_id.'_delivery_options';
        $validator = Validator::make($request->all(), [
            'ids' => 'required'
        ],[
            'ids.required' => 'Please select entry to delete'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        DeliveryOption::setGlobalTable($table);
        $ids = explode(",", $request->ids);
        DeliveryOption::whereIn('id', $ids)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Delivery deleted successfully'
        ]);
    }
}
