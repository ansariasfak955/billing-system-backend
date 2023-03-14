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
use App\Models\SupplierSpecialPrice;
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
use App\Exports\ReportExport\StockValuationExport;
use App\Exports\ReportExport\OfProfitExport;
use App\Exports\ReportExport\InvoiceByClientEvoluationExport;
use App\Exports\ReportExport\InvoiceByAgentEvoluationExport;
use App\Exports\ReportExport\InvoiceByItemEvoluationExport;
use App\Exports\ReportExport\PurchaseByProviderExport;
use App\Exports\ReportExport\PurchasesByItemExport;
use App\Exports\ReportExport\TaxSummaryExport;
use App\Exports\ReportExport\OverViewExport;
use App\Exports\ReportExport\SalesOverViewExport;
use App\Exports\ReportExport\TechnicalOverViewExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Validator;

class ReportExportController extends Controller
{
    public function overviewExport(Request $request,$company_id){
        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables); 

        $purchaseTables = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($purchaseTables); 

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $invoice_table = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoice_table);

        $invoiceReceiptTable = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($invoiceReceiptTable);

        $pruchaseReceiptTable = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($pruchaseReceiptTable);

        if($request->type == 'overview'){
            $fileName = 'OVERVIEWREPORT-'.time().$company_id.'.xlsx';

                $arr = [];
                $overViewExports = [];

                $arr['Sales'] = Item::where('type', 'inv')->sum('subtotal');
                $arr['Expenses'] = InvoiceReceipt::filter($request->all())->where('type', 'inv')->where('paid', '1')->sum('amount');
                $arr['Profit'] = Item::where('type', 'inv')->sum('subtotal') - InvoiceReceipt::filter($request->all())->where('type', 'inv')->where('paid', '1')->sum('amount');
                $arr['Invoiced'] = InvoiceReceipt::filter($request->all())->where('type', 'inv')->whereDate('expiration_date', '>', date('Y-m-d'))->sum('amount');
                $arr['Paid'] = InvoiceReceipt::filter($request->all())->where('type', 'inv')->where('paid', '1')->sum('amount');
                $arr['Unpaid'] = InvoiceReceipt::filter($request->all())->where('type', 'inv')->where('paid', '0')->sum('amount');
                $arr['PInvoiced'] = PurchaseReceipt::filter($request->all())->where('type', 'pinv')->whereDate('expiration_date', '>', date('Y-m-d'))->sum('amount');
                $arr['PPaid'] = PurchaseReceipt::filter($request->all())->where('type', 'pinv')->where('paid', '1')->sum('amount');
                $arr['PUnpaid'] = PurchaseReceipt::filter($request->all())->where('type', 'pinv')->where('paid', '0')->sum('amount');

                $overViewExports[] = $arr;

            Excel::store(new OverViewExport($overViewExports), 'public/xlsx/'.$fileName);

            
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);

        }
    }
    public function salesOverviewExport(Request $request,$company_id){
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

        if($request->type =='overview'){
            $fileName = 'SALESOVERVIEWREPORT-'.time().$company_id.'.xlsx';

            $arr = [];
            $salesOverViewExports = [];

            $arr['status'] = '';
            $arr['quantity'] = '';
            $arr['amount'] = '';

            $salesOverViewExports[] = $arr;

        Excel::store(new SalesOverViewExport($salesOverViewExports), 'public/xlsx/'.$fileName);

        
        return response()->json([
            'status' => true,
            'url' => url('/storage/xlsx/'.$fileName),
         ]);

        }
    }
    public function technicalServiceOverview(Request $request,$company_id){
        $technicalTables = 'company_'.$request->company_id.'_technical_tables';
        TechnicalTable::setGlobalTable($technicalTables); 

        $technicalIncident = 'company_'.$request->company_id.'_technical_incidents';
        TechnicalIncident::setGlobalTable($technicalIncident);

        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables); 

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);
        if($request->type =='overview'){
            $fileName = 'TECHNICALSERVICEOVERVIEWREPORT-'.time().$company_id.'.xlsx';

            $arr = [];
            $technicalOverviewExports = [];

            $arr['status'] = '';
            $arr['quantity'] = '';
            $arr['amount'] = '';

            $technicalOverviewExports[] = $arr;

        Excel::store(new TechnicalOverViewExport($technicalOverviewExports), 'public/xlsx/'.$fileName);

        
        return response()->json([
            'status' => true,
            'url' => url('/storage/xlsx/'.$fileName),
         ]);

        }
    }
    public function clientSalesExport(Request $request, $company_id){

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

            $client_ids = SalesEstimate::with('client')->pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$client_ids)->get();

            $referenceType = Reference::where('type', $request->type)->pluck('prefix')->toArray();
            $arr = [];
            $data = [];

            foreach($clients as $client){
                $arr['reference'] = $client->reference.''.$client->reference_number;
                $arr['name'] = $client->legal_name;
                $arr['tin'] = $client->tin;
                $arr['category'] = $client->client_category_name;
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

        $itemProductIds = Item::whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
        $itemServiceIds = Item::whereIn('reference',['SER'])->pluck('reference_id')->toArray();
        $products = Product::whereIn('id',$itemProductIds)->get();
        $services = Service::whereIn('id',$itemServiceIds)->get();
            $referenceType = Reference::where('type', $request->type)->pluck('prefix')->toArray();

            $fileName = 'ITEMSSALESREPORT-'.time().$company_id.'.xlsx';

            $arr = [];
            $data = [];

            foreach($products as $product){
                $arr['name'] = $product->name;
                $arr['reference'] = $product->reference.''.$product->reference_number;
                $arr['category'] = $product->product_category_name;
                $arr['pending'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO'])->where('type', $referenceType);
                })->where('status','pending')->count();
                $arr['accepted'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO'])->where('type', $referenceType);
                })->where('status','accepted')->count();
                $arr['closed'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO'])->where('type', $referenceType);
                })->where('status','closed')->count();
                $arr['refused'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO'])->where('type', $referenceType);
                })->where('status','refused')->count();
                $arr['total'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO'])->where('type', $referenceType);
                })->count();
                $arr['amount'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO'])->where('type', $referenceType);
                })->get()->sum('amount_with_out_vat');
                $arr['units'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO'])->where('type', $referenceType);
                })->count();
                

                $itemsSalesExports[] = $arr;
            }
            foreach($services as $service){
                $arr['name'] = $service->name;
                $arr['reference'] = $service->reference.''.$service->reference_number;
                $arr['category'] = $service->product_category_name;
                $arr['pending'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER'])->where('type', $referenceType);
                })->where('status','pending')->count();
                $arr['accepted'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER'])->where('type', $referenceType);
                })->where('status','accepted')->count();
                $arr['closed'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER'])->where('type', $referenceType);
                })->where('status','closed')->count();
                $arr['refused'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER'])->where('type', $referenceType);
                })->where('status','refused')->count();
                $arr['total'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER'])->where('type', $referenceType);
                })->count();
                $arr['amount'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER'])->where('type', $referenceType);
                })->get()->sum('amount_with_out_vat');
                $arr['units'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['PRO'])->where('type', $referenceType);
                })->count();

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

        $client_ids = InvoiceTable::pluck('client_id')->toArray();
        $clients = Client::whereIn('id',$client_ids)->get();

            $referenceType = Reference::where('type', $request->type)->pluck('prefix')->toArray();

            $arr = [];
            $data = [];

            foreach($clients as $client){
                $arr['name'] = $client->legal_name.'('.$client->name.')';
                $arr['reference'] = $client->reference.''.$client->reference_number;
                $arr['ruc'] = $client->tin;
                $arr['category'] = $client->client_category_name;
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
        $referenceType = Reference::whereIn('type', ['Normal Invoice', 'Refund Invoice'])->pluck('prefix')->toArray();
        $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
        $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
        $products = Product::whereIn('id',$itemProductIds)->get();
        $services = Service::whereIn('id',$itemServiceIds)->get();
        // dd($products);
            $referenceType = Reference::where('type', $request->type)->pluck('prefix')->toArray();

            $arr = [];
            $data = [];

            foreach($products as $product){
                $arr['name'] = $product->name;
                $arr['reference'] = $product->reference.''.$product->reference_number;
                $arr['category'] = $product->product_category_name;
                $arr['units'] = InvoiceTable::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO'])->where('type', $referenceType);
                })->count();
                $arr['amount'] = InvoiceTable::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO'])->where('type', $referenceType);
                })->get()->sum('amount_with_out_vat');
                

                $invoiceItemsExports[] = $arr;
            }
            foreach($services as $service){
                $arr['name'] = $service->name;
                $arr['reference'] = $service->reference.''.$service->reference_number;
                $arr['category'] = $product->product_category_name;
                $arr['units'] = InvoiceTable::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO'])->where('type', $referenceType);
                })->count();
                $arr['amount'] = InvoiceTable::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER'])->where('type', $referenceType);
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
        if($request->type =='cash_Flow'){

            $fileName = 'CASHFLOWREPORT-'.time().$company_id.'.xlsx';
            $paymentOption = InvoiceTable::get();
            $purchasePaymentOptions = PurchaseTable::where('reference','PINV')->get();

            $arr = [];
            $cashFlowExports = [];

            foreach($paymentOption as $invoiceData){
                $arr['date'] = $invoiceData->date;
                $arr['type'] = $invoiceData->reference_type;
                $arr['reference'] = $invoiceData->reference.''.$invoiceData->reference_number;
                $arr['client'] = $invoiceData->client_name;
                $arr['employee'] = \Auth::user()->name;
                $arr['payment_option'] = $invoiceData->payment_option_name;
                $arr['amount'] = $invoiceData->amount;
                $arr['paid'] = $invoiceData->set_as_paid;
                $cashFlowExports[] = $arr;
            }
            foreach($purchasePaymentOptions as $purchaseData){
                $arr['date'] = $purchaseData->date;
                $arr['type'] = $purchaseData->reference_type;
                $arr['reference'] = $purchaseData->reference.''.$purchaseData->reference_number;
                $arr['supplier'] = $purchaseData->supplier_name;
                $arr['employee'] = \Auth::user()->name;
                $arr['amount'] = $purchaseData->amount;
                $arr['payment_option'] = $purchaseData->payment_option_name;
                $arr['paid'] = $purchaseData->set_as_paid;
                $cashFlowExports[] = $arr;
            }
            
            Excel::store(new CashFlowExport($cashFlowExports), 'public/xlsx/'.$fileName);

            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }


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
            $arr['deposit'] = Deposit::WhereHas('payment_options', function ($query) use ($paymentOption) {
                $query->where('payment_option', $paymentOption->id)->where('type','deposit');
                })->get()->sum('amount');
            $arr['withdrawals'] = Deposit::WhereHas('payment_options', function ($query) use ($paymentOption) {
                    $query->where('payment_option', $paymentOption->id)->where('type','withdraw');
                    })->get()->sum('amount');
            $arr['balance'] = Deposit::WhereHas('payment_options', function ($query) use ($paymentOption) {
                $query->where('payment_option', $paymentOption->id)->where('type','deposit');
                })->get()->sum('amount') - Deposit::WhereHas('payment_options', function ($query) use ($paymentOption) {
                $query->where('payment_option', $paymentOption->id)->where('type','withdraw');
                })->get()->sum('amount');
            

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
                    
            $client_ids = TechnicalTable::pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$client_ids)->get();

            $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $arr = [];
            $data = [];

            foreach($clients as $client){
                $arr['reference'] = $client->reference.''.$client->reference_number;
                $arr['ruc'] = $client->tin;
                $arr['name'] = $client->legal_name.' ('.$client->name.')';
                $arr['category'] = $client->client_category_name;
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
        
        $itemProductIds = Item::whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
        $itemServiceIds = Item::whereIn('reference',['SER'])->pluck('reference_id')->toArray();
        $products = Product::whereIn('id',$itemProductIds)->get();
        $services = Service::whereIn('id',$itemServiceIds)->get();
            // dd($products);
                $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
               
                $arr = [];
                $data = [];
    
                foreach($products as $product){
                    $arr['name'] = $product->name;
                    $arr['reference'] = $product->reference.''.$product->reference_number;
                    $arr['category'] = $product->product_category_name;
                    $arr['pending'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                        $query->where('reference_id', $product->id)->whereIn('reference',['PRO'])->where('type', $referenceType);
                    })->where('status','pending')->count();
                    $arr['accepted'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                        $query->where('reference_id', $product->id)->whereIn('reference',['PRO'])->where('type', $referenceType);
                    })->where('status','accepted')->count();
                    $arr['closed'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                        $query->where('reference_id', $product->id)->whereIn('reference',['PRO'])->where('type', $referenceType);
                    })->where('status','closed')->count();
                    $arr['refused'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                        $query->where('reference_id', $product->id)->whereIn('reference',['PRO'])->where('type', $referenceType);
                    })->where('status','refused')->count();
                    $arr['total'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                        $query->where('reference_id', $product->id)->whereIn('reference',['PRO'])->where('type', $referenceType);
                    })->count();
                    $arr['units'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                        $query->where('reference_id', $product->id)->whereIn('reference',['PRO'])->where('type', $referenceType);
                    })->count();
                    $arr['amount'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                        $query->where('reference_id', $product->id)->whereIn('reference',['PRO'])->where('type', $referenceType);
                    })->get()->sum('amount_with_out_vat');
                    
    
                    $incidentByItemExports[] = $arr;
                }
                foreach($services as $service){
                    $arr['name'] = $service->name;
                    $arr['reference'] = $service->reference.''.$service->reference_number;
                    $arr['category'] = $service->product_category_name;
                    $arr['pending'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                        $query->where('reference_id', $service->id)->whereIn('reference',['SER'])->where('type', $referenceType);
                    })->where('status','pending')->count();
                    $arr['accepted'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                        $query->where('reference_id', $service->id)->whereIn('reference',['SER'])->where('type', $referenceType);
                    })->where('status','accepted')->count();
                    $arr['closed'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                        $query->where('reference_id', $service->id)->whereIn('reference',['SER'])->where('type', $referenceType);
                    })->where('status','closed')->count();
                    $arr['refused'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                        $query->where('reference_id', $service->id)->whereIn('reference',['SER'])->where('type', $referenceType);
                    })->where('status','refused')->count();
                    $arr['total'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                        $query->where('reference_id', $service->id)->whereIn('reference',['SER'])->where('type', $referenceType);
                    })->count();
                    $arr['units'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                        $query->where('reference_id', $service->id)->whereIn('reference',['SER'])->where('type', $referenceType);
                    })->count();
                    $arr['amount'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                        $query->where('reference_id', $service->id)->whereIn('reference',['SER'])->where('type', $referenceType);
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
                $arr['reference'] = $supplier->reference.''.$supplier->reference_number;
                $arr['ruc'] = $supplier->tin;
                $arr['name'] = $supplier->legal_name.' ('.$supplier->name.')';
                $arr['according'] = $supplier->reference_type;
                $arr['currency'] = $supplier->currency;
                $arr['category'] = $supplier->supplier_category_name;
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
            $purchaseItemExports = [];

            foreach($products as $product){
                $arr['name'] = $product->name;
                $arr['reference'] = $product->reference.''.$product->reference_number;
                $arr['category'] = $product->product_category_name;
                $arr['units'] = '';
                $arr['amount'] = PurchaseTable::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->where('type', $referenceType);
                })->get()->sum('amount_with_out_vat');
                

                $purchaseItemExports[] = $arr;
            }
            foreach($services as $service){
                $arr['name'] = $service->name;
                $arr['reference'] = $service->reference.''.$service->reference_number;
                $arr['category'] = $service->product_category_name;
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
    public function cashFlowAgentHistoryExport(Request $request, $company_id){
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
        $cashflowAgentExports = [];

            $arr['name'] = \Auth::user()->name;
            $arr['deposit'] = Deposit::where('type','deposit')->where('paid_by','1')->sum('amount');
            $arr['withdrawals'] = Deposit::where('type','withdraw')->where('paid_by','1')->sum('amount');
            $arr['balance'] = Deposit::where('type','deposit')->where('paid_by','1')->sum('amount') - Deposit::where('type','withdraw')->where('paid_by','1')->sum('amount');

            $cashflowAgentExports[] = $arr;

        Excel::store(new CashFlowByAgentExport($cashflowAgentExports), 'public/xlsx/'.$fileName);

        return response()->json([
            'status' => true,
            'url' => url('/storage/xlsx/'.$fileName),
         ]);

    }
    public function stockValuationExport(Request $request, $company_id){
        $purchaseTables = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($purchaseTables); 

        $productTable = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($productTable); 

        $supplierTables = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($supplierTables); 

        $table = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_supplier_special_prices';
        SupplierSpecialPrice::setGlobalTable($table);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        if($request->type == 'stock_Voluation'){
            
            $fileName = 'STOCKVALUATIONREPORT-'.time().$company_id.'.xlsx';
            $productIds = SupplierSpecialPrice::pluck('product_id')->toArray();
            $supplierSpecialPrices = Product::whereIn('id',$productIds)->get();
            
            $arr = [];
            $stockValuationExports = [];

            foreach($supplierSpecialPrices as $supplierSpecialPrice){
                $arr['reference'] = $supplierSpecialPrice->reference.$supplierSpecialPrice->reference_number;
                $arr['name'] =  $supplierSpecialPrice->name;
                $arr['stock'] = PurchaseTable::filter($request->all())->where('reference','PINV')->WhereHas('items', function ($query) use ($supplierSpecialPrice) {
                    $query->where('reference_id', $supplierSpecialPrice->id)->whereIn('reference',['PRO']);
                })->count();
                $arr['category'] =  $supplierSpecialPrice->product_category_name;
                $arr['sales_stock_value'] = Product::get()->sum('price');
                $arr['purchase_stock_value'] = PurchaseTable::filter($request->all())->where('reference','PINV')->WhereHas('items', function ($query) use ($supplierSpecialPrice) {
                    $query->where('reference_id', $supplierSpecialPrice->id)->whereIn('reference',['PRO']);
                })->get()->sum('amount_with_out_vat');

                $stockValuationExports[] = $arr;
            }
            Excel::store(new StockValuationExport($stockValuationExports), 'public/xlsx/'.$fileName);

            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
            ]);
        }
        
    }
    public function ofEvolution(Request $request,$company_id){
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

        if($request->type == 'ofProfit'){

        $fileName = 'PROFITEVOLUTIONREPORT-'.time().$company_id.'.xlsx';
            // $invoiceDatas = InvoiceTable::with('payment_options')->get();
            // $purchaseDatas = PurchaseTable::with('payment_options')->get();
            // dd($purchaseDatas);
            $arr = [];
            $ofProfits = [];

            // foreach($invoiceDatas as $invoiceData){
                $arr['period'] = '2023Q1';
                $arr['sales'] = '200.00';
                $arr['expense'] = '200.00';
                $arr['profit'] = '200.00';

                $ofProfits[] = $arr;
            // }
            Excel::store(new OfProfitExport($ofProfits), 'public/xlsx/'.$fileName);

            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }elseif($request->type == 'invoicing_by_client_list'){
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

            $fileName = 'CLIENTINVOICINGEVOLUTIONREPORT-'.time().$company_id.'.xlsx';
                $invoice_ids = InvoiceTable::with('client')->pluck('client_id')->toArray();
                $clients = Client::whereIn('id', $invoice_ids)->get();

                $arr = [];
                $invoiceByClients = [];
    
                foreach($clients as $client){
                    $arr['reference'] = $client->reference.''.$client->reference_number;
                    $arr['name'] = $client->legal_name.' ('.$client->name.')';
                    $arr['tin'] = $client->tin;
                    $arr['client_category'] = '';
                    $arr['zip_code'] = $client->zip_code;
                    $arr['Q1'] = InvoiceTable::filter($request->all())->where('client_id',$client->id)->get()->sum('amount');
                    $arr['Q2'] = '0.00';
                    $arr['Q3'] = '0.00';
                    $arr['Q4'] = '0.00';
                    $arr['total'] = InvoiceTable::filter($request->all())->where('client_id',$client->id)->get()->sum('amount');
                    $invoiceByClients[] = $arr;
                }
                Excel::store(new InvoiceByClientEvoluationExport($invoiceByClients), 'public/xlsx/'.$fileName);

                return response()->json([
                    'status' => true,
                    'url' => url('/storage/xlsx/'.$fileName),
                 ]);
        }elseif($request->type == 'invoicing_by_agent_list'){
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

            $fileName = 'EMPLOYEEINVOICINGEVOLUTIONREPORT-'.time().$company_id.'.xlsx';

                    $arr = [];
                    $invoiceByagents = [];
    
                    $arr['name'] = \Auth::user()->name;
                    $arr['Q1'] = InvoiceTable::filter($request->all())->where('agent_id',\Auth::id())->get()->sum('amount');
                    $arr['Q2'] = '0.00';
                    $arr['Q3'] = '0.00';
                    $arr['Q4'] = '0.00';
                    $arr['total'] = InvoiceTable::filter($request->all())->where('agent_id',\Auth::id())->get()->sum('amount');
    
                    $invoiceByagents[] = $arr;

                    Excel::store(new InvoiceByAgentEvoluationExport($invoiceByagents), 'public/xlsx/'.$fileName);

                    return response()->json([
                        'status' => true,
                        'url' => url('/storage/xlsx/'.$fileName),
                     ]);
        }elseif($request->type == 'invoicing_by_item_list'){
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
            
            $fileName = 'CATALOGINVOICINGEVOLUTIONREPORT-'.time().$company_id.'.xlsx';

            $referenceType = Reference::whereIn('type', ['Normal Invoice', 'Refund Invoice'])->pluck('prefix')->toArray();
            $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
            $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
            $products = Product::whereIn('id',$itemProductIds)->get();
            $services = Service::whereIn('id',$itemServiceIds)->get();
            // dd($products);
               
                $arr = [];
                $invoiceByItems = [];
    
                foreach($products as $product){
                    $arr['name'] = $product->name;
                    $arr['reference'] = $product->reference.''.$product->reference_number;
                    $arr['product_category'] = '';
                    $arr['Q1'] = InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                        $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                    })->get()->sum('amount_with_out_vat');
                    $arr['Q2'] = '0.00';
                    $arr['Q3'] = '0.00';
                    $arr['Q4'] = '0.00';
                    $arr['total'] = InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                        $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                    })->get()->sum('amount_with_out_vat');
                    
    
                    $invoiceByItems[] = $arr;
                }
                foreach($services as $service){
                    $arr['name'] = $service->name;
                    $arr['reference'] = $service->reference.''.$service->reference_number;
                    $arr['product_category'] = '';
                    $arr['Q1'] = InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                        $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                    })->get()->sum('amount_with_out_vat');
                    $arr['Q2'] = '0.00';
                    $arr['Q3'] = '0.00';
                    $arr['Q4'] = '0.00';
                    $arr['total'] = InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                        $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                    })->get()->sum('amount_with_out_vat');
    
                    $invoiceByItems[] = $arr;
                }
                Excel::store(new InvoiceByItemEvoluationExport($invoiceByItems), 'public/xlsx/'.$fileName);

                return response()->json([
                    'status' => true,
                    'url' => url('/storage/xlsx/'.$fileName),
                 ]);
        }elseif($request->type == 'purchase_by_provider_list'){
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

            $fileName = 'SUPPLIERPURCHASEEVOLUTIONREPORT-'.time().$company_id.'.xlsx';

                $referenceType = Reference::whereIn('type', ['Purchase Invoice'])->pluck('prefix')->toArray();
                $supplier_ids = PurchaseTable::with('supplier')->whereIn('reference',$referenceType)->pluck('supplier_id')->toArray();
                $suppliers = Supplier::whereIn('id',$supplier_ids)->get();

                $arr = [];
                $purchaseByProviders = [];
    
                foreach($suppliers as $supplier){
                    $arr['reference'] = $supplier->reference.''.$supplier->reference_number;
                    $arr['name'] = $supplier->legal_name.' ('.$supplier->name.')';
                    $arr['tin'] = $supplier->tin;
                    $arr['supplier_category'] = '';
                    $arr['zip_code'] = $supplier->zip_code;
                    $arr['Q1'] = PurchaseTable::filter($request->all())->where('supplier_id',$supplier->id)->get()->sum('amount');
                    $arr['Q2'] = '0.00';
                    $arr['Q3'] = '0.00';
                    $arr['Q4'] = '0.00';
                    $arr['total'] = PurchaseTable::filter($request->all())->where('supplier_id',$supplier->id)->get()->sum('amount');
                    
                    $purchaseByProviders[] = $arr;
                }
                Excel::store(new PurchaseByProviderExport($purchaseByProviders), 'public/xlsx/'.$fileName);

                return response()->json([
                    'status' => true,
                    'url' => url('/storage/xlsx/'.$fileName),
                 ]);
        }elseif($request->type == 'purchase_by_item_list'){
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
            
            $fileName = 'CATALOGPURCHASEEVOLUTIONREPORT-'.time().$company_id.'.xlsx';

            $referenceType = Reference::whereIn('type', ['Purchase Invoice'])->pluck('prefix')->toArray();
            $itemProductIds = Item::with('supplier')->whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
            $itemServiceIds = Item::with('supplier')->whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
            $expenseInvestmentIds = Item::with('supplier')->whereIn('type',$referenceType)->whereIn('reference',['EAI'])->pluck('reference_id')->toArray();
            $products = Product::whereIn('id',$itemProductIds)->get();
            $services = Service::whereIn('id',$itemServiceIds)->get();
            $expenses = ExpenseAndInvestment::whereIn('id',$expenseInvestmentIds)->get();
               
                $arr = [];
                $purchaseByItems = [];
    
                foreach($products as $product){
                    $arr['name'] = $product->name;
                    $arr['reference'] = $product->reference.''.$product->reference_number;
                    $arr['product_category'] = '';
                    $arr['Q1'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                        $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                    })->get()->sum('amount_with_out_vat');
                    $arr['Q2'] = '0.00';
                    $arr['Q3'] = '0.00';
                    $arr['Q4'] = '0.00';
                    $arr['total'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                        $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                    })->get()->sum('amount_with_out_vat');
                    
    
                    $purchaseByItems[] = $arr;
                }
                foreach($services as $service){
                    $arr['name'] = $service->name;
                    $arr['reference'] = $service->reference.''.$service->reference_number;
                    $arr['product_category'] = '';
                    $arr['Q1'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                        $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                    })->get()->sum('amount_with_out_vat');
                    $arr['Q2'] = '0.00';
                    $arr['Q3'] = '0.00';
                    $arr['Q4'] = '0.00';
                    $arr['total'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                        $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                    })->get()->sum('amount_with_out_vat');
    
                    $purchaseByItems[] = $arr;
                }
                foreach($expenses as $expense){
                    $arr['name'] = $expense->name;
                    $arr['reference'] = $expense->reference.''.$expense->reference_number;
                    $arr['product_category'] = '';
                    $arr['Q1'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($expense) {
                        $query->where('reference_id', $expense->id)->whereIn('reference',['EAI']);
                    })->get()->sum('amount_with_out_vat');
                    $arr['Q2'] = '0.00';
                    $arr['Q3'] = '0.00';
                    $arr['Q4'] = '0.00';
                    $arr['total'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($expense) {
                        $query->where('reference_id', $expense->id)->whereIn('reference',['EAI']);
                    })->get()->sum('amount_with_out_vat');
                    
    
                    $purchaseByItems[] = $arr;
                }
                Excel::store(new PurchasesByItemExport($purchaseByItems), 'public/xlsx/'.$fileName);

                return response()->json([
                    'status' => true,
                    'url' => url('/storage/xlsx/'.$fileName),
                 ]);
        }
    }
    public function taxSummary(Request $request,$company_id){
        if($request->type == 'tax'){

            $clientsTables = 'company_'.$request->company_id.'_clients';
            Client::setGlobalTable($clientsTables);
    
            $table = 'company_'.$request->company_id.'_services';
            Service::setGlobalTable($table);
    
            $table = 'company_'.$request->company_id.'_products';
            Product::setGlobalTable($table);

            $purchaseTables = 'company_'.$request->company_id.'_purchase_tables';
            PurchaseTable::setGlobalTable($purchaseTables);     
    
            $itemTable = 'company_'.$request->company_id.'_items';
            Item::setGlobalTable($itemTable);
    
            $table = 'company_'.$request->company_id.'_invoice_receipts';
            InvoiceReceipt::setGlobalTable($table);
    
            $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
            InvoiceTable::setGlobalTable($invoiceTable);
    
            $item_meta_table = 'company_'.$request->company_id.'_item_metas';
            ItemMeta::setGlobalTable($item_meta_table);

            $taxes = 'company_'.$request->company_id.'_consumption_taxes';
            ConsumptionTax::setGlobalTable($taxes);
    
            $referenceTable = 'company_'.$request->company_id.'_references';
            Reference::setGlobalTable($referenceTable);

            $fileName = 'TAXREPORT-'.time().$company_id.'.xlsx';

            $referenceType = Reference::whereIn('type', ['Normal Invoice', 'Refund Invoice','Purchase Invoice'])->pluck('prefix')->toArray();
            $itemProductIds = Item::whereIn('type',$referenceType)->groupby('vat')->pluck('vat')->toArray();
            $itemServiceIds = Item::whereIn('type',$referenceType)->groupby('vat')->pluck('vat')->toArray();
            $taxes = ConsumptionTax::whereIn('tax',$itemProductIds)->get();
            // return $taxes;

            $arr = [];
            $data = [];
            foreach($taxes as $tax){

                $arr['vat'] = $tax->primary_name.' '.$tax->tax.' '.'%';
                $arr['Collected'] = 'Collected';
                $arr['Paid'] = 'Paid';
                $arr['Total'] = 'Total';
                $arr['Subtotal'] = 'Subtotal';
                $arr['Tax'] = 'Tax';
                $arr['collected'] = InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($tax) {
                    $query->where('vat', $tax->tax);
                })->get()->sum('amount');
                $arr['ctax'] = InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($tax) {
                    $query->where('vat', $tax->tax);
                })->get()->sum('tax_amount');
                $arr['paid'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($tax) {
                    $query->where('vat', $tax->tax);
                })->get()->sum('amount');
                $arr['ptax'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($tax) {
                    $query->where('vat', $tax->tax);
                })->get()->sum('tax_amount');
                $arr['total'] = InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($tax) {
                    $query->where('vat', $tax->tax);
                })->get()->sum('amount') - PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($tax) {
                    $query->where('vat', $tax->tax);
                })->get()->sum('amount');
                $arr['ttax'] = InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($tax) {
                    $query->where('vat', $tax->tax);
                })->get()->sum('tax_amount') - PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($tax) {
                    $query->where('vat', $tax->tax);
                })->get()->sum('tax_amount');

                $taxes[] = $arr;
            }
            Excel::store(new TaxSummaryExport($taxes), 'public/xlsx/'.$fileName);

            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);

        }

    }
}
