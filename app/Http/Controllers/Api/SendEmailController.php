<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MyTemplate;
use App\Models\Company;
use App\Models\Product;
use App\Models\Item;
use App\Models\SendMail;
use App\Models\SalesEstimate;
use App\Models\MyTemplateMeta;
use App\Models\Reference;
use App\Models\Service;
use PDF;
use Mail;
use Storage;
use Validator;
use App;

class SendEmailController extends Controller
{
    public function sendEmail(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'type' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        $type = urldecode($request->type);
        $pdf = App::make('dompdf.wrapper');
        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);
        $productTable = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($productTable);
        $serviceTable = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($serviceTable);
        $company = Company::where('id', $request->company_id)->first();
        if($type == 'Sales Estimate'){
            $table = 'company_'.$request->company_id.'_sales_estimates';
            SalesEstimate::setGlobalTable($table);
            $salesEstimate = SalesEstimate::with('items')->find($request->id);
            $items =  $salesEstimate->items;
            $total = SalesEstimate::with('items')->where('id',$request->id)->get()->sum('amount');
            $products = [];
            foreach($items as $item){
                if($item->reference == 'PRO'){
                    $parent = Product::find($item->reference_id);
                }elseif($item->reference != 'SER'){
                    $parent = Service::find($item->reference_id);
                }
                $item->reference  = $item->reference. $parent->reference_number ?? '-';
                $products[] = $item;
            }
            
        }


        MyTemplate::setGlobalTable('company_'.$request->company_id.'_my_templates');
        MyTemplateMeta::setGlobalTable('company_'.$request->company_id.'_my_template_metas');
        Reference::setGlobalTable('company_'.$request->company_id.'_references');
        if($request->template_id){
            $template_id = $request->template_id;
            return $template_id;
        }else{
            $template_id = Reference::where('type', $type)->where('by_default', '1')->pluck('template_id')->first();
        }
        $template = MyTemplate::where('id', $template_id)->first(); 
        // return storage_path('fonts');
        
        if($request->send_to){

            return $pdf->loadView('pdf.send_email_template', compact('company', 'products', 'template','salesEstimate', 'total', 'request'))->save('my_stored_file.pdf')->stream();
        }
        
        $salesEstimate = $template->id."/my_stored_file";
        $pdf->loadView('pdf.send_email_template', compact('company', 'products', 'template','salesEstimate', 'total','request'));
        \Storage::put('/public/temp/'.$salesEstimate, $pdf->output());

        return response()->json([
            'status' => true,
            'url' => url('/storage/temp/'.$salesEstimate),
         ]);

        return $pdf->stream();

    }
}
