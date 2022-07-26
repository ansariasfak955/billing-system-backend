<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentOption;
use Illuminate\Http\Request;
use Validator;

class PaymentOptionController extends Controller
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

        $table = 'company_'.$request->company_id.'_payment_options';
        PaymentOption::setGlobalTable($table);
        $paymentOptions = PaymentOption::get();

        if ($paymentOptions->count() == 0) {
            return response()->json([
                "status" => false,
                "message" => "No PaymentOptions found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "PaymentOptions" =>  $paymentOptions
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
       
        $table = 'company_'.$request->company_id.'_payment_options';
        $validator = Validator::make($request->all(), [
            'name'     => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        PaymentOption::setGlobalTable($table);
        $payment_option =  new PaymentOption;
        PaymentOption::setGlobalTable($table) ;
        $payment_option = $payment_option->setTable($table)->create($request->all());

        return response()->json([
            "status" => true,
            "payment_option" => $payment_option,
            "message" => "Payment created successfully"
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
        $table = 'company_'.$request->company_id.'_payment_options';
        PaymentOption::setGlobalTable($table);
        $paymentOption = PaymentOption::where('id', $request->payment_option)->first();

        if($paymentOption ==  NULL){
            return response()->json([
                "status" => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "PaymentOption_address" => $paymentOption
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
        $table = 'company_'.$request->company_id.'_payment_options';
        PaymentOption::setGlobalTable($table);
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $paymentOption = PaymentOption::where('id', $request->payment_option)->first();
        $paymentOption->update($request->except('company_id', '_method'));
        $paymentOption->save();

        return response()->json([
            "status" => true,
            "PaymentOption" => $paymentOption,
            "message" => "Payment option updated successfully"
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
        $table = 'company_'.$request->company_id.'_payment_options';
        PaymentOption::setGlobalTable($table);
        $paymentOption = PaymentOption::where('id', $request->payment_option)->first();
        if ($paymentOption == NULL) {
            return response()->json([
                'status' => false,
                'message' => "Payment option not exists!"
            ]);
        }

        if($paymentOption->delete()){
            return response()->json([
                'status' => true,
                'message' => "Payment option deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again!"
            ]);
        }
    }
}
