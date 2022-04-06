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
        if(($request->company_id ==  NULL)||($request->company_id ==  0)){
            return response()->json([
                "status" => false,
                "message" =>  "Please select company"
            ]);
        }

        $client =  new Client;
        if($client->setTable('company_'.$request->company_id.'_clients')->count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "clients" =>  $client->setTable('company_'.$request->company_id.'_clients')->get()
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
        $client = $client->setTable($table)->create($request->except(['company_id', 'contacts', 'addresses']));

        

        return response()->json([
            "status" => true,
            "client" => $client,
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
        $client =  new Client;
        $client = $client->setTable('company_'.$request->company_id.'_clients')->where('id', $request->client)->first();
 
        return response()->json([
            "status" => true,
            "client" => $client
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
        $client =  new Client;
        $client = $client->setTable('company_'.$request->company_id.'_clients')->where('id', $request->client)->first();
        
        $client->update($request->except('company_id', '_method'));

        $client->is_default = $request->is_default??$client->is_default;
        $client->save();

        return response()->json([
            "status" => true,
            "client" => $client,
            "message" => "Client updated successfully"
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
        $client = new Client;
        $client = $client->setTable('company_'.$request->company_id.'_clients')->where('id', $request->client)->first();
        if($client->delete()){
            return response()->json([
                    'status' => true,
                    'message' => "Client deleted successfully!"
            ]);
        } else {
            return response()->json([
                    'status' => false,
                    'message' => "Retry deleting again! "
            ]);
        }
    }
}
