<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesEstimate;
use App\Models\TechnicalTable;
use App\Models\TechnicalIncident;
use App\Models\InvoiceTable;
use App\Models\PurchaseTable;
use App\Models\Client;
use App\Models\Item;
use App\Models\ItemMeta;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $salesEstimates = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($salesEstimates);
        $technicalTables = 'company_'.$request->company_id.'_technical_tables';
        TechnicalTable::setGlobalTable($technicalTables);
        $invoiceTables = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTables);
        $purchaseTables = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($purchaseTables);

        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables); 
        $technicalIncident = 'company_'.$request->company_id.'_technical_incidents';
        TechnicalIncident::setGlobalTable($technicalIncident); 

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);
        $data  = [];
        if($request->type == 'recent'){
            $salesEstimatesData = SalesEstimate::orderBy('created_at', 'DESC')->get()->toArray();
            $InvoiceTableData = InvoiceTable::orderBy('created_at', 'DESC')->get()->toArray();
            $purchaseTablesData = PurchaseTable::orderBy('created_at', 'DESC')->get()->toArray();
            $TechnicalIncidentData = TechnicalTable::orderBy('created_at', 'DESC')->get()->toArray();
            $data = array_merge($salesEstimatesData, $InvoiceTableData, $purchaseTablesData, $TechnicalIncidentData);
            if(count($data)){

                $data =  new \Illuminate\Support\Collection($data);
                $data = $data->sortBy('created_at')->take(20)->values();
            }

        }else{

            $salesArr = [
                "name" => "SALES", 
                "data" => [
                    [
                        "lable" => "Estimates", 
                        "value" => SalesEstimate::where('reference', 'SE')->where('status', 'pending')->count()." ($ ". SalesEstimate::where('reference', 'SE')->where('status', 'pending')->get()->sum('amount').")"
                    ], 
                    [
                        "lable" => "Orders", 
                        "value" => SalesEstimate::where('reference', 'SO')->where('status', 'pending')->count()." ($ ". SalesEstimate::where('reference', 'SO')->where('status', 'pending')->get()->sum('amount').")"
                    ], 
                    [
                        "lable" => "Delivery Notes", 
                        "value" => SalesEstimate::where('reference', 'SDN')->where('status', 'pending')->count()." ($ ". SalesEstimate::where('reference', 'SDN')->where('status', 'pending')->get()->sum('amount').")"
                    ] 
                ] 
            ];
            $technicalServiceArr = [
                "name" => "TECHNICAL SERVICE", 
                "data" => [
                    [
                        "lable" => "My Incidents", 
                        "value" => TechnicalIncident::where('status', 'pending')->count()
                    ], 
                    [
                        "lable" => "Unassigned Incidents", 
                        "value" => TechnicalIncident::where('status', 'pending')->whereNull('assigned_to')->count()
                    ], 
                    [
                        "lable" => "Estimates", 
                        "value" => TechnicalTable::where('reference', 'WE')->where('status', 'pending')->count()." ($ ". TechnicalTable::where('reference', 'WE')->where('status', 'pending')->get()->sum('amount').")" 
                    ],
                    [
                        "lable" => "Orders", 
                        "value" => TechnicalTable::where('reference', 'WO')->where('status', 'pending')->count()." ($ ". TechnicalTable::where('reference', 'WO')->where('status', 'pending')->get()->sum('amount').")" 
                    ],
                    [
                        "lable" => "Delivery Notes", 
                        "value" => TechnicalTable::where('reference', 'WDN')->where('status', 'pending')->count()." ($ ". TechnicalTable::where('reference', 'WDN')->where('status', 'pending')->get()->sum('amount').")"  
                    ]
                ] 
            ];
            $invoiceArr = [
                "name" => "INVOICING", 
                "data" => [
                    [
                        "lable" => "Invoices", 
                        "value" => InvoiceTable::where('reference', 'INV')->where('status', 'unpaid')->count()." ($ ". InvoiceTable::where('reference', 'INV')->where('status', 'unpaid')->get()->sum('amount').")"
                    ], 
                    [
                        "lable" => "Refund Invoices", 
                        "value" =>  InvoiceTable::where('reference', 'RET')->where('status', 'unpaid')->count()." ($ ". InvoiceTable::where('reference', 'RET')->where('status', 'unpaid')->get()->sum('amount').")" 
                    ]
                ] 
            ];
            $purchaseArr = [
                "name" => "PURCHASES", 
                "data" => [
                    [
                        "lable" => "Orders", 
                        "value" =>  PurchaseTable::where('reference', 'PO')->where('status', 'pending')->count()." ($ ". PurchaseTable::where('reference', 'PO')->where('status', 'pending')->get()->sum('amount').")" 
                    ], 
                    [
                        "lable" => "Delivery Notes", 
                        "value" =>  PurchaseTable::where('reference', 'PDN')->where('status', 'pending')->count()." ($ ". PurchaseTable::where('reference', 'PDN')->where('status', 'pending')->get()->sum('amount').")" 
                    ], 
                    [
                        "lable" => "Invoices", 
                        "value" =>  PurchaseTable::where('reference', 'PINV')->where('status', 'pending')->count()." ($ ". PurchaseTable::where('reference', 'PINV')->where('status', 'pending')->get()->sum('amount').")"
                    ] 
                ] 
            ];
    
            $data[] = $salesArr;
            $data[] = $technicalServiceArr;
            $data[] = $invoiceArr;
            $data[] = $purchaseArr;
        }

        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
        
    }
}