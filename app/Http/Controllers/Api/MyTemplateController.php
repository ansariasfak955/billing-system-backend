<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MyTemplate;
use App\Models\MyTemplateMeta;
use Validator;

class MyTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        MyTemplate::setGlobalTable('company_'.$request->company_id.'_my_templates');
        return response()->json([
            "status" => true,
            "custom_states" => MyTemplate::get()
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
            'name' => 'required',          
            'document_type' => 'required',          
            'font' => 'required',          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        MyTemplate::setGlobalTable('company_'.$request->company_id.'_my_templates');
        $template = MyTemplate::create($request->except('watermark', 'company_id'));

        if($request->watermark != NULL){
            $watermarkName = time().'.'.$request->watermark->extension();  
            $request->watermark->move(storage_path('app/public/templates/watermark'), $watermarkName);
            $template->watermark = $watermarkName;
            $template->save();
        }

        $template->is_default = $request->is_default;
        $template->hide_company_information = $request->hide_company_information??'0';
        $template->hide_assets_information = $request->hide_assets_information??'0';
        $template->show_signature_box = $request->show_signature_box??'0';
        $template->save();

        $parser = new \Seld\JsonLint\JsonParser();
        $items = $parser->parse($request->metas);
        MyTemplateMeta::setGlobalTable('company_'.$request->company_id.'_my_template_metas');
        foreach($items as $item ){
            MyTemplateMeta::create([
                "template_id" => $template->id,
                "option_name" => $item->option_name,
                "option_value" => $item->option_value
            ]);
        }

        return response()->json([
            "status" => true,
            "template" => $template,
            "message" => "My template created successfully"
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
        MyTemplate::setGlobalTable('company_'.$request->company_id.'_my_templates');
        MyTemplateMeta::setGlobalTable('company_'.$request->company_id.'_my_template_metas');
        $template = MyTemplate::where('id', $request->my_template)->with('metas')->first();
        return response()->json([
            "status" => true,
            "template" => $template
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
        $validator = Validator::make($request->all(),[
            'name' => 'required',          
            'document_type' => 'required',          
            'font' => 'required',          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        MyTemplate::setGlobalTable('company_'.$request->company_id.'_my_templates');
        $template = MyTemplate::where('id', $request->my_template)->first();
        $template->update($request->except('watermark', 'company_id', '_method'));

        if($request->watermark != NULL){
            $watermarkName = time().'.'.$request->watermark->extension();  
            $request->watermark->move(storage_path('app/public/templates/watermark'), $watermarkName);
            $template->watermark = $watermarkName;
            $template->save();
        }

        $template->is_default = $request->is_default??$template->is_default;
        $template->hide_company_information = $request->hide_company_information??$template->hide_company_information;
        $template->hide_assets_information = $request->hide_assets_information??$template->hide_company_information;
        $template->show_signature_box = $request->show_signature_box??$template->hide_company_information;
        $template->save();

        $parser = new \Seld\JsonLint\JsonParser();
        $items = $parser->parse($request->metas);
        MyTemplateMeta::setGlobalTable('company_'.$request->company_id.'_my_template_metas');
        foreach($items as $item ){
            MyTemplateMeta::where('template_id', $template->id)->where('option_name', $item->option_name)->update(["option_value" => $item->option_value]);
        }

        return response()->json([
            "status" => true,
            "template" => $template,
            "message" => "My template updated successfully"
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
        MyTemplate::setGlobalTable('company_'.$request->company_id.'_my_templates');
        MyTemplateMeta::setGlobalTable('company_'.$request->company_id.'_my_template_metas');
        $template = MyTemplate::where('id', $request->my_template)->first();

        if($template->delete()){
            $template->metas()->delete();
            return response()->json([
                    'status' => true,
                    'message' => "Template deleted successfully!"
            ]);
        } else {
            return response()->json([
                    'status' => true,
                    'message' => "Template deleted successfully!"
            ]);
        }
    }

}


