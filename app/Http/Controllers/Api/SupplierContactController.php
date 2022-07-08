<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupplierContact;
use Illuminate\Http\Request;
use Validator;

class SupplierContactController extends Controller
{
    public function index(Request $request)
    {
        if(($request->company_id ==  NULL) || ($request->company_id ==  0)){
            return response()->json([
                "status" => false,
                "message" =>  "Please select company"
            ]);
        }

        $supplier_contact =  new SupplierContact;
        SupplierContact::setGlobalTable('company_'.$request->company_id.'_supplier_contacts');
        
        if(SupplierContact::count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }

        return response()->json([
            "status" => true,
            "supplier_contacts" => SupplierContact::get()
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
        $table = 'company_'.$request->company_id.'_supplier_contacts';
        $validator = Validator::make($request->all(), [
            'email' => "required|unique:$table|email",
            'supplier_id' => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $client =  new SupplierContact;
        SupplierContact::setGlobalTable($table) ;
        $client = $client->setTable($table)->create($request->all());

        return response()->json([
            "status" => true,
            "supplier_contacts" => $client,
            "message" => "Supplier contact created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SupplierContact  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
       $supplier_contact =  new SupplierContact;
        SupplierContact::setGlobalTable('company_'.$request->company_id.'_supplier_contacts');
        $supplier_contact = SupplierContact::where('id', $request->supplier_contact)->first();
        return response()->json([
            "status" => true,
            "supplier_contact" => $supplier_contact
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
        $supplier_contact =  new SupplierContact;
        SupplierContact::setGlobalTable('company_'.$request->company_id.'_supplier_contacts');
        $client = SupplierContact::where('id', $request->supplier_contact)->first();
        $client->update($request->except('company_id', '_method'));
        $client->save();

        return response()->json([
            "status" => true,
            "supplier_contact" => $client,
            "message" => "Supplier contact updated successfully"
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
        $supplier_contact =  new SupplierContact;
        SupplierContact::setGlobalTable('company_'.$request->company_id.'_supplier_contacts');
        $contact = SupplierContact::where('id', $request->supplier_contact)->first();

        if($contact->delete()) {
            return response()->json([
                'status' => true,
                'message' => "Supplier contact deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again!"
            ]);
        }
    }
}
