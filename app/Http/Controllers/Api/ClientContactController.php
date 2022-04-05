<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientContact;
use Illuminate\Http\Request;
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

        $client_contact =  new ClientContact;
        ClientContact::setGlobalTable('company_'.$request->company_id.'_client_contacts');
        
        if(ClientContact::count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }

        return response()->json([
            "status" => true,
            "client_contacts" => ClientContact::get()
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

        $client =  new ClientContact;
        ClientContact::setGlobalTable($table) ;
        $client = $client->setTable($table)->create($request->all());

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
        $client_contact =  new ClientContact;
        $client_contact = $client_contact->setTable('company_'.$request->company_id.'_client_contacts')->where('id', $request->client_contact)->first();
 
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
        $client_contact =  new ClientContact;
        ClientContact::setGlobalTable('company_'.$request->company_id.'_client_contacts');
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
        $client_contact =  new ClientContact;
        ClientContact::setGlobalTable('company_'.$request->company_id.'_client_contacts');
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
