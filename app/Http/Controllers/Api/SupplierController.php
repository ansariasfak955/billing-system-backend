<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\supplierSpecialPrice;
use Illuminate\Http\Request;
use Validator;

class SupplierController extends Controller
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

        $table = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($table);
        $query = Supplier::query();
        
        if($request->search){
            $query = $query->where('name', 'like', '%'.$request->search.'%')->orWhere('legal_name', 'like', '%'.$request->search.'%')->orWhere('reference_number', 'like', '%'.$request->search.'%');
        }
        $query = $query->get();

        if (!count($query)) {
            return response()->json([
                "status" => false,
                "message" => "No suppliers found!"
            ]);
        } 
        return response()->json([
            "status" => true,
            "suppliers" =>  $query
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
        $table = 'company_'.$request->company_id.'_suppliers';
        $validator = Validator::make($request->all(), [
            'email'     => "required|unique:$table|email",
            'reference' => "required",
            'legal_name' => "required|unique:$table",
            'tin' => "required|alpha_num|unique:$table",
            'bank_account_account' => "sometimes|nullable|unique:$table",
            'phone_1' => "sometimes|nullable|unique:$table",
            'phone_2' => "sometimes|nullable|unique:$table"
        ],[
            'tin.required' => 'Ced/Ruc number is required',
            'tin.unique' => 'Ced/Ruc number must be unique',
            'tin.alpha_num' => 'Ced/Ruc special characters are not allowed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        if(!$request->payment_adjustment || $request->payment_adjustment ==''){
            $request['payment_adjustment'] = 'unspecified';
        }
        Supplier::setGlobalTable($table);
        if ($request->reference_number == '') {
            $supplier = Supplier::create($request->except(['company_id', 'contacts', 'addresses']));
            $supplier->reference_number = get_supplier_latest_ref_number($request->company_id, $request->reference, 1);
            $supplier->save();

            return response()->json([
                "status" => true,
                "Supplier" => $supplier,
                "message" => "Supplier created successfully"
            ]);
        } else {
            $supplier = Supplier::where('reference', $request->reference)->where('reference_number', $request->reference_number)->first();

            if ($supplier == NULL) {
                $supplier = Supplier::create($request->except(['company_id', 'contacts', 'addresses']));
                $supplier->reference_number = get_Supplier_latest_ref_number($request->company_id, $request->reference, 0);
                $supplier->save();

                return response()->json([
                    "status"  => true,
                    "Supplier"  => $supplier,
                    "message" => "Supplier created successfully"
                ]);
            } else {
                return response()->json([
                    "status"  => false,
                    "Supplier"  => $supplier,
                    "message" => "Please choose different reference number"
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($table);
        $supplier = Supplier::where('id', $request->supplier)->first();

        if($supplier ==  NULL){
            return response()->json([
                "status" => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "Supplier_address" => $supplier
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
        $table = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($table);
        $validator = Validator::make($request->all(), [
            //'tin' => 'required|alpha_num',
            // 'legal_name' => "required"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $supplier = Supplier::where('id', $request->supplier)->first();
        $supplier->update($request->except('company_id', '_method'));
        $supplier->save();

        return response()->json([
            "status" => true,
            "Supplier" => $supplier,
            "message" => "Supplier updated successfully"
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
        $table = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($table);
        $supplier = Supplier::where('id', $request->supplier)->first();
        if ($supplier == NULL) {
            return response()->json([
                'status' => false,
                'message' => "Supplier not exists!"
            ]);
        }

        if($supplier->delete()){
            return response()->json([
                'status' => true,
                'message' => "Supplier deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again!"
            ]);
        }
    }

    public function batchDelete(Request $request){
        $table = 'company_'.$request->company_id.'_suppliers';
        $validator = Validator::make($request->all(),[
            'ids'=>'required',
        ],[
            'ids.required' => 'Please select entry to delete'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        Supplier::setGlobalTable($table);
        $ids = explode(",", $request->ids);
        Supplier::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => 'Supplier deleted successfull'
            ]);
    }
    
    public function duplicate(Request $request){
        $table = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($table);
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        $supplier = Supplier::find($request->id);
        if(!$supplier){
            return response()->json([
                'status' => false,
                'message' => 'Suppliers not found'
            ]);
        }
        $technicalSupplier = $supplier->replicate();
        $technicalSupplier->created_at = now();
        $technicalSupplier->reference_number  = get_supplier_latest_ref_number($request->company_id, $supplier->reference, 1);
        $technicalSupplier->save();

        return response()->json([
            'status' => true,
            'message' => 'Duplicate Suppliers Successfully',
            'data' => $technicalSupplier
        ]);
    }
}
