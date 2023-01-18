<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DefaultPdfSendOption;

class DefaultPdfSendController extends Controller
{
    public function index(Request $request)
    {
        if(($request->company_id ==  NULL)||($request->company_id ==  0)){
            return response()->json([
                "status" => false,
                "message" =>  "Please select company"
            ]);
        }
        DefaultPdfSendOption::setGlobalTable('company_'.$request->company_id.'_default_pdf_send_options');
        $query = DefaultPdfSendOption::query();
    
        $query = $query->get();
        if(!count($query)){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "default_pdf_option" => $query
        ]);
    }
    public function update(Request $request){
        $table = 'company_'.$request->company_id.'_default_pdf_send_options';
        DefaultPdfSendOption::setGlobalTable($table);
        $defaultPdfSendOptions = DefaultPdfSendOption::where('id', $request->default_pdf)->first();
        $defaultPdfSendOptions->update($request->except('company_id', '_method'));
        $defaultPdfSendOptions->save();

        return response()->json([
            'status' => true,
            'message' => 'Default pdf send option update successfull',
            'data' => $defaultPdfSendOptions
        ]);
    }
    public function show(Request $request){
        $table = 'company_'.$request->company_id.'_default_pdf_send_options';
        DefaultPdfSendOption::setGlobalTable($table);

        $defaultPdfSendOptions = DefaultPdfSendOption::find($request->default_pdf);

        return response()->json([
            'status' => true,
            'data' => $defaultPdfSendOptions
        ]);
    }

}
