<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpenseAttachment;
use Illuminate\Http\Request;
use Validator;

class ExpenseAttachmentController extends Controller
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
        $table = 'company_'.$request->company_id.'_expense_attachments';
        ExpenseAttachment::setGlobalTable($table);

        if($request->expense_id == NULL){
            if(ExpenseAttachment::count() == 0){
                return response()->json([
                    "status" => false,
                    "message" =>  "No data found"
                ]);
            }
            return response()->json([
                "status" => true,
                "expense_attachments" =>  ExpenseAttachment::get()
            ]);
        }

        if(ExpenseAttachment::where('expense_id', $request->expense_id)->count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }

        return response()->json([
            "status" => true,
            "expense_attachments" =>  ExpenseAttachment::where('expense_id', $request->expense_id)->get()
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
            'expense_id' => 'required',          
            'document' => 'required'          
        ], [
            'expense_id.required' => 'Please select expense. ',
            'document.required' => 'Please select document. ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_expense_attachments';
        ExpenseAttachment::setGlobalTable($table);
        $expense_attachment = ExpenseAttachment::create($request->except('company_id', 'document'));

        $type = "attachment";
        $extension = ['jpg', 'JPG', 'png' ,'PNG' ,'jpeg' ,'JPEG'];

        if(in_array($request->document->extension(), $extension)){
            $type = "image";
        }

        if($request->document != NULL){

            $originName = $request->file('document')->getClientOriginalName();
            $imageName = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/expense/documents'), $imageName);
            $expense_attachment->document = $imageName;
            $expense_attachment->type = $type;

            $expense_attachment->name = $originName;
            $expense_attachment->save();
        }

        return response()->json([
            "status" => true,
            "expense_attachment" => $expense_attachment,
            "message" => "Expense attachment created successfully"
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
        $table = 'company_'.$request->company_id.'_expense_attachments';
        ExpenseAttachment::setGlobalTable($table);
        $expense_attachment = ExpenseAttachment::where('id', $request->expense_attachment)->first();

        if($expense_attachment ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "expense_attachment" => $expense_attachment
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
        $table = 'company_'.$request->company_id.'_expense_attachments';
        ExpenseAttachment::setGlobalTable($table);

        $validator = Validator::make($request->all(),[
            'expense_id' => 'required',          
            // 'document' => 'required'          
        ], [
            'expense_id.required' => 'Please select client. ',
            // 'document.required' => 'Please select document. ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $expense_attachment = ExpenseAttachment::where('id', $request->expense_attachment)->first();
        
        $expense_attachment->update($request->except('company_id', '_method', 'document'));

        if($request->document != NULL){
            if($expense_attachment->getRawOriginal('document')){

                $documentPath = storage_path('app/public/expense/documents/').$expense_attachment->getRawOriginal('document');
                if( file_exists($documentPath) ){
                    unlink($documentPath);
                }
            }
            $originName = $request->file('document')->getClientOriginalName();
            $imageName = time().'.'.$request->document->extension();  
            $request->document->move(storage_path('app/public/expense/documents'), $imageName);
            $expense_attachment->document = $imageName;
            $expense_attachment->name = $originName;
            $expense_attachment->save();
        }

        return response()->json([
            "status" => true,
            "expense_attachment" => $expense_attachment
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
        $table = 'company_'.$request->company_id.'_expense_attachments';
        ExpenseAttachment::setGlobalTable($table);
        $expense_attachment = ExpenseAttachment::where('id', $request->expense_attachment)->first();
        if( $expense_attachment->getRawOriginal('document') ){

            $documentPath = storage_path('app/public/expense/documents/').$expense_attachment->getRawOriginal('document');
            if( file_exists($documentPath) ){
                unlink($documentPath);
            }
        }
        if($expense_attachment->delete()){
            return response()->json([
                'status' => true,
                'message' => "Expense attachment deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again! "
            ]);
        }
    }
}
