<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientContact;
use Illuminate\Http\Request;
use App\Models\Client;
use Validator;

class ClientContactController extends Controller
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

        
        $table = 'company_'.$request->company_id.'_client_contacts';
        ClientContact::setGlobalTable($table);
        $table = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($table);
        $query = ClientContact::query();
        
        $query = $query->filter($request->all())->get();
        if (!count($query)) {
            return response()->json([
                "status" => false,
                "message" => "No clients found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "clients" =>  $query
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
        $table = 'company_'.$request->company_id.'_client_contacts';
        $validator = Validator::make($request->all(), [
            'email' => "required|unique:$table|email",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        ClientContact::setGlobalTable($table);
        $client = ClientContact::create($request->all());

        return response()->json([
            "status" => true,
            "client_contacts" => $client,
            "message" => "Client contact created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClientContact  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_client_contacts';
        ClientContact::setGlobalTable($table);

       $client_contact = ClientContact::find($request->client_contact);
 
        return response()->json([
            "status" => true,
            "client_contact" => $client_contact
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
        $table = 'company_'.$request->company_id.'_client_contacts';
        ClientContact::setGlobalTable($table);
        $client = ClientContact::where('id', $request->client_contact)->first();
        $client->update($request->except('company_id', '_method'));
        $client->save();

        return response()->json([
            "status" => true,
            "client_contact" => $client,
            "message" => "Client contact updated successfully"
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
        $table = 'company_'.$request->company_id.'_client_contacts';
        ClientContact::setGlobalTable($table);
        $contact = ClientContact::where('id', $request->client_contact)->first();

        if($contact->delete()) {
            return response()->json([
                'status' => true,
                'message' => "Client contact deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again!"
            ]);
        }
    }
}
