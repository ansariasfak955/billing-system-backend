<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseAttachment;
use Illuminate\Http\Request;
use Validator;

class PurchaseAttachmentController extends Controller
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
        $table = 'company_'.$request->company_id.'_purchase_attachments';
        PurchaseAttachment::setGlobalTable($table);

        if($request->purchase_id == NULL){
            if(PurchaseAttachment::count() == 0){
                return response()->json([
                    "status" => false,
                    "message" =>  "No data found"
                ]);
            }
            return response()->json([
                "status" => true,
                "purchase_attachments" =>  PurchaseAttachment::get()
            ]);
        }

        if(PurchaseAttachment::where('purchase_id', $request->purchase_id)->count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "purchase_attachments" =>  PurchaseAttachment::where('purchase_id', $request->purchase_id)->get()
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
            'purchase_id' => 'required',          
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

        $table = 'company_'.$request->company_id.'_purchase_attachments';
        PurchaseAttachment::setGlobalTable($table);
        $purchase_attachment = PurchaseAttachment::create($request->except('company_id', 'document'));

        if($request->document != NULL){
            $originName = $request->file('document')->getClientOriginalName();
            $imageName = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/suppliers/documents/'), $imageName);
            $purchase_attachment->document = $imageName;
            $purchase_attachment->name = $originName;
            $purchase_attachment->save();
        }

        return response()->json([
            "status" => true,
            "purchase_attachment" => $purchase_attachment,
            "message" => "Attachment created successfully"
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
        $table = 'company_'.$request->company_id.'_purchase_attachments';
        PurchaseAttachment::setGlobalTable($table);
        $purchase_attachment = PurchaseAttachment::where('id', $request->purchase_attachment)->first();

        if($purchase_attachment ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "purchase_attachment" => $purchase_attachment
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
        $table = 'company_'.$request->company_id.'_purchase_attachments';
        PurchaseAttachment::setGlobalTable($table);

        $validator = Validator::make($request->all(),[        
            'purchase_id' => 'required'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $purchase_attachment = PurchaseAttachment::where('id', $request->purchase_attachment)->first();
        
        $purchase_attachment->update($request->except('company_id', '_method', 'document'));

        if($request->document != NULL){
            $originName = $request->file('document')->getClientOriginalName();
            $imageName = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/suppliers/documents/'), $imageName);
            $purchase_attachment->document = $imageName;
            $purchase_attachment->name = $originName;
            $purchase_attachment->save();
        }

        return response()->json([
            "status" => true,
            "purchase_attachment" => $purchase_attachment
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
        $table = 'company_'.$request->company_id.'_purchase_attachments';
        PurchaseAttachment::setGlobalTable($table);
        $purchase_attachment = PurchaseAttachment::find($request->purchase_attachment);
        if(!$purchase_attachment){
            return response()->json([
                'status' => false,
                'message' => "Not found!"
            ]);
        }
        if( file_exists($purchase_attachment->document) ){
            unlink($purchase_attachment->document);
        }
        if($purchase_attachment->delete()){
            return response()->json([
                    'status' => true,
                    'message' => "Attachment deleted successfully!"
            ]);
        } else {
            return response()->json([
                    'status' => false,
                    'message' => "Retry deleting again! "
            ]);
        }
    }
}
