<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientAddress;
use Illuminate\Http\Request;
use Validator;

class ClientAddressController extends Controller
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

        $table = 'company_'.$request->company_id.'_client_addresses';
        ClientAddress::setGlobalTable($table);
        $client_addresses = ClientAddress::get();

        if ($client_addresses->count() == 0) {
            return response()->json([
                "status" => false,
                "message" => "No address found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "client_addresses" =>  $client_addresses
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

        $table = 'company_'.$request->company_id.'_client_addresses';
        ClientAddress::setGlobalTable($table);
        $client_address = ClientAddress::create($request->except('company_id', 'type'));
        
        $client_address->type = $request->type == NULL ? "other": $request->type;
        $client_address->save();

        return response()->json([
            "status" => true,
            "client_address" => $client_address,
            "message" => "Client address created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClientAddress  $clientAddress
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_client_addresses';
        ClientAddress::setGlobalTable($table);
        $client_address = ClientAddress::where('id', $request->client_address)->first();

        if($client_address ==  NULL){
            return response()->json([
                "status" => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "client_address" => $client_address
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClientAddress  $clientAddress
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $table = 'company_'.$request->company_id.'_client_addresses';
        ClientAddress::setGlobalTable($table);
        $client_address = ClientAddress::where('id', $request->client_address)->first();
        
        $client_address->update($request->except('company_id', 'client_address'));
        $client_address->save();

        return response()->json([
            "status" => true,
            "client" => $client_address,
            "message" => "Client address updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClientAddress  $clientAddress
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $table = 'company_'.$request->company_id.'_client_addresses';
        ClientAddress::setGlobalTable($table);
        $client_address = ClientAddress::where('id', $request->client_address)->first();

        if($client_address->delete()){
            return response()->json([
                'status' => true,
                'message' => "Client address deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "There is an error!"
            ]);
        }
    }
}
