<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupplierAddress;
use Illuminate\Http\Request;
use Validator;

class SupplierAddressController extends Controller
{
    public function index(Request $request)
    {
        if(($request->company_id ==  NULL)||($request->company_id ==  0)){
            return response()->json([
                "status" => false,
                "message" =>  "Please select company"
            ]);
        }

        $table = 'company_'.$request->company_id.'_supplier_addresses';
        SupplierAddress::setGlobalTable($table);
        if($request->supplier_id){
            $supplier_addresses = SupplierAddress::where('supplier_id', $request->supplier_id)->get();
        }else{

            $supplier_addresses = SupplierAddress::get();
        }

        if ($supplier_addresses->count() == 0) {
            return response()->json([
                "status" => false,
                "message" => "No address found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "supplier_addresses" =>  $supplier_addresses
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
        $validator = Validator::make($request->all(),[
            'supplier_id' => 'required'
        ], [
            'supplier_id.required' => 'Please select supplier.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_supplier_addresses';
        SupplierAddress::setGlobalTable($table);
        $supplier_address = SupplierAddress::create($request->except('company_id', 'type'));
        
        // $supplier_address->type = $request->type == NULL ? "other": $request->type;
        $supplier_address->save();

        return response()->json([
            "status" => true,
            "supplier_address" => $supplier_address,
            "message" => "supplier address created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SupplierAddress  $SupplierAddress
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_supplier_addresses';
        SupplierAddress::setGlobalTable($table);
        $supplier_address = SupplierAddress::where('id', $request->supplier_address)->first();

        if($supplier_address ==  NULL){
            return response()->json([
                "status" => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "supplier_address" => $supplier_address
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SupplierAddress  $SupplierAddress
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $table = 'company_'.$request->company_id.'_supplier_addresses';
        SupplierAddress::setGlobalTable($table);
        $supplier_address = SupplierAddress::where('id', $request->supplier_address)->first();
        
        $supplier_address->update($request->except('company_id', 'supplier_address'));
        $supplier_address->save();

        return response()->json([
            "status" => true,
            "supplier" => $supplier_address,
            "message" => "supplier address updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SupplierAddress  $SupplierAddress
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $table = 'company_'.$request->company_id.'_supplier_addresses';
        SupplierAddress::setGlobalTable($table);
        $supplier_address = SupplierAddress::where('id', $request->supplier_address)->first();

        if($supplier_address->delete()){
            return response()->json([
                'status' => true,
                'message' => "supplier address deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "There is an error!"
            ]);
        }
    }
}
