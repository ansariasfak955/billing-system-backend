<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MyTemplate;
use App\Models\Company;
use App\Models\Product;
use App\Models\MyTemplateMeta;
use Validator;
use App;
use Storage;
use Auth;

class MyTemplateController extends Controller
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
        MyTemplate::setGlobalTable('company_'.$request->company_id.'_my_templates');
        $query = MyTemplate::query();
        if($request->type){
            $query = $query->where('document_type', $request->type);
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
            "templates" => $query
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
        if(isset($request->metas)){
            $parser = new \Seld\JsonLint\JsonParser();
            $items = $parser->parse($request->metas);
            MyTemplateMeta::setGlobalTable('company_'.$request->company_id.'_my_template_metas');
            foreach($items as $key => $value ){
                MyTemplateMeta::create([
                    "template_id" => $template->id,
                    "option_name" => $key,
                    "option_value" => $value
                ]);
            }
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
        $template = MyTemplate::where('id', $request->my_template)->first();
        if(!$template){
            return response()->json([
                "status" => false,
                "message" => "Template not found! "
            ]);
        }
        
        $data = [];
        foreach($template->metas as $meta){
            $data[$meta->option_name] =  $meta->option_value;
        }
        
        return response()->json([
            "status" => true,
            "template" => $template,
            "meta_data" => $data
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
        if(isset($request->metas)){
            $parser = new \Seld\JsonLint\JsonParser();
            $items = $parser->parse($request->metas);
            MyTemplateMeta::setGlobalTable('company_'.$request->company_id.'_my_template_metas');
            foreach($items as $key => $value ){
                MyTemplateMeta::where('template_id', $template->id)->where('option_name', $key)->update(["option_value" => $value]);
            }
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

        $template->metas()->delete();
        if($template->delete()){
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
    public function deleteMytemplateLogo(Request $request)
    {
        // echo "hii";die;
        MyTemplate::setGlobalTable('company_'.$request->company_id.'_my_templates');
        MyTemplateMeta::setGlobalTable('company_'.$request->company_id.'_my_template_metas');

        $avatar = MyTemplate::find($request->id);

            $avatar->update($request->except('watermark'));
            $watermarkName = NULL;
            if($request->watermark != NULL){
                $watermarkName = time().'.'.$request->watermark->extension();  
                $request->watermark->move(storage_path('app/public/templates/watermark'), $watermarkName);
            }
            $avatar->watermark = $watermarkName;
            $avatar->save();
            return response()->json([
                'status' => true,
                'message' => "Template logo update successfully!",
                'data' => $avatar
            ]);
            
    }
    public function bulkDelete(Request $request){
        $validator = Validator::make($request->all(),[
            'ids' => 'required'     
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        MyTemplate::setGlobalTable('company_'.$request->company_id.'_my_templates');
        MyTemplateMeta::setGlobalTable('company_'.$request->company_id.'_my_template_metas');
        $ids = explode(',', $request->ids);
        $template = MyTemplate::whereIn('id', $ids)->delete();
        MyTemplateMeta::whereIn('template_id',$ids)->delete();

        return response()->json([
            'status' => true,
            'message' => "Template deleted successfully!"
        ]);
    }

    public function getTemplateFields(Request $request)
    {
        MyTemplateMeta::setGlobalTable('company_'.$request->company_id.'_my_template_metas');
        $template_metas = MyTemplateMeta::where('template_id', $request->template_id)->groupBy('category')->orderBy('id', 'ASC')->get();
        $arr = [];
        $final_arr = [];
        $templateCounter = 0;

        // Append Hide Company Information
        $arr[0]['id'] = 0;
        $arr[0]['more'] = MyTemplateMeta::where('template_id', $request->template_id)->where('category', 'Company Information')->where('type', 'hide_company_information')->get();

        $hide_company_info[0]['id'] = NULL;
        $hide_company_info[0]['template_id'] = NULL;
        $hide_company_info[0]['option_name'] = NULL;
        $hide_company_info[0]['option_value'] = 'text';
        $hide_company_info[0]['category'] = NULL;
        $hide_company_info[0]['type'] = NULL;
        $arr[0]['more'][2] = $hide_company_info;

        foreach ($template_metas as $template_meta) {
            $types = MyTemplateMeta::where('template_id', $request->template_id)->where('category', $template_meta->category)->groupBy('type')->get();
            if ($template_meta->category == 'Company Information'){
                $counter = 1;
            } else {
                $counter = 0;
            }

            foreach ($types as $type) {
                $arr[$counter]['id'] = $counter;

                // Skip hide company information
                if($type->type == 'hide_company_information'){
                    continue;
                }

                //show 'show' on first
                $moreObject = MyTemplateMeta::where('template_id', $request->template_id)->where('category', $template_meta->category)->where('type', $type->type)->get();
                $showObject = [];
                $otherObject = [];
                $optionName = [];
                foreach($moreObject as $more ){
                    
                    $otherObject[] = $more;
                    $optionName[] = $more->option_name;
                }
                if ($moreObject->count() < 3) {
                    $optionArr = ['show', 'heading', 'text'];
                    $finalArr = array_diff($optionArr,$optionName);
                    $missingValue = array_values($finalArr);
                    $counter_new = 3 - $counter;
                    
                    $otherObject[$counter]['id'] = NULL;
                    $otherObject[$counter]['template_id'] = NULL;
                    $otherObject[$counter]['option_name'] = NULL;
                    $otherObject[$counter]['option_value'] = $missingValue ? $missingValue[0] : '';
                    $otherObject[$counter]['category'] = NULL;
                    $otherObject[$counter]['type'] = NULL;
                }
                
                $arr[$counter]['more'] = array_merge($showObject, $otherObject);
                $counter++;
            }
            $final_arr[$templateCounter]['tab_name'] =  $template_meta->category;
            $final_arr[$templateCounter]['tab_data'] =  $arr;
            //re-initialize array to fix repeated section title  and other repeated fields issue
            $arr =[];
            $templateCounter++;
        }

        return response()->json([
            'status' => true,
            'data'   => $final_arr
        ]);
    }

    public function updateTemplateField(Request $request)
    {
        $table = 'company_'.$request->company_id.'_my_template_metas';
        MyTemplateMeta::setGlobalTable('company_'.$request->company_id.'_my_template_metas');
        $template_meta = MyTemplateMeta::where('template_id', $request->template_id)->where('id', $request->field_id)->first();
        if ($template_meta == NULL) {
            return response()->json([
                'status'  => false,
                'message' => 'Please enter correct template field id!',
            ]);
        }

        MyTemplateMeta::where('template_id', $request->template_id)->where('id', $request->field_id)->update([
            'option_value' => $request->value
        ]);  

        return response()->json([
            'status'  => true,
            'message' => 'Template fields updated successfully!',
        ]);
    }

    public function getTemplatePreview(Request $request)
    {
        $user = \Auth::user(); 
        
        $language = $user->language ?? 'en'; // get the user's language preference, default to English
        \App::setLocale($language);
        
        $pdf = App::make('dompdf.wrapper');
        $company = Company::where('id', $request->company_id)->first();

        $table = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($table);
        $products = Product::limit(2)->get();

        MyTemplate::setGlobalTable('company_'.$request->company_id.'_my_templates');
        MyTemplateMeta::setGlobalTable('company_'.$request->company_id.'_my_template_metas');
        $template = MyTemplate::where('id', $request->template_id)->first();
        // return storage_path('fonts');
        if($request->view == 1){

            return view('pdf.template', compact('company', 'products', 'template'));
        }
        $pdf->loadView('pdf.template', compact('company', 'products', 'template'));
        return $pdf->stream();
    }

    public function duplicateTemplate(Request $request, $id){

        MyTemplate::setGlobalTable('company_'.$request->company_id.'_my_templates');
        MyTemplateMeta::setGlobalTable('company_'.$request->company_id.'_my_template_metas');

        $template = MyTemplate::with('metas')->where('id',  $request->id)->first();
        
        if(!$template){
            return response()->json([
                "status" => false,
                "message" => "Template not found! "
            ]);
        }
        $duplicated_template = $template->replicate();
        $duplicated_template->save();

        if(count( $template->metas)){
            foreach($template->metas as $meta){
               $duplicate_meta =  $meta->replicate();
               $duplicate_meta->template_id =   $duplicated_template->id;
               $duplicate_meta->save();
            }
        }
        return response()->json([
            "status" => true,
            "message" => "Template duplicated! ",
            'data' => MyTemplate::with('metas')->where('id',  $duplicated_template->id)->first()
        ]);
    }
}