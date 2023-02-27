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
                        "$". Item::where('type', 'inv')->sum('subtotal')
                    ] 
                ], 
                [
                        "type" => "bar", 
                        "label" => "Expenses", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            "$". InvoiceReceipt::where('type', 'inv')->where('paid', '1')->sum('amount')
                        ]  
                ], 
                [
                    "type" => "bar", 
                    "label" => "Profit", 
                    "backgroundColor" => "#FE9140", 
                    "data" => [
                        "$". Item::where('type', 'inv')->sum('subtotal') - InvoiceReceipt::where('type', 'inv')->where('paid', '1')->sum('amount')
                    ] 
                ] 
            ], 
            "sales_invoicing" => [
                [
                    "type" => "bar", 
                    "label" => "Invoiced", 
                    "backgroundColor" => "#26C184", 
                    "data" => [
                        "$". InvoiceReceipt::where('type', 'inv')->whereDate('expiration_date', '>', date('Y-m-d'))->sum('amount')
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

        $table = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($table);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $productTables = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($productTables);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);

        $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
        
        if($request->type == "clients"){
            $clients = Client::get();
            $data = [];
            $data['sales_invoicing'] = [];
            foreach($clients as $client){
                $data['sales_invoicing'][] = [
                        "type" => "bar",
                        "label" => "" .  $client->legal_name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                            "$". InvoiceTable::where('reference',$referenceType)->where('client_id',$client->id)->get()->sum('amount'),
                            ]
                        ];
            }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);
        }else if($request->type == "agents"){
            $clients = Client::get();
            $data = [];
            $data['invoice_agents'] = [];
            foreach($clients as $client){
                $data['invoice_agents'][] = [
                        "type" => "bar",
                        "label" => "" .  \Auth::user()->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                            "$". InvoiceTable::where('reference',$referenceType)->where('client_id',$client->id)->get()->sum('amount'),
                            ]
                        ];
            }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);

        }else if($request->type == "items"){
            $productTables = Product::get();
            $services = Service::get();
            $data = [];
            $data['invoice_items'] = [];
            foreach($productTables as $productTable){
                $data['invoice_items'][] = [
                        "type" => "bar",
                        "label" => "" .  $productTable->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                                InvoiceTable::with(['items'])->WhereHas('items', function ($query) use ($productTable,$referenceType) {
                                $query->where('reference_id', $productTable->id)->where('type', $referenceType);
                                })->get()->sum('amount'),
                            ]
                        ];
            }
            foreach($services as $service){
                $data['invoice_items'][] = [
                        "type" => "bar",
                        "label" => "" .  $service->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                                InvoiceTable::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                                $query->where('reference_id', $service->id)->where('type', $referenceType);
                                })->get()->sum('amount'),
                            ]
                        ];
            }
            
            return response()->json([
                "status" => true,
                "data" =>  $data
            ]);
        }   
    }

    public function invoiceClientHistory(Request $request){
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
            $arr = [];
            $data = [];

            foreach($clients as $client){
                $arr['name'] = $client->legal_name;
                $arr['invoiced'] = InvoiceTable::where('reference', $referenceType)->where('client_id',$client->id)->get()->sum('amount');
                $arr['paid'] = InvoiceTable::where('reference', $referenceType)->where('client_id',$client->id)->get()->sum('amount_paid');
                $arr['Unpaid'] = InvoiceTable::where('reference', $referenceType)->where('client_id',$client->id)->get()->sum('amount_due');

                $data[] = $arr;
            }
            
            return response()->json([
                "status" => true,
                "data" =>  $data
            ]);
    }
    public function invoiceAgentsHistory(Request $request){
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
            $arr = [];
            $data = [];
            // $arr['legal_name'] = \Auth::user()->name;
            //     $arr['pending'] = SalesEstimate::where('reference', $referenceType)->where('agent_id',\Auth::id())

                $arr['name'] = \Auth::user()->name;
                $arr['invoiced'] = InvoiceTable::where('reference', $referenceType)->where('agent_id',\Auth::id())->get()->sum('amount');
                $arr['paid'] = InvoiceTable::where('reference', $referenceType)->where('agent_id',\Auth::id())->get()->sum('amount_paid');
                $arr['Unpaid'] = InvoiceTable::where('reference', $referenceType)->where('agent_id',\Auth::id())->get()->sum('amount_due');

                $data[] = $arr;
            
            return response()->json([
                "status" => true,
                "data" =>  $data
            ]);
    }
    public function invoiceItemsHistory(Request $request){
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
           
            $arr = [];
            $data = [];

            foreach($products as $product){
                $arr['name'] = $product->name;
                $arr['reference'] = $product->reference.''.$product->reference_number;
                $arr['units'] = '';
                $arr['amount'] = InvoiceTable::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->where('type', $referenceType);
                })->get()->sum('amount_with_out_vat');
                

                $data[] = $arr;
            }
            foreach($services as $service){
                $arr['name'] = $service->name;
                $arr['reference'] = $service->reference.''.$service->reference_number;
                $arr['units'] = '';
                $arr['amount'] = InvoiceTable::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                    $query->where('reference_id', $service->id)->where('type', $referenceType);
                })->get()->sum('amount_with_out_vat');

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

        $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
        // dd($referenceType);

        $data = [];
        if( $request->type == "overview" ){
            $data = [
                "estimates_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending(". SalesEstimate::where('reference', 'se')->where('status', 'pending')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                        " " . SalesEstimate::where('reference', 'se')->where('status', 'pending')->count(),
                        ] 
                    ], 
                    [
                            "type" => "bar", 
                            "label" => "Refused(". SalesEstimate::where('reference', 'se')->where('status', 'refused')->count().")", 
                            "backgroundColor" => "#FB6363", 
                            "data" => [
                                " " . SalesEstimate::where('reference', 'se')->where('status', 'refused')->count(),
                                ] 
                        ], 
                    [
                        "type" => "bar", 
                        "label" => "Accepted(". SalesEstimate::where('reference', 'se')->where('status', 'accepted')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            " " . SalesEstimate::where('reference', 'se')->where('status', 'accepted')->count(), 
                        ] 
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed(". SalesEstimate::where('reference', 'se')->where('status', 'closed')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            "" . SalesEstimate::where('reference', 'se')->where('status', 'closed')->count(),
                        ] 
                    ],
            ], 
                "orders_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending (". SalesEstimate::where('reference', 'so')->where('status', 'pending')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            " ".SalesEstimate::where('reference', 'so')->where('status', 'pending')->count(),
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Refused (". SalesEstimate::where('reference', 'so')->where('status', 'refused')->count().")", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            " ". SalesEstimate::where('reference', 'so')->where('status', 'refused')->count(),
                        ]  
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "In Progress (". SalesEstimate::where('reference', 'so')->where('status', 'in progress')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            " ". SalesEstimate::where('reference', 'so')->where('status', 'in progress')->count(),
                        ] 
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed (". SalesEstimate::where('reference', 'so')->where('status', 'closed')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            " ". SalesEstimate::where('reference', 'so')->where('status', 'closed')->count(),
                        ] 
                    ],
                         
                ], 
                "delivery_notes_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending Invoice (". SalesEstimate::where('reference', 'sdn')->where('status', 'pending invoice')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            " ". SalesEstimate::where('reference', 'sdn')->where('status', 'pending invoice')->count(),
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "In Progress (". SalesEstimate::where('reference', 'sdn')->where('status', 'in progress')->count().")", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            " ". SalesEstimate::where('reference', 'sdn')->where('status', 'in progress')->count(),
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Closed (". SalesEstimate::where('reference', 'sdn')->where('status', 'closed')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            " ". SalesEstimate::where('reference', 'sdn')->where('status', 'closed')->count(),
                        ] 
                        ],
                    [
                        "type" => "bar", 
                        "label" => "Invoiced (". SalesEstimate::where('reference', 'sdn')->where('status', 'invoiced')->count().")",
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            " ". SalesEstimate::where('reference', 'sdn')->where('status', 'invoiced')->count(),
                        ] 
                    ]  
                ]
            ];

        }elseif( $request->type == "clients" ){
            $clients = Client::get();
            $data = [];
            $data['clients'] = [];
            foreach($clients as $client){
                $data['clients'][] = [
                        "type" => "bar",
                        "label" => "" .  $client->legal_name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                            "$". SalesEstimate::where('reference',$referenceType)->where('client_id',$client->id)->get()->sum('amount'),
                            ]
                        ];
            }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);

        }elseif($request->type == "agents"){
            $clients = Client::get();
            $data = [];
            $data['agents'] = [];
            foreach($clients as $client){
                $data['agents'][] = [
                        "type" => "bar",
                        "label" => "" .  \Auth::user()->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                            "$". SalesEstimate::where('reference',$referenceType)->where('client_id',$client->id)->get()->sum('amount'),
                            ]
                        ];
            }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);
        }elseif($request->type == "items"){
            $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $productTables = Product::get();
            $services = Service::get();
            $data = [];
            $data['invoice_items'] = [];
            foreach($productTables as $productTable){
                $data['invoice_items'][] = [
                        "type" => "bar",
                        "label" => "" .  $productTable->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                                SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($productTable,$referenceType) {
                                $query->where('reference_id', $productTable->id)->where('type', $referenceType);
                                })->get()->sum('amount'),
                            ]
                        ];
            }
            foreach($services as $service){
                $data['products'][] = [
                        "type" => "bar",
                        "label" => "" .  $service->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                                SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                                $query->where('reference_id', $service->id)->where('type', $referenceType);
                                })->get()->sum('amount'),
                            ]
                        ];
            }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);

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

    public function salesClientHistory(Request $request){
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

                $data[] = $arr;
            }
            
            return response()->json([
                "status" => true,
                "data" =>  $data
            ]);
    }
    public function salesAgentsHistory(Request $request){
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

        $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();

            $referenceType = Reference::where('type',$request->type)->pluck('prefix')->toArray();
            $clients = SalesEstimate::where('reference', $referenceType)->get();
            $arr = [];
            $data = [];

                $arr['name'] = \Auth::user()->name;
                $arr['pending'] = SalesEstimate::where('reference', $referenceType)->where('agent_id',\Auth::id())->where('status','pending')->count();
                $arr['refused'] = SalesEstimate::where('reference', $referenceType)->where('agent_id',\Auth::id())->where('status','refused')->count();
                $arr['accepted'] = SalesEstimate::where('reference', $referenceType)->where('agent_id',\Auth::id())->where('status','accepted')->count();
                $arr['closed'] = SalesEstimate::where('reference', $referenceType)->where('agent_id',\Auth::id())->where('status','closed')->count();
                $arr['total'] = SalesEstimate::where('reference', $referenceType)->where('agent_id',\Auth::id())->count();
                $arr['amount'] = SalesEstimate::where('reference', $referenceType)->where('agent_id',\Auth::id())->get()->sum('amount');

                $data[] = $arr;
            
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);

    }
    public function salesItemsHistory(Request $request){
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
                $arr['units'] = '';
                $arr['amount'] = SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($product,$referenceType) {
                    $query->where('reference_id', $product->id)->where('type', $referenceType);
                })->get()->sum('amount');
                

                $data[] = $arr;
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

                $data[] = $arr;
            }

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

        }elseif($request->type == "incidents_by_client"){
            $data = [
                "incidents_by_client" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            "$ ". TechnicalIncident::where('reference', 'inc')->where('status', 'pending')->count()
                        ] 
                    ], 
                    [
                            "type" => "bar", 
                            "label" => "Refused", 
                            "backgroundColor" => "#FB6363", 
                            "data" => [
                                "$ ". TechnicalIncident::where('reference', 'inc')->where('status', 'refused')->count()
                            ]
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Resolved", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            "$ ". TechnicalIncident::where('reference', 'inc')->where('status', 'resolved')->count()
                        ]
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            "$ ". TechnicalIncident::where('reference', 'inc')->where('status', 'closed')->count()
                        ]
                    ],
                ],
            ];
        }elseif($request->type == "incidents_by_agent"){
            $data = [
                "incidents_by_agent" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            "$ ". 500
                        ] 
                    ], 
                    [
                            "type" => "bar", 
                            "label" => "Refused", 
                            "backgroundColor" => "#FB6363", 
                            "data" => [
                                "$ ". 500
                            ]
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Resolved", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            "$ ". 500
                        ]
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            "$ ". 500
                        ]
                    ],
                ],
            ];
        }elseif($request->type == "agent" ){
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
    public function incidentByClientHistory(Request $request){
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
        if($request->type == 'incident_by_client')
        
            $clients = Client::get();

            // $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $arr = [];
            $data = [];

            foreach($clients as $client){
                $arr['name'] = $client->legal_name;
                $arr['pending'] = TechnicalIncident::where('client_id',$client->id)->where('status','pending')->count();
                $arr['refused'] = TechnicalIncident::where('client_id',$client->id)->where('status','refused')->count();
                $arr['accepted'] = TechnicalIncident::where('client_id',$client->id)->where('status','accepted')->count();
                $arr['closed'] = TechnicalIncident::where('client_id',$client->id)->where('status','closed')->count();
                $arr['total'] = TechnicalIncident::where('client_id',$client->id)->count();

                $data[] = $arr;
            }
            
            return response()->json([
                "status" => true,
                "data" =>  $data
            ]);
    }
    public function incidentByClient(Request $request){
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
        if($request->type == 'incident_by_client'){
            $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $clients = Client::get();
            $data = [];
            $data['clients'] = [];
            foreach($clients as $client){
                $data['clients'][] = [
                        "type" => "bar",
                        "label" => "" .  $client->legal_name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                            "$". TechnicalTable::where('reference',$referenceType)->where('client_id',$client->id)->get()->sum('amount'),
                            ]
                        ];
            }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);
        }elseif($request->type == 'by_client_history'){
                    
            $clients = Client::get();

            $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $arr = [];
            $data = [];

            foreach($clients as $client){
                $arr['name'] = $client->legal_name;
                $arr['pending'] = TechnicalTable::where('reference', $referenceType)->where('client_id',$client->id)->where('status','pending')->count();
                $arr['refused'] = TechnicalTable::where('reference', $referenceType)->where('client_id',$client->id)->where('status','refused')->count();
                $arr['accepted'] = TechnicalTable::where('reference', $referenceType)->where('client_id',$client->id)->where('status','accepted')->count();
                $arr['closed'] = TechnicalTable::where('reference', $referenceType)->where('client_id',$client->id)->where('status','closed')->count();
                $arr['total'] = TechnicalTable::where('reference', $referenceType)->where('client_id',$client->id)->count();
                $arr['amount'] = TechnicalTable::where('reference', $referenceType)->where('client_id',$client->id)->get()->sum('amount');

                $data[] = $arr;
            }
            
            return response()->json([
                "status" => true,
                "data" =>  $data
            ]);
        }
            
    }
    public function incidentByAgent(Request $request){
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
        if($request->type == 'incident_by_agent'){
            $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $clients = Client::get();
            $data = [];
            $data['clients'] = [];
            foreach($clients as $client){
                $data['clients'][] = [
                        "type" => "bar",
                        "label" => "" . \Auth::user()->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                            "$". TechnicalTable::where('reference',$referenceType)->where('client_id',$client->id)->get()->sum('amount'),
                            ]
                        ];
            }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);
        }elseif($request->type == 'by_agent_history'){

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

                $data[] = $arr;
            
            return response()->json([
                "status" => true,
                "data" =>  $data
            ]);
        }
            
    }
    public function incidentByItem(Request $request){
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
        if($request->type == 'incident_by_item'){
            $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $productTables = Product::get();
            $services = Service::get();
            $data = [];
            $data['invoice_items'] = [];
            foreach($productTables as $productTable){
                $data['invoice_items'][] = [
                        "type" => "bar",
                        "label" => "" .  $productTable->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                                TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($productTable,$referenceType) {
                                $query->where('reference_id', $productTable->id)->where('type', $referenceType);
                                })->get()->sum('amount'),
                            ]
                        ];
            }
            foreach($services as $service){
                $data['products'][] = [
                        "type" => "bar",
                        "label" => "" .  $service->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                                TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                                $query->where('reference_id', $service->id)->where('type', $referenceType);
                                })->get()->sum('amount'),
                            ]
                        ];
            }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);
        }elseif($request->type == 'by_item_history'){

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
                    })->get()->sum('amount');
                    
    
                    $data[] = $arr;
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
                    $arr['amount'] = TechnicalTable::with(['items'])->WhereHas('items', function ($query) use ($service,$referenceType) {
                        $query->where('reference_id', $service->id)->where('type', $referenceType);
                    })->get()->sum('amount');
    
                    $data[] = $arr;
                }
    
            return response()->json([
                "status" => true,
                "data" =>  $data
            ]);
        }
            
    }
    public function incidentByAgentHistory(Request $request){
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
        if($request->type == 'incident_by_agent')
        
            $clients = Client::get();

            // $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $arr = [];
            $data = [];

                $arr['name'] = \Auth::user()->name;;
                $arr['pending'] = TechnicalIncident::where('assigned_to',\Auth::id())->where('status','pending')->count();
                $arr['refused'] = TechnicalIncident::where('assigned_to',\Auth::id())->where('status','refused')->count();
                $arr['accepted'] = TechnicalIncident::where('assigned_to',\Auth::id())->where('status','accepted')->count();
                $arr['closed'] = TechnicalIncident::where('assigned_to',\Auth::id())->where('status','closed')->count();
                $arr['total'] = TechnicalIncident::where('assigned_to',\Auth::id())->count();

                $data[] = $arr;
            
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

        $productTables = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($productTables);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        if($request->type == "supplier"){
            $suppliers =  Supplier::has('purchases', '>', 3)->get();
            $data = [];
            $data['supplier'] = [];
            foreach($suppliers as $supplier){
                $data['supplier'][] = [
                        "type" => "bar",
                        "label" => "" .  $supplier->email,
                        "backgroundColor" => "#26C184",
                        "data" => [
                            PurchaseTable::where('id', $supplier->id)->get()->sum('amount'),
                            ]
                        ];
            }
            
            return response()->json([
                "status" => true,
                "data" =>  $data
            ]);

        }else{
            $items = Product::has('items', '>', 3)->get();
            $data = [];
            $data['item'] = [];
            foreach($items as $item){
                $data['item'][] = [
                        "type" => "bar",
                        "label" => "" .  $item->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                            Item::where('reference_id', $item->id)->where('reference', 'pro')->get()->sum('amount'),
                            ]
                        ];
            }
            
            return response()->json([
                "status" => true,
                "data" =>  $data
            ]);
        }
    }
    public function cashFlow(Request $request){
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
        $data = [];
        if($request->type == "overview"){
            $data = [
                "cash_flow" => [
                    [
                        "type" => "bar", 
                        "label" => "Deposits", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                        " " . InvoiceReceipt::where('type', 'inv')->where('paid', '1')->sum('amount')
                        ] 
                    ], 
                    [
                            "type" => "bar", 
                            "label" => "Withdrawals", 
                            "backgroundColor" => "#FB6363", 
                            "data" => [
                                " " . InvoiceReceipt::where('type', 'RET')->where('paid', '1')->sum('amount')
                            ] 
                        ], 
                    [
                        "type" => "bar", 
                        "label" => "Balance", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                        " " . InvoiceReceipt::where('type', 'inv')->where('paid', '1')->sum('amount') - InvoiceReceipt::where('type', 'RET')->where('paid', '1')->sum('amount')
                        ] 
                    ] 
                ], 
                "deposits" => [
                    [
                        "type" => "bar", 
                        "label" => "Invoices", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            "$ " . InvoiceReceipt::where('type', 'inv')->where('paid', '1')->sum('amount')
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Account Deposits", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            "$ ". Deposit::where('type','deposit')->where('paid','1')->sum('amount')
                        ]  
                    ]
                ], 
                "withdrawals" => [
                    [
                        "type" => "bar", 
                        "label" => "Refunds", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            "$ ". InvoiceReceipt::where('type', 'RET')->where('paid', '1')->sum('amount')
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
                            "$ ". Deposit::where('type','withdraw')->where('paid','1')->sum('amount')
                        ] 
                    ]  
                ]
            ];
        }elseif($request->type == 'paymentOption'){
            $paymentOptions = PaymentOption::get();
            // dd($products);
                $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
               
                $data = [];
                $data['invoice_items'] = [];
                foreach($paymentOptions as $paymentOption){
                    $data['invoice_items'][] = [
                            "type" => "bar",
                            "label" => "" .  $paymentOption->name,
                            "backgroundColor" => "#26C184",
                            "data" => [
                                    InvoiceTable::WhereHas('payment_options', function ($query) use ($paymentOption,$referenceType) {
                                    $query->where('payment_option', $paymentOption->id)->where('reference', $referenceType);
                                    })->get()->sum('amount'),
                                ]
                            ];
                }
                return response()->json([
                    "status" => true,
                    "data" =>  $data
                ]);
        }
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
    }
    public function paymentOptionHistory(Request $request){
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
       
        $arr = [];
        $data = [];

        foreach($paymentOptions as $paymentOption){
            $arr['name'] = $paymentOption->name;
            $arr['deposit'] = '';
            $arr['withdrawals'] = InvoiceReceipt::WhereHas('payment_options', function ($query) use ($paymentOption,$referenceType) {
                $query->where('payment_option', $paymentOption->id)->where('type', $referenceType);
            })->where('paid','1')->sum('amount');
            $arr['balance'] = '';
            

            $data[] = $arr;
        }
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);

    }
    public function cashFlowHistory(Request $request){
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

                $data[] = $arr;
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

                $data[] = $arr;
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

        $supplierTables = 'company_'.$request->company_idInciden.'_suppliers';
        Supplier::setGlobalTable($supplierTables); 

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        ConsumptionTax::setGlobalTable('company_'.$request->company_id.'_consumption_taxes');
        $query = ConsumptionTax::query();

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
            ],
            "subtotal_tax" => [
                [
                    "data" => [
                        // Item::where('type', 'inv')->sum('subtotal')
                    ]
                ]
            ]
        ];
        
        // $taxes = ConsumptionTax::get();
        // foreach($taxes as $key => $consumptionTax){
        //     $data = $consumptionTax->tax;
            
        // }
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);

    }
}
