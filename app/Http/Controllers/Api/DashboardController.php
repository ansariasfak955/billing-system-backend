<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesEstimate;
use App\Models\TechnicalTable;
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

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);
        $data  = [];

        $salesArr = [
            "name" => "SALES", 
            "data" => [
                [
                    "lable" => "Estimates", 
                    "value" => "4($ 291.01)"
                ], 
                [
                    "lable" => "Orders", 
                    "value" => "7($ 424.73)" 
                ], 
                [
                    "lable" => "Delivery Notes", 
                    "value" => "1($ 363.60)"
                ] 
            ] 
        ];
        $technicalServiceArr = [
            "name" => "TECHNICAL SERVICE", 
            "data" => [
                [
                    "lable" => "My Incidents", 
                    "value" => "7"
                ], 
                [
                    "lable" => "Unassigned Incidents", 
                    "value" => "0" 
                ], 
                [
                    "lable" => "Estimates", 
                    "value" => "2($ 1.424.40)" 
                ],
                [
                    "lable" => "Orders", 
                    "value" => "0($ 0.00)" 
                ],
                [
                    "lable" => "Delivery Notes", 
                    "value" => "2($ 149.90)" 
                ]
            ] 
        ];
        $invoiceArr = [
            "name" => "INVOICING", 
            "data" => [
                [
                    "lable" => "Invoices", 
                    "value" => "6($ 470.22)" 
                ], 
                [
                    "lable" => "Refund Invoices", 
                    "value" => "0($ 0.00)" 
                ]
            ] 
        ];
        $purchaseArr = [
            "name" => "PURCHASES", 
            "data" => [
                [
                    "lable" => "Orders", 
                    "value" => "1($ 846.00)" 
                ], 
                [
                    "lable" => "Delivery Notes", 
                    "value" => "0($ 0.00)" 
                ], 
                [
                    "lable" => "Invoices", 
                    "value" => "1($ 1,650.00)"
                ] 
            ] 
        ];

        $data[] = $salesArr;
        $data[] = $technicalServiceArr;
        $data[] = $invoiceArr;
        $data[] = $purchaseArr;
        
           
        // $data = [];  
        // $arr = [];
        //                             // Sales Dashboard
        // $arr['name'] = "SALES";
        // $arr['label'] = "Estimates";
        // $arr['value'] = SalesEstimate::where('status', 'pending')->where('reference', 'SE')->count()."(".SalesEstimate::where('status', 'pending')->get()->sum('amount').")";
        // $arr['label'] = "Orders";
        // $arr['value'] = SalesEstimate::where('status', 'pending')->where('reference', 'SO')->count()."(".SalesEstimate::where('status', 'pending')->get()->sum('amount').")";
        // $arr['label'] = "Delivery Notes";
        // $arr['value'] = SalesEstimate::where('status', 'pending')->where('reference', 'SDN')->count()."(".SalesEstimate::where('status', 'pending')->get()->sum('amount').")";

        //                             // Technical Service
        // $arr1['name'] = "TECHNICAL SERVICE";
        // $arr1['lebal'] = "My Incidents";
        // $arr1['value'] = TechnicalTable::where('status', 'pending')->where('reference', 'INC')->count()."(".TechnicalTable::where('status', 'pending')->get()->sum('amount').")";
        // $arr1['lebal'] = "Unassigned Incidents";
        // $arr1['value'] = TechnicalTable::where('status', 'pending')->whereNull('assigned_to')->count()."(".TechnicalTable::where('status', 'pending')->get()->sum('amount').")";
        // $arr['lebal'] = "Estimates";
        // $arr1['value'] = TechnicalTable::where('status', 'pending')->where('reference', 'WE')->count()."(".TechnicalTable::where('status', 'pending')->get()->sum('amount').")";
        // $arr1['lebal'] = "Orders";
        // $arr1['value'] = TechnicalTable::where('status', 'pending')->where('reference', 'WO')->count()."(".TechnicalTable::where('status', 'pending')->get()->sum('amount').")";
        // $arr1['lebal'] = "Delivery Notes";
        // $arr1['value'] = TechnicalTable::where('status', 'pending')->where('reference', 'WDN')->count()."(".TechnicalTable::where('status', 'pending')->get()->sum('amount').")";

        //                             // Invoice Dsahboard
        // $arr2['name'] = "INVOICING";
        // $arr2['lebal'] = "Invoices";
        // $arr2['value'] = InvoiceTable::where('status', 'pending')->where('reference', 'INV')->count()."(".InvoiceTable::where('status', 'pending')->get()->sum('amount').")";
        // $arr2['lebal'] = "Refund Invoices";
        // $arr2['value'] = InvoiceTable::where('status', 'pending')->where('reference', 'RET')->count()."(".InvoiceTable::where('status', 'pending')->get()->sum('amount').")";

        //                             // Purchase Dashboard
        // $arr3['name'] = "PURCHASES";
        // $arr3['lebal'] = "Orders";
        // $arr3['value'] = PurchaseTable::where('status', 'pending')->where('reference', 'PO')->count()."(".PurchaseTable::where('status', 'pending')->get()->sum('amount').")";
        // $arr3['lebal'] = "Delivery Notes";
        // $arr3['value'] = PurchaseTable::where('status', 'pending')->where('reference', 'PDN')->count()."(".PurchaseTable::where('status', 'pending')->get()->sum('amount').")";
        // $arr3['lebal'] = "Invoices";
        // $arr3['value'] = PurchaseTable::where('status', 'pending')->where('reference', 'PINV')->count()."(".PurchaseTable::where('status', 'pending')->get()->sum('amount').")";
        

        // $data[] = $arr;
        // $data[] = $arr1;
        // $data[] = $arr2;
        // $data[] = $arr3;
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
        
    }
}
