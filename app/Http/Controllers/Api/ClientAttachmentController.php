<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientAttachment;
use Illuminate\Http\Request;
use Validator;

class ClientAttachmentController extends Controller
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
        $table = 'company_'.$request->company_id.'_client_attachments';
        ClientAttachment::setGlobalTable($table);

        if($request->client_id == NULL){
            if(ClientAttachment::count() == 0){
                return response()->json([
                    "status" => false,
                    "message" =>  "No data found"
                ]);
            }
            return response()->json([
                "status" => true,
                "client_attachments" =>  ClientAttachment::get()
            ]);
        }

        if(ClientAttachment::where('client_id', $request->client_id)->count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "client_attachments" =>  ClientAttachment::where('client_id', $request->client_id)->get()
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
        $validator = Validator::make($request->all(),[
            'client_id' => 'required',          
            'document' => 'required'          
        ], [
            'client_id.required' => 'Please select client. ',
            'document.required' => 'Please select document. ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_client_attachments';
        ClientAttachment::setGlobalTable($table);
        $client_attachment = ClientAttachment::create($request->except('company_id', 'document'));

        if($request->document != NULL){
            $originName = $request->file('document')->getClientOriginalName();
            $imageName = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/clients/documents'), $imageName);
            $client_attachment->document = $imageName;
            $client_attachment->name = $originName;
            $client_attachment->save();
        }

        return response()->json([
            "status" => true,
            "client_attachment" => $client_attachment,
            "message" => "Client attachment created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClientAttachment  $clientAttachment
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_client_attachments';
        ClientAttachment::setGlobalTable($table);
        $client_attachment = ClientAttachment::where('id', $request->client_attachment)->first();

        if($client_attachment ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "client_attachment" => $client_attachment
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClientAttachment  $clientAttachment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $table = 'company_'.$request->company_id.'_client_attachments';
        ClientAttachment::setGlobalTable($table);

        // $validator = Validator::make($request->all(),[
        //     'client_id' => 'required',          
        //     // 'document' => 'required'          
        // ], [
        //     'client_id.required' => 'Please select client. ',
        //     // 'document.required' => 'Please select document. ',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         "status" => false,
        //         "message" => $validator->errors()->first()
        //     ]);
        // }
        $client_attachment = ClientAttachment::find($request->client_attachment);
        
        if(!$client_attachment){
            return response()->json([
                "status" => false,
                "message" => "Attachment not found!"
            ]);
        }
        $client_attachment->update($request->except('company_id', '_method', 'document'));

        if($request->document != NULL){
            $originName = $request->file('document')->getClientOriginalName();
            $imageName = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/clients/assets'), $imageName);
            $client_attachment->document = $imageName;
            $client_attachment->image = $originName;
            $client_attachment->save();
        }

        return response()->json([
            "status" => true,
            "client_attachment" => $client_attachment
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClientAttachment  $clientAttachment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $table = 'company_'.$request->company_id.'_client_attachments';
        ClientAttachment::setGlobalTable($table);

        $client_attachment = ClientAttachment::find($request->client_attachment);
        
        if(!$client_attachment){
            return response()->json([
                "status" => false,
                "message" => "Attachment not found!"
            ]);
        }
        
        if($client_attachment->delete()){
            return response()->json([
                    'status' => true,
                    'message' => "Client attachment deleted successfully!"
            ]);
        } else {
            return response()->json([
                    'status' => false,
                    'message' => "Retry deleting again! "
            ]);
        }
    }
}
