<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
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

        $bank_account =  new BankAccount;
        if($bank_account->setTable('company_'.$request->company_id.'_bank_accounts')->count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "bank_accounts" =>  $bank_account->setTable('company_'.$request->company_id.'_bank_accounts')->get()
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
        $bank_account =  new BankAccount;
        $bank_account = $bank_account->setTable('company_'.$request->company_id.'_bank_accounts')->create($request->except('company_id'));

        $bank_account->is_default = $request->is_default??'0';
        $bank_account->save();

        return response()->json([
            "status" => true,
            "bank_account" => $bank_account,
            "message" => "Bank account created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BankAccount  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {

        $bank_account =  new BankAccount;
        $bank_account = $bank_account->setTable('company_'.$request->company_id.'_bank_accounts')->where('id', $request->bank_account)->first();
 
        return response()->json([
            "status" => true,
            "bank_account" => $bank_account
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

        $bank_account =  new BankAccount;
        $bank_account = $bank_account->setTable('company_'.$request->company_id.'_bank_accounts')->where('id', $request->bank_account)->first();
        
        $bank_account->update($request->except('company_id', '_method'));

        $bank_account->is_default = $request->is_default??$bank_account->is_default;
        $bank_account->save();

        return response()->json([
            "status" => true,
            "bank_account" => $bank_account,
            "message" => "Bank account updated successfully"
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
        $bank_account =  new BankAccount;
        $bank_account = $bank_account->setTable('company_'.$request->company_id.'_bank_accounts')->where('id', $request->bank_account)->first();
        if($bank_account->delete()){
            return response()->json([
                    'status' => true,
                    'message' => "Bank account deleted successfully!"
            ]);
        } else {
            return response()->json([
                    'status' => false,
                    'message' => "Retry deleting again! "
            ]);
        }
    }
}
