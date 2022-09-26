<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupplierAttachment;
use Illuminate\Http\Request;
use Validator;

class SupplierAttachmentController extends Controller
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
        $table = 'company_'.$request->company_id.'_supplier_attachments';
        SupplierAttachment::setGlobalTable($table);
        $query = SupplierAttachment::query();

        if($request->supplier_id){
            $query = $query->where('supplier_id', $request->supplier_id);
        }
        $query = $query->get();
        if(!count($query)){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "supplier_attachments" =>  $query
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
            'supplier_id' => 'required',          
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

        $table = 'company_'.$request->company_id.'_supplier_attachments';
        SupplierAttachment::setGlobalTable($table);
        $supplier_attachment = SupplierAttachment::create($request->except('company_id', 'document'));

        if($request->document != NULL){
            $originName = $request->file('document')->getClientOriginalName();
            $imageName = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/suppliers/attachments/'), $imageName);
            $supplier_attachment->document = $imageName;
            $supplier_attachment->name = $originName;
            $supplier_attachment->save();
        }

        return response()->json([
            "status" => true,
            "supplier_attachment" => $supplier_attachment,
            "message" => " Attachment created successfully"
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
        $table = 'company_'.$request->company_id.'_supplier_attachments';
        SupplierAttachment::setGlobalTable($table);
        $supplier_attachment = SupplierAttachment::where('id', $request->supplier_attachment)->first();

        if($supplier_attachment ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "supplier_attachment" => $supplier_attachment
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
        $table = 'company_'.$request->company_id.'_supplier_attachments';
        SupplierAttachment::setGlobalTable($table);
        $supplier_attachment = SupplierAttachment::find( $request->supplier_attachment);
        if(!$supplier_attachment){
            return response()->json([
                "status" => false,
                "message" => 'Attachment  not found!'
            ]);
        }
        
        $supplier_attachment->update($request->except('company_id', '_method', 'document'));

        if($request->document != NULL){
            $originName = $request->file('document')->getClientOriginalName();
            $imageName = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/suppliers/attachments/'), $imageName);
            $supplier_attachment->document = $imageName;
            $supplier_attachment->name = $originName;
            $supplier_attachment->save();
        }

        return response()->json([
            "status" => true,
            "message" => " Attachment updated successfully",
            "supplier_attachment" => $supplier_attachment
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
        $table = 'company_'.$request->company_id.'_supplier_attachments';
        SupplierAttachment::setGlobalTable($table);
        $supplier_attachment = SupplierAttachment::where('id', $request->supplier_attachment)->first();
        if($supplier_attachment->delete()){
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
