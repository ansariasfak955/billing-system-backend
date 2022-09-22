<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TechnicalTableAttachment;
use Illuminate\Http\Request;
use Validator;

class TechnicalTableAttachmentController extends Controller
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
        $table = 'company_'.$request->company_id.'_technical_table_attachments';
        TechnicalTableAttachment::setGlobalTable($table);

        if($request->asset_id == NULL){
            if(TechnicalTableAttachment::count() == 0){
                return response()->json([
                    "status" => false,
                    "message" =>  "No data found"
                ]);
            }
            return response()->json([
                "status" => true,
                "technical_attachments" =>  TechnicalTableAttachment::get()
            ]);
        }

        if(TechnicalTableAttachment::where('technical_id', $request->technical_id)->count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "technical_attachments" =>  TechnicalTableAttachment::where('technical_id', $request->technical_id)->get()
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
            'technical_id' => 'required',          
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

        $table = 'company_'.$request->company_id.'_technical_table_attachments';
        TechnicalTableAttachment::setGlobalTable($table);
        $technical_attachment = TechnicalTableAttachment::create($request->except('company_id', 'document'));

        if($request->document != NULL){
            $originName = $request->file('document')->getClientOriginalName();
            $imageName = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/technical/documents/'), $imageName);
            $technical_attachment->document = $imageName;
            $technical_attachment->name = $originName;
            $technical_attachment->save();
        }

        return response()->json([
            "status" => true,
            "technical_attachment" => $technical_attachment,
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
        $table = 'company_'.$request->company_id.'_technical_table_attachments';
        TechnicalTableAttachment::setGlobalTable($table);
        $technical_attachment = TechnicalTableAttachment::where('id', $request->technical_table_attachment)->first();

        if($technical_attachment ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "technical_attachment" => $technical_attachment
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
        $table = 'company_'.$request->company_id.'_technical_table_attachments';
        TechnicalTableAttachment::setGlobalTable($table);
        $technical_attachment = TechnicalTableAttachment::find( $request->technical_table_attachment);
        if(!$technical_attachment){
            return response()->json([
                "status" => false,
                "message" => 'Attachment  not found!'
            ]);
        }
        
        $technical_attachment->update($request->except('company_id', '_method', 'document'));

        if($request->document != NULL){
            $originName = $request->file('document')->getClientOriginalName();
            $imageName = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/technical/documents/'), $imageName);
            $technical_attachment->document = $imageName;
            $technical_attachment->name = $originName;
            $technical_attachment->save();
        }

        return response()->json([
            "status" => true,
            "technical_attachment" => $technical_attachment
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
        $table = 'company_'.$request->company_id.'_technical_table_attachments';
        TechnicalTableAttachment::setGlobalTable($table);
        $technical_attachment = TechnicalTableAttachment::where('id', $request->technical_table_attachment)->first();
        if($technical_attachment->delete()){
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
