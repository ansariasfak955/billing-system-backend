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
use App\Models\ExpenseAndInvestment;
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
use App\Exports\ReportExport\PaymentOptionExport;
use App\Exports\ReportExport\IncidentsByClientExport;
use App\Exports\ReportExport\IncidentsByAgentExport;
use App\Exports\ReportExport\IncidentByClientExport;
use App\Exports\ReportExport\IncidentByAgentExport;
use App\Exports\ReportExport\IncidentByItemExport;
use App\Exports\ReportExport\PurchaseSupplierExport;
use App\Exports\ReportExport\PurchaseItemExport;
use App\Exports\ReportExport\CashFlowByAgentExport;
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
                })->get()->sum('amount_with_out_vat');
                

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
                })->get()->sum('amount_with_out_vat');

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

        $table = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($table);

        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);

        $fileName = 'INVOICECLIENTREPORT-'.time().$company_id.'.xlsx';
            $clients = Client::get();

            $referenceType = Reference::where('type', $request->type)->pluck('prefix')->toArray();

            $arr = [];
            $data = [];

            foreach($clients as $client){
                $arr['name'] = $client->legal_name.'('.$client->name.')';
                $arr['reference'] = $client->reference.''.$client->reference_number;
                $arr['ruc'] = $client->tin;
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

        $table = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($table);

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

        $fileName = 'INVOICEITEMSREPORT-'.time().$company_id.'.xlsx';
        $products = Product::get();
        $services = Service::get();
        // dd($products);
            $referenceType = Reference::where('type', $request->type)->pluck('prefix')->toArray();

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
    public function paymentOptionExport(Request $request, $company_id){
        $purchaseTables = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($purchaseTables); 

        $table = 'company_'.$request->company_id.'_payment_options';
        PaymentOption::setGlobalTable($table);

        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTable);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);

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

        $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
        $paymentOptions = PaymentOption::get();
        $fileName = 'PAYMENTOPTIONCASHFLOWREPORT-'.time().$company_id.'.xlsx';
       
        $arr = [];
        $data = [];

        foreach($paymentOptions as $paymentOption){
            $arr['name'] = $paymentOption->name;
            $arr['deposit'] = '';
            $arr['withdrawals'] = InvoiceReceipt::WhereHas('payment_options', function ($query) use ($paymentOption,$referenceType) {
                $query->where('payment_option', $paymentOption->id)->where('type', $referenceType);
            })->where('paid','1')->sum('amount');
            $arr['balance'] = '';
            

            $paymentOptionExports[] = $arr;
        }
        Excel::store(new PaymentOptionExport($paymentOptionExports), 'public/xlsx/'.$fileName);

        return response()->json([
            'status' => true,
            'url' => url('/storage/xlsx/'.$fileName),
         ]);

    }
    public function incidentsByClientExport(Request $request, $company_id){
        $salesTables = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($salesTables);

        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables);

        $table = 'company_'.$request->company_id.'_technical_incidents';
        TechnicalIncident::setGlobalTable($table);

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
        
            $clients = Client::get();
            $fileName = 'CLIENTINCIDENTSTECHNICALSERVICEREPORT-'.time().$company_id.'.xlsx';
            $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $arr = [];
            $data = [];

            foreach($clients as $client){
                $arr['reference'] = $client->reference.''.$client->reference_number;
                $arr['ruc'] = $client->tin;
                $arr['name'] = $client->legal_name.' ('.$client->name.')';
                $arr['pending'] = TechnicalIncident::where('client_id',$client->id)->where('reference', $referenceType)->where('status','pending')->count();
                $arr['refused'] = TechnicalIncident::where('client_id',$client->id)->where('reference', $referenceType)->where('status','refused')->count();
                $arr['accepted'] = TechnicalIncident::where('client_id',$client->id)->where('reference', $referenceType)->where('status','accepted')->count();
                $arr['closed'] = TechnicalIncident::where('client_id',$client->id)->where('reference', $referenceType)->where('status','closed')->count();
                $arr['total'] = TechnicalIncident::where('client_id',$client->id)->where('reference', $referenceType)->count();

                $incidentByClientExports[] = $arr;
            }
            
            Excel::store(new IncidentsByClientExport($incidentByClientExports), 'public/xlsx/'.$fileName);

            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
    }
    public function incidentsByAgentExport(Request $request, $company_id){
        $salesTables = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($salesTables);

        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables);

        $table = 'company_'.$request->company_id.'_technical_incidents';
        TechnicalIncident::setGlobalTable($table);

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
        
            $clients = Client::get();

            $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $fileName = 'EMPLOYEEINCIDENTSTECHNICALSERVICEREPORT-'.time().$company_id.'.xlsx';
                $arr = [];
                $data = [];

                $arr['name'] = \Auth::user()->name;
                $arr['pending'] = TechnicalIncident::where('reference', $referenceType)->where('assigned_to',\Auth::id())->where('status','pending')->count();
                $arr['refused'] = TechnicalIncident::where('reference', $referenceType)->where('assigned_to',\Auth::id())->where('status','refused')->count();
                $arr['accepted'] = TechnicalIncident::where('reference', $referenceType)->where('assigned_to',\Auth::id())->where('status','accepted')->count();
                $arr['closed'] = TechnicalIncident::where('reference', $referenceType)->where('assigned_to',\Auth::id())->where('status','closed')->count();
                $arr['total'] = TechnicalIncident::where('reference', $referenceType)->where('assigned_to',\Auth::id())->count();
                $arr['amount'] = TechnicalIncident::where('reference', $referenceType)->where('assigned_to',\Auth::id())->get()->sum('amount');

                $incidentsAgentsExports[] = $arr;
            
                Excel::store(new IncidentsByAgentExport($incidentsAgentsExports), 'public/xlsx/'.$fileName);

                return response()->json([
                    'status' => true,
                    'url' => url('/storage/xlsx/'.$fileName),
                 ]);
    }
    public function incidentByClient(Request $request, $company_id){
        $salesTables = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($salesTables);

        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables);

        $table = 'company_'.$request->company_id.'_technical_incidents';
        TechnicalIncident::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $table = 'company_'.$request->company_id.'_technical_tables';
        TechnicalTable::setGlobalTable($table);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);
            $fileName = 'CLIENTTECHNICALSERVICEREPORT-'.time().$company_id.'.xlsx';
                    
            $clients = Client::get();

            $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $arr = [];
            $data = [];

            foreach($clients as $client){
                $arr['reference'] = $client->reference.''.$client->reference_number;
                $arr['ruc'] = $client->tin;
                $arr['name'] = $client->legal_name.' ('.$client->name.')';
                $arr['pending'] = TechnicalTable::where('reference', $referenceType)->where('client_id',$client->id)->where('status','pending')->count();
                $arr['refused'] = TechnicalTable::where('reference', $referenceType)->where('client_id',$client->id)->where('status','refused')->count();
                $arr['accepted'] = TechnicalTable::where('reference', $referenceType)->where('client_id',$client->id)->where('status','accepted')->count();
                $arr['closed'] = TechnicalTable::where('reference', $referenceType)->where('client_id',$client->id)->where('status','closed')->count();
                $arr['total'] = TechnicalTable::where('reference', $referenceType)->where('client_id',$client->id)->count();
                $arr['amount'] = TechnicalTable::where('reference', $referenceType)->where('client_id',$client->id)->get()->sum('amount');

                $incidentByClientExports[] = $arr;
            }
            
            Excel::store(new IncidentByClientExport($incidentByClientExports), 'public/xlsx/'.$fileName);

            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
            
    }
    public function incidentByAgent(Request $request, $company_id){
        $salesTables = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($salesTables);

        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables);

        $table = 'company_'.$request->company_id.'_technical_incidents';
        TechnicalIncident::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $table = 'company_'.$request->company_id.'_technical_tables';
        TechnicalTable::setGlobalTable($table);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);
        $fileName = 'EMPLOYEETECHNICALSERVICEREPORT-'.time().$company_id.'.xlsx';
            $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $arr = [];
            $data = [];

            $arr['name'] = \Auth::user()->name;
            $arr['pending'] = TechnicalTable::where('reference', $referenceType)->where('agent_id',\Auth::id())->where('status','pending')->count();
            $arr['refused'] = TechnicalTable::where('reference', $referenceType)->where('agent_id',\Auth::id())->where('status','refused')->count();
            $arr['accepted'] = TechnicalTable::where('reference', $referenceType)->where('agent_id',\Auth::id())->where('status','accepted')->count();
            $arr['closed'] = TechnicalTable::where('reference', $referenceType)->where('agent_id',\Auth::id())->where('status','closed')->count();
            $arr['total'] = TechnicalTable::where('reference', $referenceType)->where('agent_id',\Auth::id())->count();
            $arr['amount'] = TechnicalTable::where('reference', $referenceType)->where('agent_id',\Auth::id())->get()->sum('amount');

                $incidentByAgentExports[] = $arr;
            
                Excel::store(new IncidentByAgentExport($incidentByAgentExports), 'public/xlsx/'.$fileName);

                return response()->json([
                    'status' => true,
                    'url' => url('/storage/xlsx/'.$fileName),
                 ]);
            
    }
    public function incidentByItem(Request $request, $company_id){
        $salesTables = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($salesTables);

        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables);

        $table = 'company_'.$request->company_id.'_technical_incidents';
        TechnicalIncident::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $table = 'company_'.$request->company_id.'_technical_tables';
        TechnicalTable::setGlobalTable($table);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);

        $fileName = 'CATALOGTECHNICALSERVICEREPORT-'.time().$company_id.'.xlsx';
        
            $products = Product::get();
            $services = Service::get();
            // dd($products);
                $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
               
                $arr = [];
                $data = [];
    
                foreach($products as $product){
                    $arr['name'] = $product->name;
                    $arr['reference'] = $product->reference.''.$product->reference_number;
                    $arr['pending'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                        $query->where('reference_id', $product->id)->where('type', $referenceType);
                    })->where('status','pending')->count();
                    $arr['accepted'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                        $query->where('reference_id', $product->id)->where('type', $referenceType);
                    })->where('status','accepted')->count();
                    $arr['closed'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                        $query->where('reference_id', $product->id)->where('type', $referenceType);
                    })->where('status','closed')->count();
                    $arr['refused'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                        $query->where('reference_id', $product->id)->where('type', $referenceType);
                    })->where('status','refused')->count();
                    $arr['total'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                        $query->where('reference_id', $product->id)->where('type', $referenceType);
                    })->count();
                    $arr['units'] = '';
                    $arr['amount'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                        $query->where('reference_id', $product->id)->where('type', $referenceType);
                    })->get()->sum('amount_with_out_vat');
                    
    
                    $incidentByItemExports[] = $arr;
                }
                foreach($services as $service){
                    $arr['name'] = $service->name;
                    $arr['reference'] = $service->reference.''.$service->reference_number;
                    $arr['pending'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                        $query->where('reference_id', $service->id)->where('type', $referenceType);
                    })->where('status','pending')->count();
                    $arr['accepted'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                        $query->where('reference_id', $service->id)->where('type', $referenceType);
                    })->where('status','accepted')->count();
                    $arr['closed'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                        $query->where('reference_id', $service->id)->where('type', $referenceType);
                    })->where('status','closed')->count();
                    $arr['refused'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                        $query->where('reference_id', $service->id)->where('type', $referenceType);
                    })->where('status','refused')->count();
                    $arr['total'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                        $query->where('reference_id', $service->id)->where('type', $referenceType);
                    })->count();
                    $arr['units'] = '';
                    $arr['amount'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                        $query->where('reference_id', $service->id)->where('type', $referenceType);
                    })->get()->sum('amount_with_out_vat');
    
                    $incidentByItemExports[] = $arr;
                }
    
                Excel::store(new IncidentByItemExport($incidentByItemExports), 'public/xlsx/'.$fileName);

                return response()->json([
                    'status' => true,
                    'url' => url('/storage/xlsx/'.$fileName),
                 ]);
            
    }
    public function purchaseSupplierHistoryExport(Request $request, $company_id){

        $table = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($table);

        $purchaseTables = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($purchaseTables); 

        $supplierTables = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($supplierTables); 
        
        $table = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($table);

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
        $fileName = 'SUPPLIERPURCHASEREPORT-'.time().$company_id.'.xlsx';
            $suppliers = Supplier::get();

            $referenceType = Reference::where('type', $request->type)->pluck('prefix')->toArray();
            $arr = [];
            $data = [];

            foreach($suppliers as $supplier){
                $arr['name'] = $supplier->legal_name;
                $arr['invoiced'] = PurchaseReceipt::whereHas('invoice', function($q) use ($supplier,$referenceType){
                    $q->where('supplier_id', $supplier->id)->where('reference', $referenceType);
                })->where('paid','0')->sum('amount');
                $arr['paid'] = PurchaseReceipt::whereHas('invoice', function($q) use ($supplier,$referenceType){
                    $q->where('supplier_id', $supplier->id)->where('reference', $referenceType);
                })->where('paid','1')->sum('amount');
                $arr['unpaid'] = PurchaseReceipt::whereHas('invoice', function($q) use ($supplier,$referenceType){
                    $q->where('supplier_id', $supplier->id)->where('reference', $referenceType);
                })->where('paid','0')->sum('amount');

                $purchaseSupplierExports[] = $arr;
            }
            
            Excel::store(new PurchaseSupplierExport($purchaseSupplierExports), 'public/xlsx/'.$fileName);

            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
    }
    public function purchaseItemHistoryExport(Request $request, $company_id){

        $table = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($table);

        $purchaseTables = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($purchaseTables); 

        $supplierTables = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($supplierTables); 
        
        $table = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $table = 'company_'.$request->company_id.'_expense_and_investments';
        ExpenseAndInvestment::setGlobalTable($table);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);
        
        $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
        $fileName = 'CATALOGPURCHASEREPORT-'.time().$company_id.'.xlsx';
        $products = Product::get();
        $expenses = ExpenseAndInvestment::get();
        $services = Service::get();
           
            $arr = [];
            $data = [];

            foreach($products as $product){
                $arr['name'] = $product->name;
                $arr['reference'] = $product->reference.''.$product->reference_number;
                $arr['units'] = '';
                $arr['amount'] = PurchaseTable::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->where('type', $referenceType);
                })->get()->sum('amount_with_out_vat');
                

                $purchaseItemExports[] = $arr;
            }
            foreach($services as $service){
                $arr['name'] = $service->name;
                $arr['reference'] = $service->reference.''.$service->reference_number;
                $arr['units'] = '';
                $arr['amount'] = PurchaseTable::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                    $query->where('reference_id', $service->id)->where('type', $referenceType);
                })->get()->sum('amount_with_out_vat');
                

                $purchaseItemExports[] = $arr;
            }
            foreach($expenses as $expense){
                $arr['name'] = $expense->name;
                $arr['reference'] = $expense->reference.''.$expense->reference_number;
                $arr['units'] = '';
                $arr['amount'] = PurchaseTable::with(['items'])->WhereHas('items', function ($query) use ($expense,$referenceType) {
                    $query->where('reference_id', $expense->id)->where('type', $referenceType);
                })->get()->sum('amount_with_out_vat');
                

                $purchaseItemExports[] = $arr;
            }
            Excel::store(new PurchaseItemExport($purchaseItemExports), 'public/xlsx/'.$fileName);

            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
    }
    public function cashFlowAgentHistory(Request $request, $company_id){
        $purchaseTables = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($purchaseTables); 

        $table = 'company_'.$request->company_id.'_payment_options';
        PaymentOption::setGlobalTable($table);

        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTable);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);

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

        $fileName = 'EMPLOYEECASHFLOWREPORT-'.time().$company_id.'.xlsx';
        $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
        $paymentOptions = PaymentOption::get();
       
        $arr = [];
        $data = [];

        // foreach($paymentOptions as $paymentOption){
            $arr['name'] = \Auth::user()->name;
            $arr['deposit'] = Deposit::where('type','deposit')->sum('amount');
            // $arr['withdrawals'] = InvoiceTable::WhereHas('payment_options', function ($query) use ($paymentOption) {
            //     $query->where('payment_option', $paymentOption->id);
            //     })->get()->sum('amount');
            $arr['withdrawals'] = InvoiceTable::get()->sum('amount');
            $arr['balance'] = Deposit::where('type','withdraw')->sum('amount');
            

            $cashflowAgentExports[] = $arr;
        // }
        Excel::store(new CashFlowByAgentExport($cashflowAgentExports), 'public/xlsx/'.$fileName);

        return response()->json([
            'status' => true,
            'url' => url('/storage/xlsx/'.$fileName),
         ]);

    }
}
