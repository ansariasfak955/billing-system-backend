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

        $table = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($table);
        $clients = Client::get();

        if ($clients->count() == 0) {
            return response()->json([
                "status" => false,
                "message" => "No clients found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "clients" =>  $clients
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
        $table = 'company_'.$request->company_id.'_clients';
        $validator = Validator::make($request->all(), [
            'email'     => "required|unique:$table|email",
            'reference' => "required",
            'legal_name' => "required|unique:$table",
            'tin' => "required|alpha_num|unique:$table",
            'bank_account_account' => "sometimes|nullable|unique:$table",
            'phone_1' => "sometimes|nullable|unique:$table",
            'phone_2' => "sometimes|nullable|unique:$table",
            'client_category' => "integer"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ]);
        }

        Client::setGlobalTable($table);
        if ($request->reference_number == '') {
            $client = Client::create($request->except(['company_id', 'contacts', 'addresses']));
            $client->reference_number = get_client_latest_ref_number($request->company_id, $request->reference, 1);
            $client->save();

            return response()->json([
                "status" => true,
                "client" => $client,
                "message" => "Client created successfully"
            ]);
        } else {
            $client = Client::where('reference', $request->reference)->where('reference_number', $request->reference_number)->first();

            if ($client == NULL) {
                $client = Client::create($request->except(['company_id', 'contacts', 'addresses']));
                $client->reference_number = get_client_latest_ref_number($request->company_id, $request->reference, 0);
                $client->save();

                return response()->json([
                    "status"  => true,
                    "client"  => $client,
                    "message" => "Client created successfully"
                ]);
            } else {
                return response()->json([
                    "status"  => false,
                    "client"  => $client,
                    "message" => "Please choose different reference number"
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Client  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($table);

        $clientTable = 'company_'.$request->company_id.'_client_attachments';
        Client::setGlobalTable($itemTable);

        $client = Client::with(['client_attachments'])->where('id', $request->client)->first();

        if($client ==  NULL){
            return response()->json([
                "status" => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "client_address" => $client
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
        $table = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($table);
        $validator = Validator::make($request->all(), [
            'tin' => 'required|alpha_num',
            'legal_name' => "required"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $client = Client::where('id', $request->client)->first();
        $client->update($request->except('company_id', '_method'));
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
        $table = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($table);
        $client = Client::where('id', $request->client)->first();
        if ($client == NULL) {
            return response()->json([
                'status' => false,
                'message' => "Client not exists!"
            ]);
        }

        if($client->delete()){
            return response()->json([
                'status' => true,
                'message' => "Client deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again!"
            ]);
        }
    }
}
