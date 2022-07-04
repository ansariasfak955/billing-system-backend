<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesAttachment;
use Illuminate\Http\Request;
use Validator;

class SalesAttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(($request->sales_id ==  NULL)||($request->company_id ==  0)){
            return response()->json([
                "status" => false,
                "message" =>  "Please select sales."
            ]);
        }

        $table = 'company_'.$request->company_id.'_sales_attachments';
        SalesAttachment::setGlobalTable($table);

        if($request->sales_id == NULL){
            if(SalesAttachment::count() == 0){
                return response()->json([
                    "status" => false,
                    "message" =>  "No data found"
                ]);
            }
            return response()->json([
                "status" => true,
                "sale_attachments" => SalesAttachment::get()
            ]);
        }

        if(SalesAttachment::where('sales_id', $request->sales_id)->count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }

        return response()->json([
            "status" => true,
            "sale_attachments" =>  SalesAttachment::where('sales_id', $request->sales_id)->get()
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
            'sales_id' => 'required',
            'document'  => 'required'
        ], [
            'sales_id.required' => 'Please select sales.',
            'document.required' => 'Please select document.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_sales_attachments';
        SalesAttachment::setGlobalTable($table);
        $sale_attachment = SalesAttachment::create($request->except('company_id', 'document'));

        if($request->document != NULL){
            $originName = $request->file('document')->getClientOriginalName();
            $imageName = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/sales/documents'), $imageName);
            $sale_attachment->document = $imageName;
            $sale_attachment->name = $originName;
            $sale_attachment->save();
        }

        return response()->json([
            "status" => true,
            "sale_attachment" => $sale_attachment,
            "message" => "Sales attachment created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SalesAttachment  $SaleAttachment
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_sales_attachments';
        SalesAttachment::setGlobalTable($table);
        $sale_attachment = SalesAttachment::where('id', $request->sales_attachment)->first();

        if($sale_attachment ==  NULL){
            return response()->json([
                "status" => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "sale_attachment" => $sale_attachment
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SalesAttachment  $SaleAttachment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $table = 'company_'.$request->company_id.'_sales_attachments';
        SalesAttachment::setGlobalTable($table);

        $validator = Validator::make($request->all(),[
            'sales_id' => 'required',          
            'document' => 'required'          
        ], [
            'sales_id.required' => 'Please select sales.',
            'document.required' => 'Please select document.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $sale_attachment = SalesAttachment::where('id', $request->sales_attachment)->first();

        if ($sale_attachment == NULL) {
            return response()->json([
                "status" => false,
                "message" => "Sales attachment not exists!"
            ]);
        }

        $sale_attachment->update($request->except('company_id', '_method', 'document'));

        if($request->document != NULL){
            $originName = $request->file('document')->getClientOriginalName();
            $doc_name = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/sales/documents'), $doc_name);
            $sale_attachment->document = $doc_name;
            $sale_attachment->name = $originName;
            $sale_attachment->save();
        }

        return response()->json([
            "status" => true,
            "sale_attachment" => $sale_attachment
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SalesAttachment  $SaleAttachment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $table = 'company_'.$request->company_id.'_sales_attachments';
        SalesAttachment::setGlobalTable($table);
        $sale_attachment = SalesAttachment::where('id', $request->sales_attachment)->first();
        if ($sale_attachment != NULL) {
            if($sale_attachment->delete()){
                return response()->json([
                    'status' => true,
                    'message' => "Sales attachment deleted successfully!"
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => "There is an error!"
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => "Sales attachment not exists!"
            ]);
        }
    }
}