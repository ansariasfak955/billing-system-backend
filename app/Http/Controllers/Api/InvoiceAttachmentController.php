<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvoiceAttachment;
use App\Models\Item;
use App\Models\ItemMeta;
use Validator;
use Storage;
class InvoiceAttachmentController extends Controller
{
    public function index( Request $request )
    {
        if(($request->company_id ==  NULL)||($request->company_id ==  0)){
            return response()->json([
                "status" => false,
                "message" =>  "Please select company"
            ]);
        }
        $table = 'company_'.$request->company_id.'_invoice_attachments';
        InvoiceAttachment::setGlobalTable($table);

        if($request->invoice_id == NULL){
            if(InvoiceAttachment::count() == 0){
                return response()->json([
                    "status" => false,
                    "message" =>  "No data found"
                ]);
            }
            return response()->json([
                "status" => true,
                "invoice_attachments" =>  InvoiceAttachment::get()
            ]);
        }

        if(InvoiceAttachment::where('invoice_id', $request->invoice_id)->count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "invoice_attachments" =>  InvoiceAttachment::where('invoice_id', $request->invoice_id)->get()
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
            'invoice_id' => 'required',          
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

        $table = 'company_'.$request->company_id.'_invoice_attachments';
        InvoiceAttachment::setGlobalTable($table);
        $invoice = InvoiceAttachment::create($request->except('company_id', 'document'));

        if($request->document != NULL){
            $originName = $request->file('document')->getClientOriginalName();
            $imageName = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/invoice/documents/'), $imageName);
            $invoice->document = $imageName;
            $invoice->name = $originName;
            $invoice->save();
        }

        return response()->json([
            "status" => true,
            "technical_attachment" => $invoice,
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
        $table = 'company_'.$request->company_id.'_invoice_attachments';
        InvoiceAttachment::setGlobalTable($table);
        $invoice = InvoiceAttachment::where('id', $request->invoice_attachment)->first();

        if($invoice ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "technical_attachment" => $invoice
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
        $table = 'company_'.$request->company_id.'_invoice_attachments';
        InvoiceAttachment::setGlobalTable($table);

        $validator = Validator::make($request->all(),[        
            'document' => 'required'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $invoice = InvoiceAttachment::where('id', $request->invoice_attachment)->first();
        
        $invoice->update($request->except('company_id', '_method', 'document'));

        if($request->document != NULL){
            $originName = $request->file('document')->getClientOriginalName();
            $imageName = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/invoice/documents/'), $imageName);
            $invoice->document = $imageName;
            $invoice->name = $originName;
            $invoice->save();
        }

        return response()->json([
            "status" => true,
            "technical_attachment" => $invoice
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
        $table = 'company_'.$request->company_id.'_invoice_attachments';
        InvoiceAttachment::setGlobalTable($table);
        $invoice = InvoiceAttachment::where('id', $request->invoice_attachment)->first();
        //delete attachment
        if($invoice->document){
            if( file_exists($invoice->document) ){
                unlink($invoice->document);
            }
        }
        if($invoice->delete()){

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
