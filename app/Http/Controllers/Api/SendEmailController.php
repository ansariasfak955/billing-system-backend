<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MyTemplate;
use App\Models\Company;
use App\Models\Product;
use App\Models\Item;
use App\Models\ClientAsset;
use App\Models\PaymentOption;
use App\Models\SalesEstimate;
use App\Models\TechnicalTable;
use App\Models\InvoiceTable;
use App\Models\PurchaseTable;
use App\Models\Setting;
use App\Models\DeliveryOption;
use App\Models\Client;
use App\Models\MyTemplateMeta;
use App\Models\Reference;
use App\Models\Service;
//attachment tables
// use App\Models\ClientAssetAttachmentController;
// use App\Models\ClientAttachment;
// use App\Models\ExpenseAttachment;
// use App\Models\InvoiceAttachment;
// use App\Models\ProductAttachment;
// use App\Models\PurchaseAttachment;
// use App\Models\SalesAttachment;
// use App\Models\TechnicalTable;
// use App\Models\TechnicalTable;
// use App\Models\TechnicalTable;
// use App\Models\TechnicalTable;
use App\Mail\SendMail;
use App\Mail\SendAttachmentsMail;
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
        $table = 'company_'.$request->company_id.'_payment_options';
        PaymentOption::setGlobalTable($table);
        $deliveryOption = 'company_'.$request->company_id.'_delivery_options';
        DeliveryOption::setGlobalTable($deliveryOption);
        $clienTable = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clienTable);
        $company = Company::where('id', $request->company_id)->first();
        if($type == 'Sales Estimate'){
            $table = 'company_'.$request->company_id.'_sales_estimates';
            SalesEstimate::setGlobalTable($table);
            $invoiceData = SalesEstimate::with(['items','payment_options','client'])->find($request->id);
            $items =  $invoiceData->items;
            $total = SalesEstimate::with('items')->where('id',$request->id)->get()->sum('amount');
            $products = [];
            foreach($items as $item){
                if($item->reference == 'PRO'){
                    $parent = Product::find($item->reference_id);
                }elseif($item->reference != 'SER'){
                    $parent = Service::find($item->reference_id);
                }
                $item->reference  = $item->reference. @$parent->reference_number ?? '-';
                $products[] = $item;
            }
            
        }elseif($type == 'Sales Order'){
            $table = 'company_'.$request->company_id.'_sales_estimates';
            SalesEstimate::setGlobalTable($table);
            $invoiceData = SalesEstimate::with(['items','payment_options','client','delivery_options'])->find($request->id);
            $items =  $invoiceData->items;
            $total = SalesEstimate::with('items')->where('id',$request->id)->get()->sum('amount');
            $products = [];
            foreach($items as $item){
                if($item->reference == 'PRO'){
                    $parent = Product::find($item->reference_id);
                }elseif($item->reference != 'SER'){
                    $parent = Service::find($item->reference_id);
                }
                $item->reference  = $item->reference. @$parent->reference_number ?? '-';
                $products[] = $item;
            }
        }elseif($type == 'Sales Delivery Note'){
            $table = 'company_'.$request->company_id.'_sales_estimates';
            SalesEstimate::setGlobalTable($table);
            $invoiceData = SalesEstimate::with('items')->find($request->id);
            $items =  $invoiceData->items;
            $total = SalesEstimate::with('items')->where('id',$request->id)->get()->sum('amount');
            $products = [];
            foreach($items as $item){
                if($item->reference == 'PRO'){
                    $parent = Product::find($item->reference_id);
                }elseif($item->reference != 'SER'){
                    $parent = Service::find($item->reference_id);
                }
                $item->reference  = $item->reference. @$parent->reference_number ?? '-';
                $products[] = $item;
            }
        }elseif($type == 'Work Estimate'){
            $table = 'company_'.$request->company_id.'_technical_tables';
            TechnicalTable::setGlobalTable($table);
            $clientAsset = 'company_'.$request->company_id.'_client_assets';
            ClientAsset::setGlobalTable($clientAsset);
            $invoiceData = TechnicalTable::with(['items','payment_options','client','delivery_options','clientAsset'])->find($request->id);
            $items =  $invoiceData->items;
            $total = TechnicalTable::with('items')->where('id',$request->id)->get()->sum('amount');
            $products = [];
            foreach($items as $item){
                if($item->reference == 'PRO'){
                    $parent = Product::find($item->reference_id);
                }elseif($item->reference == 'SER'){
                    $parent = Service::find($item->reference_id);
                }
                $item->reference  = $item->reference. @$parent->reference_number ?? '-';
                $products[] = $item;
            }
        }elseif($type == 'Work Order'){
            $table = 'company_'.$request->company_id.'_technical_tables';
            TechnicalTable::setGlobalTable($table);
            $clientAsset = 'company_'.$request->company_id.'_client_assets';
            ClientAsset::setGlobalTable($clientAsset);
            $invoiceData = TechnicalTable::with(['items','payment_options','client','delivery_options','clientAsset'])->find($request->id);
            $items =  $invoiceData->items;
            $total = TechnicalTable::with('items')->where('id',$request->id)->get()->sum('amount');
            $products = [];
            foreach($items as $item){
                if($item->reference == 'PRO'){
                    $parent = Product::find($item->reference_id);
                }elseif($item->reference == 'SER'){
                    $parent = Service::find($item->reference_id);
                }
                $item->reference  = $item->reference. @$parent->reference_number ?? '-';
                $products[] = $item;
            }
        }
        elseif($type == 'Work Delivery Note'){
            $table = 'company_'.$request->company_id.'_technical_tables';
            TechnicalTable::setGlobalTable($table);
            $clientAsset = 'company_'.$request->company_id.'_client_assets';
            ClientAsset::setGlobalTable($clientAsset);
            $invoiceData = TechnicalTable::with(['items','payment_options','client','delivery_options','clientAsset'])->find($request->id);
            $items =  $invoiceData->items;
            $total = TechnicalTable::with('items')->where('id',$request->id)->get()->sum('amount');
            $products = [];
            foreach($items as $item){
                if($item->reference == 'PRO'){
                    $parent = Product::find($item->reference_id);
                }elseif($item->reference == 'SER'){
                    $parent = Service::find($item->reference_id);
                }
                $item->reference  = $item->reference. @$parent->reference_number ?? '-';
                $products[] = $item;
            }
        }elseif($type == 'Normal Invoice'){
            $table = 'company_'.$request->company_id.'_invoice_tables';
            InvoiceTable::setGlobalTable($table);
            $clientAsset = 'company_'.$request->company_id.'_client_assets';
            ClientAsset::setGlobalTable($clientAsset);
            $invoiceData = InvoiceTable::with(['items','payment_options','delivery_options','client','clientAsset'])->find($request->id);
            $items =  $invoiceData->items;
            $total = InvoiceTable::with(['items','payment_options','client'])->where('id',$request->id)->get()->sum('amount');
            $products = [];
            foreach($items as $item){
                if($item->reference == 'PRO'){
                    $parent = Product::find($item->reference_id);
                }elseif($item->reference == 'SER'){
                    $parent = Service::find($item->reference_id);
                }
                $item->reference  = $item->reference. @$parent->reference_number ?? '-';
                $products[] = $item;
            }
        }elseif($type == 'Refund Invoice'){
            $table = 'company_'.$request->company_id.'_invoice_tables';
            InvoiceTable::setGlobalTable($table);
            $clientAsset = 'company_'.$request->company_id.'_client_assets';
            ClientAsset::setGlobalTable($clientAsset);
            $invoiceData = InvoiceTable::with('items','clientAsset')->find($request->id);
            // dd($invoiceData);
            $items =  $invoiceData->items;
            $total = InvoiceTable::with('items')->where('id',$request->id)->get()->sum('amount');
            $products = [];
            foreach($items as $item){
                if($item->reference == 'PRO'){
                    $parent = Product::find($item->reference_id);
                }elseif($item->reference == 'SER'){
                    $parent = Service::find($item->reference_id);
                }
                $item->reference  = $item->reference. @$parent->reference_number ?? '-';
                $products[] = $item;
            }
        }elseif($type == 'Purchase Order'){
            $table = 'company_'.$request->company_id.'_purchase_tables';
            PurchaseTable::setGlobalTable($table);
            $invoiceData = PurchaseTable::with('items')->find($request->id);
            $items =  $invoiceData->items;
            $total = PurchaseTable::with('items')->where('id',$request->id)->get()->sum('amount');
            $products = [];
            foreach($items as $item){
                if($item->reference == 'PRO'){
                    $parent = Product::find($item->reference_id);
                }elseif($item->reference == 'SER'){
                    $parent = Service::find($item->reference_id);
                }
                $item->reference  = $item->reference. @$parent->reference_number ?? '-';
                $products[] = $item;
            }
        }elseif($type == 'Purchase Delivery Note'){
            $table = 'company_'.$request->company_id.'_purchase_tables';
            PurchaseTable::setGlobalTable($table);
            $invoiceData = PurchaseTable::with('items')->find($request->id);
            $items =  $invoiceData->items;
            $total = PurchaseTable::with('items')->where('id',$request->id)->get()->sum('amount');
            $products = [];
            foreach($items as $item){
                if($item->reference == 'PRO'){
                    $parent = Product::find($item->reference_id);
                }elseif($item->reference == 'SER'){
                    $parent = Service::find($item->reference_id);
                }
                $item->reference  = $item->reference. @$parent->reference_number ?? '-';
                $products[] = $item;
            }
        }elseif($type == 'Purchase Invoice'){
            $table = 'company_'.$request->company_id.'_purchase_tables';
            PurchaseTable::setGlobalTable($table);
            $invoiceData = PurchaseTable::with('items')->find($request->id);
            $items =  $invoiceData->items;
            $total = PurchaseTable::with('items')->where('id',$request->id)->get()->sum('amount');
            $products = [];
            foreach($items as $item){
                if($item->reference == 'PRO'){
                    $parent = Product::find($item->reference_id);
                }elseif($item->reference == 'SER'){
                    $parent = Service::find($item->reference_id);
                }
                $item->reference  = $item->reference. @$parent->reference_number ?? '-';
                $products[] = $item;
            }
        }


        MyTemplate::setGlobalTable('company_'.$request->company_id.'_my_templates');
        MyTemplateMeta::setGlobalTable('company_'.$request->company_id.'_my_template_metas');
        Reference::setGlobalTable('company_'.$request->company_id.'_references');
        if($request->template_id){
            $template_id = $request->template_id;
        }else{
            $template_id = Reference::where('type', $type)->where('by_default', '1')->pluck('template_id')->first();
        }
        $template = MyTemplate::where('id', $template_id)->first(); 
        // return storage_path('fonts');
        
        $attachment =  str_replace(' ' ,'_',$type).".pdf";
        // $pdf->loadView('pdf.send_email_template', compact('company', 'products', 'template','invoiceData', 'total','request'));
        // $pdf->loadView('pdf.ticket_template', compact('company', 'products', 'template','invoiceData', 'total','request'));
        
        \Storage::put('/public/temp/'.$attachment, $pdf->output());
        if($request->send_to){
            $subject = $request->subject;
            $body = $request->body;
            Mail::to($request->send_to)->send(new SendMail($attachment, $subject , $body, $pdf));
             //delete the file
            if(file_exists(public_path().'/storage/temp/'. $attachment)){
                unlink(public_path().'/storage/temp/'. $attachment);
            }
            return response()->json([
                'status' => true,
                'message' => 'Mail Sent!',
            ]);
        }
        if($request->download){
            
            return $pdf->stream();  
        }
        return response()->json([
            'status' => true,
            'url' => url('/storage/temp/'.$attachment),
        ]);           

    }
    public function sendAttachmentEmail(Request $request){
        $validator = Validator::make($request->all(), [
            'ids' => 'required',
            'type' => 'required',
            'send_to' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        $type = urldecode($request->type);
        if($type == 'client_attachments'){
            $path =public_path().'/storage/clients/documents/';
        }elseif($type == 'client_asset_attachments'){
            $path = public_path().'/storage/clients/assets/documents/';
        }elseif($type == 'expense_attachments'){
            $path = public_path().'/storage/expense/documents/';
        }elseif($type == 'invoice_attachments'){
            $path = public_path().'/storage/invoice/documents/';
        }elseif($type == 'product_attachments'){
            $path = public_path().'/storage/clients/documents/';
        }elseif($type == 'purchase_attachments'){
            $path = public_path().'/storage/suppliers/documents/';
        }
        elseif($type == 'sales_attachments'){
            $path = public_path().'/storage/sales/documents/';
        }
        elseif($type == 'service_attachments'){
            $path = public_path().'/storage/service/documents/';
        }elseif($type == 'supplier_attachments'){
            $path = public_path().'/storage/suppliers/attachments/';
        }elseif($type == 'technical_incident_attachments'){
            $path = public_path().'/storage/technical-incidents/documents/';
        }elseif($type == 'technical_table_attachments'){
            $path = public_path().'/storage/technical/documents/';
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Invalid Attachment Type'
            ]);
        }
        $table = 'company_'.$request->company_id.'_'.$type;
        $totalAttachments = \DB::table($table)->whereIn('id', explode(',', $request->ids) )->get();
        $attachments = [];
        foreach($totalAttachments as $attachment){
            if( file_exists($path.$attachment->document) ){
               $attachments[] = $path.$attachment->document;
            }
        }
        if(empty( $attachments)){
            return response()->json([
                'status' => false,
                'message' => 'No Attachment found on the system to send!',
            ]);
        }
        $subject = $request->subject;
        $body = $request->body;
        Mail::to($request->send_to)->send(new SendAttachmentsMail($attachments, $subject , $body));
        return response()->json([
            'status' => true,
            'message' => 'Mail Sent!',
        ]);
    }
    // Get Message //
    public function getMessage(Request $request){
        Setting::setGlobalTable('company_'.$request->company_id.'_settings');

        return response()->json([
            "status" => true,
            "settings" =>  Setting::pluck('option_value', 'option_name')
        ]);
    }
}
