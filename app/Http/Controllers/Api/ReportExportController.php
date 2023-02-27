<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Item;
use App\Models\ItemMeta;
use App\Models\InvoiceTable;
use App\Models\PaymentOption;
use App\Models\SalesAttachment;
use App\Models\SalesEstimate;
use App\Models\PurchaseTable;
use App\Models\TechnicalTable;
use App\Models\InvoiceReceipt;
use App\Models\Company;
use App\Models\User;
use App\Models\Reference;
use App\Models\Service;
use App\Models\PurchaseReceipt;
use App\Models\Supplier;
use App\Models\ConsumptionTax;
use App\Models\Product;
use App\Models\TechnicalIncident;
use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Exports\ReportExport\SalesClientExport;
use App\Exports\ReportExport\SalesAgentsExport;
use App\Exports\ReportExport\SalesItemsExport;
use App\Exports\ReportExport\InvoiceClientExport;
use App\Exports\ReportExport\InvoiceAgentExport;
use App\Exports\ReportExport\InvoiceItemExport;
use App\Exports\ReportExport\CashFlowExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Validator;

class ReportExportController extends Controller
{
    public function clientSalesExport(Request $request, $company_id){
        $validator = Validator::make($request->all(),[
            'type' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $salesTables = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($salesTables);

        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables);

        $table = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $table = 'company_'.$request->company_id.'_users';
        User::setGlobalTable($table);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);

        $fileName = 'CLIENTSALESREPORT-'.time().$company_id.'.xlsx';

            $clients = Client::get();

            $referenceType = Reference::where('type', $request->type)->pluck('prefix')->toArray();
            $arr = [];
            $data = [];

            foreach($clients as $client){
                $arr['name'] = $client->legal_name;
                $arr['currency'] = SalesEstimate::where('reference', $referenceType)->where('client_id',$client->id)->where('currency')->first();
                $arr['after_tax'] = '';
                $arr['according_to'] = $client->reference_type;
                $arr['start_date'] = SalesEstimate::where('reference', $referenceType)->where('client_id',$client->id)->where('date')->first();
                $arr['end_date'] = SalesEstimate::where('reference', $referenceType)->where('client_id',$client->id)->where('date')->first();
                $arr['document_type'] = SalesEstimate::where('reference', $referenceType)->where('client_id',$client->id)->where('date')->first();
                $arr['pending'] = SalesEstimate::where('reference', $referenceType)->where('client_id',$client->id)->where('status','pending')->count();
                $arr['refused'] = SalesEstimate::where('reference', $referenceType)->where('client_id',$client->id)->where('status','refused')->count();
                $arr['accepted'] = SalesEstimate::where('reference', $referenceType)->where('client_id',$client->id)->where('status','accepted')->count();
                $arr['closed'] = SalesEstimate::where('reference', $referenceType)->where('client_id',$client->id)->where('status','closed')->count();
                $arr['total'] = SalesEstimate::where('reference', $referenceType)->where('client_id',$client->id)->count();
                $arr['amount'] = SalesEstimate::where('reference', $referenceType)->where('client_id',$client->id)->get()->sum('amount');

                $clientSalesExports[] = $arr;
            }
            Excel::store(new SalesClientExport($clientSalesExports), 'public/xlsx/'.$fileName);

            
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]); 
    }

    public function salesAgentsExport(Request $request, $company_id){
        $validator = Validator::make($request->all(),[
            'type' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        $salesTables = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($salesTables);

        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables);

        $table = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $table = 'company_'.$request->company_id.'_users';
        User::setGlobalTable($table);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);

        $fileName = 'CLIENTSALESREPORT-'.time().$company_id.'.xlsx';

        $referenceType = Reference::where('type',$request->type)->pluck('prefix')->toArray();
        $clients = SalesEstimate::where('reference', $referenceType)->get();
                $arr = [];
                $data = [];

                $arr['legal_name'] = \Auth::user()->name;
                $arr['pending'] = SalesEstimate::where('reference', $referenceType)->where('agent_id',\Auth::id())->where('status','pending')->count();
                $arr['refused'] = SalesEstimate::where('reference', $referenceType)->where('agent_id',\Auth::id())->where('status','refused')->count();
                $arr['accepted'] = SalesEstimate::where('reference', $referenceType)->where('agent_id',\Auth::id())->where('status','accepted')->count();
                $arr['closed'] = SalesEstimate::where('reference', $referenceType)->where('agent_id',\Auth::id())->where('status','closed')->count();
                $arr['total'] = SalesEstimate::where('reference', $referenceType)->where('agent_id',\Auth::id())->count();
                $arr['amount'] = SalesEstimate::where('reference', $referenceType)->where('agent_id',\Auth::id())->get()->sum('amount');

                $agentsSalesExports[] = $arr;
            
            Excel::store(new SalesAgentsExport($agentsSalesExports), 'public/xlsx/'.$fileName);

            
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]); 
    }
    public function itemsSalesExport(Request $request, $company_id){
        $salesTables = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($salesTables);

        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables);

        $table = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $table = 'company_'.$request->company_id.'_users';
        User::setGlobalTable($table);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);

        $products = Product::get();
        $services = Service::get();
        // dd($products);
            $referenceType = Reference::where('type', $request->type)->pluck('prefix')->toArray();
            // $clients = SalesEstimate::where('reference', $referenceType)->get();

            $fileName = 'ITEMSSALESREPORT-'.time().$company_id.'.xlsx';

            $arr = [];
            $data = [];

            foreach($products as $product){
                $arr['name'] = $product->name;
                $arr['reference'] = $product->reference.''.$product->reference_number;
                $arr['pending'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->where('type', $referenceType);
                })->where('status','pending')->count();
                $arr['accepted'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->where('type', $referenceType);
                })->where('status','accepted')->count();
                $arr['closed'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->where('type', $referenceType);
                })->where('status','closed')->count();
                $arr['refused'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->where('type', $referenceType);
                })->where('status','refused')->count();
                $arr['total'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->where('type', $referenceType);
                })->count();
                $arr['amount'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->where('type', $referenceType);
                })->get()->sum('amount');
                

                $itemsSalesExports[] = $arr;
            }
            foreach($services as $service){
                $arr['name'] = $service->name;
                $arr['reference'] = $service->reference.''.$service->reference_number;
                $arr['pending'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                    $query->where('reference_id', $service->id)->where('type', $referenceType);
                })->where('status','pending')->count();
                $arr['accepted'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                    $query->where('reference_id', $service->id)->where('type', $referenceType);
                })->where('status','accepted')->count();
                $arr['closed'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                    $query->where('reference_id', $service->id)->where('type', $referenceType);
                })->where('status','closed')->count();
                $arr['refused'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                    $query->where('reference_id', $service->id)->where('type', $referenceType);
                })->where('status','refused')->count();
                $arr['total'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                    $query->where('reference_id', $service->id)->where('type', $referenceType);
                })->count();
                $arr['amount'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                    $query->where('reference_id', $service->id)->where('type', $referenceType);
                })->get()->sum('amount');

                $itemsSalesExports[] = $arr;
            }
            Excel::store(new SalesItemsExport($itemsSalesExports), 'public/xlsx/'.$fileName);

            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]); 

    }
    public function invoiceClientExport(Request $request, $company_id){
        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables);

        $table = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);
        
            $clients = Client::get();

            $referenceType = Reference::where('type', $request->type)->pluck('prefix')->toArray();

            $fileName = 'INVOICECLIENTREPORT-'.time().$company_id.'.xlsx';

            $arr = [];
            $data = [];

            foreach($clients as $client){
                $arr['name'] = $client->legal_name;
                $arr['invoiced'] = InvoiceTable::where('reference', $referenceType)->where('client_id',$client->id)->get()->sum('amount');
                $arr['paid'] = InvoiceTable::where('reference', $referenceType)->where('client_id',$client->id)->get()->sum('amount_paid');
                $arr['Unpaid'] = InvoiceTable::where('reference', $referenceType)->where('client_id',$client->id)->get()->sum('amount_due');

                $invoiceClientsExports[] = $arr;
            }
            
            Excel::store(new InvoiceClientExport($invoiceClientsExports), 'public/xlsx/'.$fileName);

            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
    }
    public function invoiceAgentsExport(Request $request, $company_id){
        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables);

        $table = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);
        
            $clients = Client::get();

            $referenceType = Reference::where('type', $request->type)->pluck('prefix')->toArray();
            $fileName = 'INVOICEAGENTSREPORT-'.time().$company_id.'.xlsx';
                $arr = [];
                $data = [];

                $arr['name'] = \Auth::user()->name;
                $arr['invoiced'] = InvoiceTable::where('reference', $referenceType)->where('agent_id',\Auth::id())->get()->sum('amount');
                $arr['paid'] = InvoiceTable::where('reference', $referenceType)->where('agent_id',\Auth::id())->get()->sum('amount_paid');
                $arr['Unpaid'] = InvoiceTable::where('reference', $referenceType)->where('agent_id',\Auth::id())->get()->sum('amount_due');

                $invoiceAgentsExports[] = $arr;
            
                Excel::store(new InvoiceAgentExport($invoiceAgentsExports), 'public/xlsx/'.$fileName);

                return response()->json([
                    'status' => true,
                    'url' => url('/storage/xlsx/'.$fileName),
                 ]);
    }
    public function invoiceItemsExport(Request $request, $company_id){
        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTable);

        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables);

        $table = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $table = 'company_'.$request->company_id.'_users';
        User::setGlobalTable($table);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);

        $products = Product::get();
        $services = Service::get();
        // dd($products);
            $referenceType = Reference::where('type', $request->type)->pluck('prefix')->toArray();
            // $clients = SalesEstimate::where('reference', $referenceType)->get();
            $fileName = 'INVOICEITEMSREPORT-'.time().$company_id.'.xlsx';
            $arr = [];
            $data = [];

            foreach($products as $product){
                $arr['name'] = $product->name;
                $arr['reference'] = $product->reference.''.$product->reference_number;
                $arr['units'] = '';
                $arr['amount'] = InvoiceTable::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->where('type', $referenceType);
                })->get()->sum('amount_with_out_vat');
                

                $invoiceItemsExports[] = $arr;
            }
            foreach($services as $service){
                $arr['name'] = $service->name;
                $arr['reference'] = $service->reference.''.$service->reference_number;
                $arr['units'] = '';
                $arr['amount'] = InvoiceTable::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                    $query->where('reference_id', $service->id)->where('type', $referenceType);
                })->get()->sum('amount_with_out_vat');

                $invoiceItemsExports[] = $arr;
            }

            Excel::store(new InvoiceItemExport($invoiceItemsExports), 'public/xlsx/'.$fileName);

            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);

    }
    public function cashFlowExport(Request $request,$company_id){
        $purchaseTables = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($purchaseTables); 

        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTable);

        $supplierTables = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($supplierTables); 

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $table = 'company_'.$request->company_id.'_deposits';
        Deposit::setGlobalTable($table);

        $invoiceReceiptTable = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($invoiceReceiptTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $table = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_payment_options';
        PaymentOption::setGlobalTable($table);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);
        
            $invoiceDatas = InvoiceTable::with('payment_options')->get();
            $purchaseDatas = PurchaseTable::with('payment_options')->get();
            // dd($purchaseDatas);

            $referenceType = Reference::where('type', $request->type)->pluck('prefix')->toArray();
            $fileName = 'CASHFLOWREPORT-'.time().$company_id.'.xlsx';
            $arr = [];
            $data = [];

            foreach($invoiceDatas as $invoiceData){
                $arr['date'] = $invoiceData->date;
                $arr['type'] = $invoiceData->reference_type;
                $arr['reference'] = $invoiceData->reference.''.$invoiceData->reference_number;
                $arr['client'] = $invoiceData->client_name;
                $arr['employee'] = \Auth::user()->name;
                $arr['payment_option'] = $invoiceData->payment_options->name;
                $arr['amount'] = $invoiceData->amount_paid;
                // $arr['amount'] = InvoiceReceipt::whereHas('invoice', function($q) use ($request,$referenceType){
                //     $q->where('type', $referenceType);
                // })->where('paid','1')->sum('amount');
                // $arr['paid'] = InvoiceReceipt::whereHas('invoice', function($q) use ($request,$referenceType){
                //     $q->where('type', $referenceType);
                // })->where('paid','0')->sum('amount');

                $cashFlowExports[] = $arr;
            }
            foreach($purchaseDatas as $purchaseData){
                $arr['date'] = $purchaseData->date;
                $arr['type'] = $purchaseData->reference_type;
                $arr['reference'] = $purchaseData->reference.''.$purchaseData->reference_number;
                $arr['client'] = $purchaseData->client_name;
                $arr['employee'] = \Auth::user()->name;
                // $arr['payment_option'] = $purchaseData->payment_options->name;
                $arr['amount'] = $purchaseData->amount_paid;
                // $arr['amount'] = PurchaseReceipt::whereHas('invoice', function($q) use ($request,$referenceType){
                //     $q->where('type', $referenceType);
                // })->where('paid','1')->sum('amount');
                // $arr['paid'] = PurchaseReceipt::whereHas('invoice', function($q) use ($request,$referenceType){
                //     $q->where('type', $referenceType);
                // })->where('paid','0')->sum('amount');

                $cashFlowExports[] = $arr;
            }
            
            Excel::store(new CashFlowExport($cashFlowExports), 'public/xlsx/'.$fileName);

            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);


    }
}
