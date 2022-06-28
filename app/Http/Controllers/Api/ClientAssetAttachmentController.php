<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientAssetAttachment;
use Illuminate\Http\Request;
use Validator;

class ClientAssetAttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        if(($request->company_id ==  NULL)||($request->company_id ==  0)){
            return response()->json([
                "status" => false,
                "message" =>  "Please select company"
            ]);
        }
        $table = 'company_'.$request->company_id.'_client_asset_attachments';
        ClientAssetAttachment::setGlobalTable($table);

        if($request->asset_id == NULL){
            if(ClientAssetAttachment::count() == 0){
                return response()->json([
                    "status" => false,
                    "message" =>  "No data found"
                ]);
            }
            return response()->json([
                "status" => true,
                "client_attachments" =>  ClientAssetAttachment::get()
            ]);
        }

        if(ClientAssetAttachment::where('asset_id', $request->asset_id)->count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "client_attachments" =>  ClientAssetAttachment::where('asset_id', $request->asset_id)->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'asset_id' => 'required',          
            'document' => 'required'          
        ], [
            'document.required' => 'Please select document. ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_client_asset_attachments';
        ClientAssetAttachment::setGlobalTable($table);
        $client_attachment = ClientAssetAttachment::create($request->except('company_id', 'document'));

        if($request->document != NULL){
            $imageName = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/assets/documents/'), $imageName);
            $client_attachment->document = $imageName;
            $client_attachment->save();
        }

        return response()->json([
            "status" => true,
            "client_attachment" => $client_attachment,
            "message" => "Client asset attachment created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_client_asset_attachments';
        ClientAssetAttachment::setGlobalTable($table);
        $client_attachment = ClientAssetAttachment::where('id', $request->client_asset_attachment)->first();

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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $table = 'company_'.$request->company_id.'_client_asset_attachments';
        ClientAssetAttachment::setGlobalTable($table);

        $validator = Validator::make($request->all(),[        
            'document' => 'required'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $client_attachment = ClientAssetAttachment::where('id', $request->client_asset_attachment)->first();
        
        $client_attachment->update($request->except('company_id', '_method', 'document'));

        if($request->document != NULL){
            $imageName = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/assets/documents/'), $imageName);
            $client_attachment->document = $imageName;
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $table = 'company_'.$request->company_id.'_client_asset_attachments';
        ClientAssetAttachment::setGlobalTable($table);
        $client_attachment = ClientAssetAttachment::where('id', $request->client_asset_attachment)->first();
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
