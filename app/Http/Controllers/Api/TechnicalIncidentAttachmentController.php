<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TechnicalIncidentAttachment;
use Illuminate\Http\Request;
use Validator;
use Storage;

class TechnicalIncidentAttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(($request->company_id ==  0)) {
            return response()->json([
                "status"  => false,
                "message" =>  "Please select company."
            ]);
        }

        $table = 'company_'.$request->company_id.'_technical_incident_attachments';
        TechnicalIncidentAttachment::setGlobalTable($table);
        if(!$request->technical_incident_id){

            $technical_incident_attachments = TechnicalIncidentAttachment::get();
        }else{
            $technical_incident_attachments = TechnicalIncidentAttachment::where('technical_incident_id' , $request->technical_incident_id)->get();
        }
        
        if ($technical_incident_attachments == NULL) {
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }

        return response()->json([
            "status" => true,
            "technical_incident_attachments" => $technical_incident_attachments
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
            'technical_incident_id' => 'required',
            'document'  => 'required'
        ], [
            'technical_incident_id.required' => 'Please select technical incident.',
            'document.required' => 'Please select document.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_technical_incident_attachments';
        TechnicalIncidentAttachment::setGlobalTable($table);
        $technical_incident_attachment = TechnicalIncidentAttachment::create($request->except('company_id', 'document'));

        if($request->document != NULL){
            $originName = $request->file('document')->getClientOriginalName();
            $imageName = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/technical-incidents/documents'), $imageName);
            $technical_incident_attachment->document = $imageName;
            $technical_incident_attachment->name = $originName;
            $technical_incident_attachment->save();
        }

        return response()->json([
            "status" => true,
            "technical_incident_attachment" => $technical_incident_attachment,
            "message" => "Technical Incident created successfully"
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
        $table = 'company_'.$request->company_id.'_technical_incident_attachments';
        TechnicalIncidentAttachment::setGlobalTable($table);
        $incident_attachment = TechnicalIncidentAttachment::where('id', $request->technical_incident_attachment)->first();

        if($incident_attachment ==  NULL){
            return response()->json([
                "status" => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "incident_attachment" => $incident_attachment
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
        $table = 'company_'.$request->company_id.'_technical_incident_attachments';
        TechnicalIncidentAttachment::setGlobalTable($table);

        $incident_attachment = TechnicalIncidentAttachment::find( $request->technical_incident_attachment);

        if ($incident_attachment == NULL) {
            return response()->json([
                "status" => false,
                "message" => "Technical Incident not exists!"
            ]);
        }

        $incident_attachment->update($request->except('company_id', '_method', 'document'));

        if($request->document != NULL){
            $originName = $request->file('document')->getClientOriginalName();
            $doc_name = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/technical-incidents/documents'), $doc_name);
            $incident_attachment->document = $doc_name;
            $incident_attachment->name = $originName;
            $incident_attachment->save();
        }

        return response()->json([
            "status" => true,
            "incident_attachment" => $incident_attachment
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
        $table = 'company_'.$request->company_id.'_technical_incident_attachments';
        TechnicalIncidentAttachment::setGlobalTable($table);
        $incident_attachment = TechnicalIncidentAttachment::where('id', $request->technical_incident_attachment)->first();

        if ($incident_attachment != NULL) {
            if($incident_attachment->delete()){
                if (strpos($incident_attachment->document,'technical-incidents') !== false) {
                    $document_name_arr = explode('/',$incident_attachment->document);
                    $document_name = end($document_name_arr);
                    Storage::delete('public/technical-incidents/documents/'.$document_name.'');
                }
                return response()->json([
                    'status' => true,
                    'message' => "Incident attachment deleted successfully!"
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
                'message' => "Technical Incident attachment not exists!"
            ]);
        }
    }
}