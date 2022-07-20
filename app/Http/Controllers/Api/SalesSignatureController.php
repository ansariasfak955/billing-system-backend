<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesSignature;
use Validator;

class SalesSignatureController extends Controller
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

        $table = 'company_'.$request->company_id.'_sales_signatures';
        SalesSignature::setGlobalTable($table);
        $sales_signature = SalesSignature::get();

        if ($sales_signature->count() == 0) {
            return response()->json([
                "status" => false,
                "message" => "No sales signature found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "sales_signature" => $sales_signature
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
        print_r($request->all());
        die;
        $validator = Validator::make($request->all(),[
            'client_id' => 'required'
        ], [
            'client_id.required' => 'Please select client.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_sales_signatures';
        SalesSignature::setGlobalTable($table);
        $sales_signature = SalesSignature::create($request->except('company_id'));
        $sales_signature->save();

        return response()->json([
            "status" => true,
            "sales_signature" => $sales_signature,
            "message" => "Sales signature created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SalesSignature  $salesEstimate
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_sales_signatures';
        SalesSignature::setGlobalTable($table);
        $sales_signature = SalesSignature::where('id', $request->sales_signature)->first();

        if($sales_signature ==  NULL){
            return response()->json([
                "status" => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "sales_signature" => $sales_signature
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SalesSignature  $salesEstimate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $table = 'company_'.$request->company_id.'_sales_signatures';
        SalesSignature::setGlobalTable($table);
        $sales_signature = SalesSignature::where('id', $request->sales_signature)->first();
        
        $sales_signature->update($request->except('company_id'));
        $sales_signature->save();

        return response()->json([
            "status" => true,
            "sales_signature" => $sales_signature,
            "message" => "Sales signature updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SalesSignature  $salesEstimate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $table = 'company_'.$request->company_id.'_sales_signatures';
        SalesSignature::setGlobalTable($table);
        $sales_signature = SalesSignature::where('id', $request->sales_signature)->first();

        if ($sales_signature == NULL) {
            return response()->json([
                'status' => false,
                'message' => "Sales signature not exist!"
            ]);
        } else {
            if($sales_signature->delete()){
                return response()->json([
                    'status' => true,
                    'message' => "Sales signature deleted successfully!"
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => "There is an error!"
                ]);
            }    
        }
    }
}