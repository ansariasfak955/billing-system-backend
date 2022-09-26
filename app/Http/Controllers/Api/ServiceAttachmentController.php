<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceAttachment;
use Illuminate\Http\Request;
use Validator;

class ServiceAttachmentController extends Controller
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
        $table = 'company_'.$request->company_id.'_service_attachments';
        ServiceAttachment::setGlobalTable($table);

        if($request->service_id == NULL){
            if(ServiceAttachment::count() == 0){
                return response()->json([
                    "status" => false,
                    "message" =>  "No data found"
                ]);
            }
            return response()->json([
                "status" => true,
                "service_attachments" =>  ServiceAttachment::get()
            ]);
        }

        if(ServiceAttachment::where('service_id', $request->service_id)->count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "service_attachments" =>  ServiceAttachment::where('service_id', $request->service_id)->get()
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
            'service_id' => 'required',          
            'document' => 'required'          
        ], [
            'service_id.required' => 'Please select service. ',
            'document.required' => 'Please select document. ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_service_attachments';
        ServiceAttachment::setGlobalTable($table);
        $service_attachment = ServiceAttachment::create($request->except('company_id', 'document'));

        $type = "attachment";
        $extension = ['jpg', 'JPG', 'png' ,'PNG' ,'jpeg' ,'JPEG'];

        if(in_array($request->document->extension(), $extension)){
            $type = "image";
        }

        if($request->document != NULL){
            $originName = $request->file('document')->getClientOriginalName();
            $imageName = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/service/documents/'), $imageName);
            $service_attachment->document = $imageName;
            $service_attachment->type = $type;

            $service_attachment->name = $originName;
            $service_attachment->save();
        }

        return response()->json([
            "status" => true,
            "service_attachment" => $service_attachment,
            "message" => "Service attachment created successfully"
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
        $table = 'company_'.$request->company_id.'_service_attachments';
        ServiceAttachment::setGlobalTable($table);
        $service_attachment = ServiceAttachment::where('id', $request->service_attachment)->first();

        if($service_attachment ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "service_attachment" => $service_attachment
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $table = 'company_'.$request->company_id.'_service_attachments';
        ServiceAttachment::setGlobalTable($table);

        $validator = Validator::make($request->all(),[
            'service_id' => 'required',                   
        ], [
            'service_id.required' => 'Please select service. ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $service_attachment = ServiceAttachment::where('id', $request->service_attachment)->first();
        
        $service_attachment->update($request->except('company_id', '_method', 'document'));

        if($request->document != NULL){
            $originName = $request->file('document')->getClientOriginalName();
            $imageName = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/service/documents/'), $imageName);
            $service_attachment->document = $imageName;
            $service_attachment->image = $originName;
            $service_attachment->save();
        }

        return response()->json([
            "status" => true,
            "service_attachment" => $service_attachment
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
        $table = 'company_'.$request->company_id.'_service_attachments';
        ServiceAttachment::setGlobalTable($table);
        $service_attachment = ServiceAttachment::where('id', $request->service_attachment)->first();
        if($service_attachment->delete()){
            return response()->json([
                'status' => true,
                'message' => "Service attachment deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again! "
            ]);
        }
    }
}
