<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deposit;
use App\Models\Client;
use App\Exports\InvoiceDepositWithdrawExport;
use Validator;
use Excel;

class DepositController extends Controller
{
    public function index(Request $request){
        if(($request->company_id ==  NULL) || ($request->company_id ==  0)){
            return response()->json([
                "status" => false,
                "message" =>  "Please select company"
            ]);
        }
        $table = 'company_'.$request->company_id.'_deposits';
        Deposit::setGlobalTable($table);

        $query = Deposit::query();
        $query = $query->filter($request->all())->get();

        if(!count($query)){
            return response()->json([
                'status' => false,
                'message' => 'Invoice not found'
            ]);
        }else{
            return response()->json([
                'status' => true,
                'data' => $query
            ]);
        }


    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'client_id' => 'required',
            'amount' => 'required'
        ],[
            'amount.required' => 'The amount of this deposit must be higher than 0.'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        $table = 'company_'.$request->company_id.'_deposits';
        Deposit::setGlobalTable($table);

        $invoiceDeposit = Deposit::create($request->all());

        return response()->json([
            'status' => true,
            'data' => $invoiceDeposit
        ]);
    }
    public function show(Request $request){
        $table = 'company_'.$request->company_id.'_deposits';
        Deposit::setGlobalTable($table);

        $invoiceDeposit = Deposit::find($request->deposit);

        return response()->json([
            'status' => true,
            'data' => $invoiceDeposit
        ]);
    }
    public function export(Request $request, $company_id){
        $validator = Validator::make($request->all(), [
            'ids' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        $table = 'company_'.$request->company_id.'_deposits';
        Deposit::setGlobalTable($table);

        $fileName = 'Account-Transactions-'.time().$company_id.'.xlsx';
        $ids = explode(',', $request->ids);
        $invoiceDeposits = Deposit::whereIn('id', $ids)->get();

        Excel::store(new InvoiceDepositWithdrawExport($invoiceDeposits), 'public/xlsx/'.$fileName);

        return response()->json([
            'status' => true,
            'url' => url('/storage/xlsx/'.$fileName),
        ]);
    }
}
