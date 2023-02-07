<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupplierBankAccount;
use Validator;

class SupplierBankAccountController extends Controller
{
    public function index(Request $request){
        if(($request->company_id ==  NULL) || ($request->company_id ==  0)){
            return response()->json([
                "status" => false,
                "message" =>  "Please select company"
            ]);
        }

        
        $table = 'company_'.$request->company_id.'_supplier_bank_accounts';
        SupplierBankAccount::setGlobalTable($table);
        $query = SupplierBankAccount::where('supplier_id', $request->supplier_id);
        
        $query = $query->get();
        if (!count($query)) {
            return response()->json([
                "status" => false,
                "message" => "No supplier bank found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "clients" =>  $query
            ]);  
        }
    }
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'supplier_id' => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $table = 'company_'.$request->company_id.'_supplier_bank_accounts';
        SupplierBankAccount::setGlobalTable($table);

        if(!$request->format || $request->format == ''){
            $request['format'] = 'other';
        }
        
        $supplierBank = SupplierBankAccount::create($request->all());
        $supplierBank->is_default = $request->is_default[0] ?? '0';
        $supplierBank->save();
        return response()->json([
            "status" => true,
            "supplier_bank" => $supplierBank,
            "message" => "Supplier bank account created successfully"
        ]);
    }

    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_supplier_bank_accounts';
        SupplierBankAccount::setGlobalTable($table);

       $supplierBank = SupplierBankAccount::find($request->supplier_bank_account);
 
        return response()->json([
            "status" => true,
            "supplier_bank" => $supplierBank
        ]);
    }

    public function update(Request $request)
    {
        $table = 'company_'.$request->company_id.'_supplier_bank_accounts';
        SupplierBankAccount::setGlobalTable($table);
        $supplierBank = SupplierBankAccount::where('id', $request->supplier_bank_account)->first();
        $supplierBank->update($request->except('company_id', '_method'));
        $supplierBank->save();

        return response()->json([
            "status" => true,
            "supplier_bank" => $supplierBank,
            "message" => "Supplier bank updated successfully"
        ]);
    }
    public function destroy(Request $request)
    {
        $table = 'company_'.$request->company_id.'_supplier_bank_accounts';
        SupplierBankAccount::setGlobalTable($table);
        $supplierBank = SupplierBankAccount::where('id', $request->supplier_bank_account)->first();

        if($supplierBank->delete()) {
            return response()->json([
                'status' => true,
                'message' => "Supplier Bank deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again!"
            ]);
        }
    }
}
