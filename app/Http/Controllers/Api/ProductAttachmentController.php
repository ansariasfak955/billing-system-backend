<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductAttachment;
use Illuminate\Http\Request;
use Validator;

class ProductAttachmentController extends Controller
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
        $table = 'company_'.$request->company_id.'_product_attachments';
        ProductAttachment::setGlobalTable($table);

        if($request->product_id == NULL){
            if(ProductAttachment::count() == 0){
                return response()->json([
                    "status" => false,
                    "message" =>  "No data found"
                ]);
            }
            return response()->json([
                "status" => true,
                "product_attachments" =>  ProductAttachment::get()
            ]);
        }

        if(ProductAttachment::where('product_id', $request->product_id)->count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "product_attachments" =>  ProductAttachment::where('product_id', $request->product_id)->get()
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
            'product_id' => 'required',          
            'document' => 'required'          
        ], [
            'product_id.required' => 'Please select client. ',
            'document.required' => 'Please select document. ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_product_attachments';
        ProductAttachment::setGlobalTable($table);
        $product_attachment = ProductAttachment::create($request->except('company_id', 'document'));

        $type = "attachment";
        $extension = ['jpg', 'JPG', 'png' ,'PNG' ,'jpeg' ,'JPEG'];

        if(in_array($request->document->extension(), $extension)){
            $type = "image";
        }

        if($request->document != NULL){
            $originName = $request->file('document')->getClientOriginalName();
            $imageName = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/clients/documents'), $imageName);
            $product_attachment->document = $imageName;
            $product_attachment->type = $type;

            $product_attachment->name = $originName;
            $product_attachment->save();
        }

        return response()->json([
            "status" => true,
            "product_attachment" => $product_attachment,
            "message" => "Product attachment created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductAttachment  $ProductAttachment
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_product_attachments';
        ProductAttachment::setGlobalTable($table);
        $product_attachment = ProductAttachment::where('id', $request->product_attachment)->first();

        if($product_attachment ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "product_attachment" => $product_attachment
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductAttachment  $ProductAttachment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $table = 'company_'.$request->company_id.'_product_attachments';
        ProductAttachment::setGlobalTable($table);

        $validator = Validator::make($request->all(),[
            'product_id' => 'required',          
            // 'document' => 'required'          
        ], [
            'product_id.required' => 'Please select client. ',
            // 'document.required' => 'Please select document. ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $product_attachment = ProductAttachment::where('id', $request->product_attachment)->first();
        
        $product_attachment->update($request->except('company_id', '_method', 'document'));

        if($request->document != NULL){
            $originName = $request->file('document')->getClientOriginalName();
            $imageName = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/clients/assets'), $imageName);
            $product_attachment->document = $imageName;
            $product_attachment->image = $originName;
            $product_attachment->save();
        }

        return response()->json([
            "status" => true,
            "product_attachment" => $product_attachment
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductAttachment  $ProductAttachment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $table = 'company_'.$request->company_id.'_product_attachments';
        ProductAttachment::setGlobalTable($table);
        $product_attachment = ProductAttachment::where('id', $request->product_attachment)->first();
        if($product_attachment->delete()){
            return response()->json([
                'status' => true,
                'message' => "Product attachment deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again! "
            ]);
        }
    }
}
