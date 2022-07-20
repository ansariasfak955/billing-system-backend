<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientRate;
use Illuminate\Http\Request;
use Validator;

class ClientRateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(Request $request)
    {
        if(($request->company_id ==  NULL) || ($request->company_id ==  0)){
            return response()->json([
                "status" => false,
                "message" =>  "Please select company"
            ]);
        }

        $client_rate =  new ClientRate;
        ClientRate::setGlobalTable('company_'.$request->company_id.'_client_rates');
        
        if(ClientRate::count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }

        return response()->json([
            "status" => true,
            "client_rates" => ClientRate::get()
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
        $table = 'company_'.$request->company_id.'_client_rates';

        $client =  new ClientRate;
        ClientRate::setGlobalTable($table) ;
        $client = $client->setTable($table)->create($request->all());

        return response()->json([
            "status" => true,
            "client_rates" => $client,
            "message" => "Client Rate created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClientRate  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $client_rate =  new ClientRate;
        $client_rate = $client_rate->setTable('company_'.$request->company_id.'_client_rates')->where('id', $request->client_rate)->first();
 
        return response()->json([
            "status" => true,
            "client_rates" => $client_rate
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
        $client_rate =  new ClientRate;
        ClientRate::setGlobalTable('company_'.$request->company_id.'_client_rates');
        $client = ClientRate::where('id', $request->client_rate)->first();
        $client->update($request->except('company_id', '_method'));
        $client->save();

        return response()->json([
            "status" => true,
            "client_rate" => $client,
            "message" => "Client Rate updated successfully"
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
        $client_rate =  new ClientRate;
        ClientRate::setGlobalTable('company_'.$request->company_id.'_client_rates');
        $contact = ClientRate::where('id', $request->client_rate)->first();

        if($contact->delete()) {
            return response()->json([
                'status' => true,
                'message' => "Client rate deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again!"
            ]);
        }
    }
}
