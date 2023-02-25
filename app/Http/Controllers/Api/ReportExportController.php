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
use Illuminate\Http\Request;
use App\Exports\ReportExport\SalesClientExport;
use App\Exports\ReportExport\SalesAgentsExport;
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
}
