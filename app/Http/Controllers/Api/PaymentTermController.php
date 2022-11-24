<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentTerm;
use Illuminate\Http\Request;
use Validator;

class PaymentTermController extends Controller
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
            $table = 'company_'.$request->company_id.'_payment_terms';
            PaymentTerm::setGlobalTable($table);
            $paymentTerms = PaymentTerm::where('id', $request->payment_term)->get();

            if ($paymentTerms->count() == 0) {
                return response()->json([
                    "status" => false,
                    "message" => "No sales estimate found!"
                ]);
            } else {
                return response()->json([
                    "status" => true,
                    "sales_estimate" => $paymentTerms
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
        $table = 'company_'.$request->company_id.'_payment_terms';
        PaymentTerm::setGlobalTable($table);
        $validator = Validator::make($request->all(),[
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $payment = PaymentTerm::create($request->all());

        return response()->json([
            'status' => true,
            'data' => $payment,
            'message' => 'Payment successfully'
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PaymentTerm  $paymentTerm
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_payment_terms';
        PaymentTerm::setGlobalTable($table);
        $payment = PaymentTerm::where('id', $request->payment_term)->first();
        if(!$payment){
            return response()->json([
                'status' => false,
                'message' => 'No data'
            ]);
        }
        return response()->json([
            "status" => true,
            "payment" => $payment
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PaymentTerm  $paymentTerm
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $table = 'company_'.$request->company_id.'_payment_terms';
        PaymentTerm::setGlobalTable($table);
        $validator = Validator::make($request->all(),[
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $paymentTerm = PaymentTerm::where('id', $request->payment_term)->first();

        $updatePayment = $paymentTerm->update($request->except('company_id', '_method'));
        return response()->json([
            'status' => true,
            'data' => $paymentTerm,
            'message' => 'Payment updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PaymentTerm  $paymentTerm
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $table = 'company_'.$request->company_id.'_payment_terms';
        PaymentTerm::setGlobalTable($table);
    
        $paymentDelete = PaymentTerm::where('id', $request->payment_term)->first();
        if(!$paymentDelete){
            return response()->json([
                'status' => false,
                'message' => 'Payment not found'
            ]);
        }
        $paymentDelete->delete();

        return response()->json([
            'status' => true,
            'message' => 'Payment deleted successfully'
        ]);
    }

    public function batchDelete(Request $request){
        $table = 'company_'.$request->company_id.'_payment_terms';

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
        PaymentTerm::setGlobalTable($table);
        $ids = explode(",", $request->ids);
        PaymentTerm::whereIn('id', $ids)->delete();
        return response()->json([
            'status' => true,
            'message' => 'PaymentTerm deleted successfully'
        ]);
    }
}
