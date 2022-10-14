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
use App\Models\PurchaseReceipt;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\TechnicalIncident;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function overview(Request $request){
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
        $client_id = Client::pluck('id')->toArray();
        $data = [
            "profit" => [
                [
                    "type" => "bar", 
                    "label" => "Sales", 
                    "backgroundColor" => "#26C184", 
                    "data" => [
                    "2900" 
                    ] 
                ], 
                [
                        "type" => "bar", 
                        "label" => "Expenses", 
                        "backgroundColor" => "#FB6363", 
                        "data" => ["3548"] 
                    ], 
                [
                    "type" => "bar", 
                    "label" => "Profit", 
                    "backgroundColor" => "#FE9140", 
                    "data" => [
                    "-4895" 
                    ] 
                ] 
            ], 
            "sales_invoicing" => [
                [
                    "type" => "bar", 
                    "label" => "Invoiced", 
                    "backgroundColor" => "#26C184", 
                    "data" => [
                        "$ ". InvoiceReceipt::where('type', 'inv')->whereDate('expiration_date', '>', date('Y-m-d'))->sum('amount')
                    ] 
                ], 
                [
                    "type" => "bar", 
                    "label" => "Paid", 
                    "backgroundColor" => "#FB6363", 
                    "data" => [
                        "$ ". InvoiceReceipt::where('type', 'inv')->where('paid', '1')->sum('amount')
                    ]  
                ], 
                [
                    "type" => "bar", 
                    "label" => "Unpaid", 
                    "backgroundColor" => "#FE9140", 
                    "data" => [
                        "$ ". InvoiceReceipt::where('type', 'inv')->where('paid', '0')->sum('amount')
                    ] 
                ] 
            ], 
            "purchase_invoicing" => [
                [
                    "type" => "bar", 
                    "label" => "Invoiced", 
                    "backgroundColor" => "#26C184", 
                    "data" => [
                        "$ ". PurchaseReceipt::where('type', 'pinv')->whereDate('expiration_date', '>', date('Y-m-d'))->sum('amount')
                    ] 
                ], 
                [
                    "type" => "bar", 
                    "label" => "Paid", 
                    "backgroundColor" => "#FB6363", 
                    "data" => [
                        "$ ". PurchaseReceipt::where('type', 'pinv')->where('paid', '1')->sum('amount')
                    ] 
                ], 
                [
                    "type" => "bar", 
                    "label" => "Unpaid", 
                    "backgroundColor" => "#FE9140", 
                    "data" => [
                        "$ ". PurchaseReceipt::where('type', 'pinv')->where('paid', '0')->sum('amount')
                    ] 
                ] 
            ],
            "expense_distribution" => 
            [
                [
                    "type" => "bar", 
                    "label" => "Personnel Expenses", 
                    "backgroundColor" => "#26C184", 
                    "data" => [
                        "2900" 
                    ] 
                ]
            ]  
        ];  
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
    }

    public function invoicing(Request $request){
        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables); 

        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTable);
        
        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);
        
        if($request->type == "agent"){
            $agent_ids  = InvoiceTable::pluck('agent_id')->groupBy('agent_id')->toArray();        
            $client_ids = Client::whereIn('id', $agent_ids)->pluck('id')->toArray();
        }else{
            $client_ids  = Client::pluck('id')->toArray();
        }

        $data = [];
        foreach($client_ids as $client_id){
            $invoiceDatas = InvoiceTable::with(['items', 'item_meta'])->where('client_id', $client_id)->get();
            $sum = 0;
            $arr['client_id'] = $client_id;
            // foreach($invoiceDatas as $invoiceData){
            //    $sum = $sum + $invoiceData->items()->sum('base_price');
            // }
            $arr['sum'] = InvoiceTable::with(['ireferencetems', 'item_meta'])->where('client_id', $client_id)->get()->sum('amount');
            $data[] = $arr;
        }
        
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
    }

    public function sales( Request $request ){
        $salesTables = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($salesTables);

        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables); 

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);
        $data = [];
        if( $request->type == "overview" ){
            $data = [
                "estimates_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending(10)", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                        "2900" 
                        ] 
                    ], 
                    [
                            "type" => "bar", 
                            "label" => "Refused(2)", 
                            "backgroundColor" => "#FB6363", 
                            "data" => ["3548"] 
                        ], 
                    [
                        "type" => "bar", 
                        "label" => "Accepted(4)", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                        "-4895" 
                        ] 
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed(0)", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            "$ ". 35
                        ] 
                    ],
            ], 
                "orders_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending (1)", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            "$ ". 35
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Refused (0)", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            "$ ". 3273
                        ]  
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "In Progress (0)", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            "$ ". 500
                        ] 
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed (1)", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            "$ ". 500
                        ] 
                    ],
                         
                ], 
                "delivery_notes_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending Invoice (0)", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            "$ ". 3474
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "In Progress (0)", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            "$ ". 300
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Closed (0)", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            "$ ". 200
                        ] 
                        ],
                    [
                        "type" => "bar", 
                        "label" => "Invoiced (0)", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            "$ ". 200
                        ] 
                    ]  
                ]
            ];

        }elseif( $request->type == "agent" ){
            $agent_ids  = SalesEstimate::pluck('agent_id')->toArray();
            $client_ids  = Client::whereIn('id', $agent_ids)->pluck('id')->toArray();

        }else{
            $client_ids  = Client::pluck('id')->toArray();
        }
        // foreach($client_ids as $client_id){
        //     $purchaseDatas = SalesEstimate::with(['items', 'item_meta'])->where('client_id', $client_id)->get();
        //     $sum = 0;
        //     $arr['client_id'] = $client_id;
        //     // foreach($purchaseDatas as $purchaseData){
        //     //    $sum = $sum + $purchaseData->items()->sum('base_price');
        //     // }
        //     $arr['sum'] = SalesEstimate::with(['items', 'item_meta'])->where('client_id', $client_id)->get()->sum('amount');
        //     $data[] = $arr;
        // }

        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
    }

    public function technicalService( Request $request ){
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
        $data = [];
        if( $request->type == "overview" ){
            $data = [
                "incidents_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            TechnicalIncident::where('reference', 'inc')->where('status', 'pending')->count()
                        ] 
                    ], 
                    [
                            "type" => "bar", 
                            "label" => "Refused", 
                            "backgroundColor" => "#FB6363", 
                            "data" => [
                                TechnicalIncident::where('reference', 'inc')->where('status', 'refused')->count()
                            ]
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Resolved", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            TechnicalIncident::where('reference', 'inc')->where('status', 'resolved')->count()
                        ]
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            TechnicalIncident::where('reference', 'inc')->where('status', 'closed')->count()
                        ]
                    ],
                ],
                "estimates_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending (". TechnicalTable::where('reference', 'we')->where('status', 'pending')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            "$".TechnicalTable::where('reference', 'we')->where('status', 'pending')->get()->sum('amount')
                        ]
                    ], 
                    [
                            "type" => "bar", 
                            "label" => "Refused (". TechnicalTable::where('reference', 'we')->where('status', 'refused')->count().")",
                            "backgroundColor" => "#FB6363", 
                            "data" => [
                                "$".TechnicalTable::where('reference', 'we')->where('status', 'refused')->get()->sum('amount')
                            ] 
                        ], 
                    [
                        "type" => "bar", 
                        "label" => "Accepted (". TechnicalTable::where('reference', 'we')->where('status', 'accepted')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            "$".TechnicalTable::where('reference', 'we')->where('status', 'accepted')->get()->sum('amount')
                        ]
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed (". TechnicalTable::where('reference', 'we')->where('status', 'closed')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            "$".TechnicalTable::where('reference', 'we')->where('status', 'closed')->get()->sum('amount')
                        ]
                    ],
                ], 
                "orders_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending (". TechnicalTable::where('reference', 'wo')->where('status', 'pending')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            "$".TechnicalTable::where('reference', 'wo')->where('status', 'pending')->get()->sum('amount')
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Refused (". TechnicalTable::where('reference', 'wo')->where('status', 'refused')->count().")",  
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            "$".TechnicalTable::where('reference', 'wo')->where('status', 'refused')->get()->sum('amount')
                        ]  
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "In Progress (". TechnicalTable::where('reference', 'wo')->where('status', 'in_progress')->count().")",
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            "$".TechnicalTable::where('reference', 'wo')->where('status', 'in_progress')->get()->sum('amount')
                        ] 
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed (". TechnicalTable::where('reference', 'wo')->where('status', 'closed')->count().")",
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            "$".TechnicalTable::where('reference', 'wo')->where('status', 'closed')->get()->sum('amount')
                        ] 
                    ],
                         
                ], 
                "delivery_notes_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending Invoice (". TechnicalTable::where('reference', 'wdn')->where('status', 'pending')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            "$".TechnicalTable::where('reference', 'wdn')->where('status', 'pending')->get()->sum('amount')
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "In Progress (". TechnicalTable::where('reference', 'wdn')->where('status', 'in_progress')->count().")", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            "$".TechnicalTable::where('reference', 'wdn')->where('status', 'pending')->get()->sum('amount')
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Closed (". TechnicalTable::where('reference', 'wdn')->where('status', 'closed')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            "$".TechnicalTable::where('reference', 'wdn')->where('status', 'closed')->get()->sum('amount')
                        ] 
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Invoiced  (". TechnicalTable::where('reference', 'invoiced')->where('status', 'closed')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            "$".TechnicalTable::where('reference', 'wdn')->where('status', 'invoiced')->get()->sum('amount')
                        ] 
                    ]  
                ]
            ];

        }elseif( $request->type == "agent" ){
            $agent_ids  = TechnicalTable::pluck('agent_id')->toArray();
            $client_ids  = Client::whereIn('id', $agent_ids)->pluck('id')->toArray();

        }else{
            $client_ids  = Client::pluck('id')->toArray();
        }
        // foreach($client_ids as $client_id){
        //     $technicalDatas = TechnicalTable::with(['items', 'item_meta'])->where('client_id', $client_id)->get();
        //     $sum = 0;
        //     $arr['client_id'] = $client_id;
        //     // foreach($technicalDatas as $technicalData){
        //     //    $sum = $sum + $technicalData->items()->sum('base_price');
        //     // }
        //     $arr['sum'] = TechnicalTable::with(['items', 'item_meta'])->where('client_id', $client_id)->get()->sum('amount');
        //     $data[] = $arr;
        // }

        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
    }

    public function purchases( Request $request ){
        $purchaseTables = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($purchaseTables); 

        $supplierTables = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($supplierTables); 

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        if($request->type == "supplier"){
            $supplierArr  = PurchaseTable::pluck('supplier_id')->toArray();        
            $supplier_ids = Supplier::whereIn('id', $supplierArr)->pluck('id')->toArray();
            $data = [];
            foreach($supplier_ids as $supplier_id){
                $purchaseDatas = PurchaseTable::with(['items', 'item_meta'])->where('supplier_id', $supplier_id)->get();
                $sum = 0;
                $arr['supplier_id'] = $supplier_id;
                // foreach($purchaseDatas as $purchaseData){
                //     $sum = $sum + $purchaseData->items()->sum('base_price');
                // }
                $arr['sum'] = PurchaseTable::with(['items', 'item_meta'])->where('supplier_id', $supplier_id)->get()->sum('amount');
                $data[] = $arr;
            }

        }else{
            $data = [];
            $purchaseDatas = PurchaseTable::with(['items', 'item_meta'])->get();
            
            $sum = 0;
            foreach($purchaseDatas as $purchaseData){
                $arr['name'] = $purchaseData->title;
                $sum = $sum + $purchaseData->items()->sum('base_price');
                $arr['sum'] = $sum;
                $data[] = $arr;
            }
            
        }
        
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
    }
    public function cashFlow(Request $request){
        $purchaseTables = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($purchaseTables); 

        $supplierTables = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($supplierTables); 

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);
        $data = [];
        if($request->type == "overview"){
            $data = [
                "cash_flow" => [
                    [
                        "type" => "bar", 
                        "label" => "Deposits", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                        "2900" 
                        ] 
                    ], 
                    [
                            "type" => "bar", 
                            "label" => "Withdrawals", 
                            "backgroundColor" => "#FB6363", 
                            "data" => ["3548"] 
                        ], 
                    [
                        "type" => "bar", 
                        "label" => "Balance", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                        "-4895" 
                        ] 
                    ] 
                ], 
                "deposits" => [
                    [
                        "type" => "bar", 
                        "label" => "Invoices", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            "$ ". 35
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Account Deposits", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            "$ ". 3273
                        ]  
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Unpaid", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            "$ ". 500
                        ] 
                    ] 
                ], 
                "withdrawals" => [
                    [
                        "type" => "bar", 
                        "label" => "Refunds", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            "$ ". 3474
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Purchases", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            "$ ". 300
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Tickets and other expenses", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            "$ ". 200
                        ] 
                        ],
                    [
                        "type" => "bar", 
                        "label" => "Account Withdrawals", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            "$ ". 200
                        ] 
                    ]  
                ]
            ];
        }
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
    }
    public function stockValuation(Request $request){
        $purchaseTables = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($purchaseTables); 

        $productTable = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($productTable); 

        $supplierTables = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($supplierTables); 

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);
        $data = [];
        $data = [
            "stock_valuation" => [
                [
                    "type" => "bar", 
                    "label" => "Sales stock value", 
                    "backgroundColor" => "#26C184", 
                    "data" => [
                        "$ ". Product::get()->sum('sales_stock_value') 
                    ] 
                ], 
                [
                        "type" => "bar", 
                        "label" => "Purchase stock value", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            "$ ". Product::get()->sum('purchase_stock_value') 
                        ] 
                ], 
            ]
        ];
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
    }
    public function taxSummary(Request $request){
        $purchaseTables = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($purchaseTables); 

        $supplierTables = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($supplierTables); 

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);
        $data = [];
        
        $data = [
            "tax_Summary" => [
                [
                    "type" => "bar", 
                    "label" => "Collected", 
                    "backgroundColor" => "#26C184", 
                    "data" => [
                    "$3847" 
                    ] 
                ], 
                [
                        "type" => "bar", 
                        "label" => "Paid", 
                        "backgroundColor" => "#FB6363", 
                        "data" => ["$88"] 
                    ], 
                [
                    "type" => "bar", 
                    "label" => "Total", 
                    "backgroundColor" => "#FE9140", 
                    "data" => [
                    "$756" 
                    ] 
                ] 
            ]
        ];
        
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
    }
}
