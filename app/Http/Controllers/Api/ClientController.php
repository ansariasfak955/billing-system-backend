<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientSpecialPrice;
use Illuminate\Http\Request;
use Validator;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // if(($request->company_id ==  NULL)||($request->company_id ==  0)){
        //     return response()->json([
        //         "status" => false,
        //         "message" =>  "Please select company"
        //     ]);
        // }

        // $bank_account =  new Client;
        // if($bank_account->setTable('company_'.$request->company_id.'_bank_accounts')->count() == 0){
        //     return response()->json([
        //         "status" => false,
        //         "message" =>  "No data found"
        //     ]);
        // }
        // return response()->json([
        //     "status" => true,
        //     "bank_accounts" =>  $bank_account->setTable('company_'.$request->company_id.'_bank_accounts')->get()
        // ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $table = 'company_'.$request->company_id.'_clients';
        $validator = Validator::make($request->all(), [
            'email' => "required|unique:$table|email",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $client =  new Client;
        Client::setGlobalTable($table) ;
        $client = $client->setTable($table)->create($request->except(['company_id','client_id','product_id','purchase_price','sales_price','purchase_margin','sales_margin','discount','special_price']));

        $client_special_price =  new ClientSpecialPrice;

        $client_sp_table = 'company_'.$request->company_id.'_special_prices';
        ClientSpecialPrice::setGlobalTable($client_sp_table);
        ClientSpecialPrice::create([
            'client_id' => $request->client_id,
            'product_id' => $request->product_id,
            'purchase_price' => $request->purchase_price,
            'sales_price' => $request->sales_price,
            'purchase_margin' => $request->purchase_margin,
            'sales_margin' => $request->sales_margin,
            'discount' => $request->discount,
            'special_price' => $request->special_price,
        ]);

        return response()->json([
            "status" => true,
            "bank_account" => $client,
            "message" => "Client created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Client  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // $client =  new Client;
        // $client = $client->setTable('company_'.$request->company_id.'_bank_accounts')->where('id', $request->client)->first();
 
        // return response()->json([
        //     "status" => true,
        //     "client" => $client
        // ]);
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
        // $client =  new Client;
        // $client = $client->setTable('company_'.$request->company_id.'_bank_accounts')->where('id', $request->client)->first();
        
        // $client->update($request->except('company_id', '_method'));

        // $client->is_default = $request->is_default??$client->is_default;
        // $client->save();

        // return response()->json([
        //     "status" => true,
        //     "client" => $client,
        //     "message" => "Client updated successfully"
        // ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // $client = new Client;
        // $client = $client->setTable('company_'.$request->company_id.'_bank_accounts')->where('id', $request->client)->first();
        // if($client->delete()){
        //     return response()->json([
        //             'status' => true,
        //             'message' => "Client deleted successfully!"
        //     ]);
        // } else {
        //     return response()->json([
        //             'status' => false,
        //             'message' => "Retry deleting again! "
        //     ]);
        // }
    }
}
