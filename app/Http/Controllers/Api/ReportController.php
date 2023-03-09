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
use App\Models\ExpenseAndInvestment;
use App\Models\SupplierSpecialPrice;
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

        // $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
        
        if($request->type == "clients"){
            $client_ids = InvoiceTable::with('client')->pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$client_ids)->get();
            $data = [];
            $data['invoice_client'] = [];
            foreach($clients as $client){
                $data['invoice_client'][] = [
                        "type" => "bar",
                        "label" => "" .  $client->legal_name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                             InvoiceTable::filter($request->all())->where('client_id',$client->id)->get()->sum('amount'),
                            ]
                        ];
            }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);
        }else if($request->type == "agents"){
            // $clients = Client::get();
            $data = [];
            $data['invoice_agents'] = [];
            // foreach($clients as $client){
                $data['invoice_agents'][] = [
                        "type" => "bar",
                        "label" => "" .  \Auth::user()->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                             InvoiceTable::filter($request->all())->get()->sum('amount'),
                            ]
                        ];
            // }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);

        }else if($request->type == "items"){
            $referenceType = Reference::whereIn('type', ['Normal Invoice', 'Refund Invoice'])->pluck('prefix')->toArray();
            $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
            $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
            $products = Product::whereIn('id',$itemProductIds)->get();
            $services = Service::whereIn('id',$itemServiceIds)->get();

            $data = [];
            $data['invoice_items'] = [];
            foreach($products as $product){
                $data['invoice_items'][] = [
                        "type" => "bar",
                        "label" => "" .  $product->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                                InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                                $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
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
                                InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                                $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
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

        $table = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($table);

        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);
            $client_ids = InvoiceTable::pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$client_ids)->get();
            // $referenceType = Reference::where('type', $request->type)->pluck('prefix')->toArray();
            $arr = [];
            $data = [];

            foreach($clients as $client){
                $arr['name'] = $client->legal_name;
                $arr['invoiced'] = InvoiceTable::filter($request->all())->where('client_id',$client->id)->get()->sum('amount');
                $arr['paid'] = InvoiceTable::filter($request->all())->where('client_id',$client->id)->get()->sum('amount_paid');
                $arr['Unpaid'] = InvoiceTable::filter($request->all())->where('client_id',$client->id)->get()->sum('amount_due');
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

        $table = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($table);

        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);
            // $clients = Client::get();
            $arr = [];
            $data = [];

                $arr['name'] = \Auth::user()->name;
                $arr['invoiced'] = InvoiceTable::filter($request->all())->where('agent_id',\Auth::id())->get()->sum('amount');
                $arr['paid'] = InvoiceTable::filter($request->all())->where('agent_id',\Auth::id())->get()->sum('amount_paid');
                $arr['Unpaid'] = InvoiceTable::filter($request->all())->where('agent_id',\Auth::id())->get()->sum('amount_due');

                $data[] = $arr;
            
            return response()->json([
                "status" => true,
                "data" =>  $data
            ]);
    }
    public function invoiceItemsHistory(Request $request){
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
        
        $referenceType = Reference::whereIn('type', ['Normal Invoice', 'Refund Invoice'])->pluck('prefix')->toArray();
        $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
        $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
        $products = Product::whereIn('id',$itemProductIds)->get();
        $services = Service::whereIn('id',$itemServiceIds)->get();
        // dd($products);
           
            $arr = [];
            $data = [];

            foreach($products as $product){
                $arr['name'] = $product->name;
                $arr['reference'] = $product->reference.''.$product->reference_number;
                $arr['units'] = InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                })->count();
                $arr['amount'] = InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                })->get()->sum('amount_with_out_vat');
                

                $data[] = $arr;
            }
            foreach($services as $service){
                $arr['name'] = $service->name;
                $arr['reference'] = $service->reference.''.$service->reference_number;
                $arr['units'] = InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                })->count();
                $arr['amount'] = InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
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
            // $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $client_ids = SalesEstimate::with('client')->pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$client_ids)->get();
            $data = [];
            $data['sales_clients'] = [];
            foreach($clients as $client){
                $data['sales_clients'][] = [
                        "type" => "bar",
                        "label" => "" .  $client->legal_name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                             SalesEstimate::filter($request->all())->where('client_id',$client->id)->get()->sum('amount'),
                            ]
                        ];
            }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);
            
        }elseif($request->type == "agents"){
            $data = [];
            $data['sales_agents'] = [];
            // foreach($clients as $client){
                $data['sales_agents'][] = [
                        "type" => "bar",
                        "label" => "" .  \Auth::user()->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                                SalesEstimate::filter($request->all())->get()->sum('amount'),
                            ]
                        ];
            // }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);
        }elseif($request->type == "items"){
            // $referenceType = Reference::whereIn('type', ['Normal Invoice', 'Refund Invoice'])->pluck('prefix')->toArray();
            $itemProductIds = Item::whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
            $itemServiceIds = Item::whereIn('reference',['SER'])->pluck('reference_id')->toArray();
            $products = Product::whereIn('id',$itemProductIds)->get();
            $services = Service::whereIn('id',$itemServiceIds)->get();
            $data = [];
            $data['sales_items'] = [];
            foreach($products as $products){
                $data['sales_items'][] = [
                        "type" => "bar",
                        "label" => "" .  $products->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                                SalesEstimate::filter($request->all())->WhereHas('items', function ($query) use ($products) {
                                $query->where('reference_id', $products->id)->whereIn('reference',['PRO']);
                                })->get()->sum('amount'),
                            ]
                        ];
            }
            foreach($services as $service){
                $data['sales_items'][] = [
                        "type" => "bar",
                        "label" => "" .  $service->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                                SalesEstimate::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                                $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                                })->get()->sum('amount'),
                            ]
                        ];
            }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);

        }

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
        
        $client_ids = SalesEstimate::with('client')->pluck('client_id')->toArray();
        $clients = Client::whereIn('id',$client_ids)->get();

            // $referenceType = Reference::where('type', $request->type)->pluck('prefix')->toArray();
            $arr = [];
            $data = [];

            foreach($clients as $client){
                $arr['name'] = $client->legal_name;
                $arr['pending'] = SalesEstimate::filter($request->all())->where('client_id',$client->id)->where('status','pending')->count();
                $arr['refused'] = SalesEstimate::filter($request->all())->where('client_id',$client->id)->where('status','refused')->count();
                $arr['accepted'] = SalesEstimate::filter($request->all())->where('client_id',$client->id)->where('status','accepted')->count();
                $arr['closed'] = SalesEstimate::filter($request->all())->where('client_id',$client->id)->where('status','closed')->count();
                $arr['total'] = SalesEstimate::filter($request->all())->where('client_id',$client->id)->count();
                $arr['amount'] = SalesEstimate::filter($request->all())->where('client_id',$client->id)->get()->sum('amount');

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

            $arr = [];
            $data = [];

                $arr['name'] = \Auth::user()->name;
                $arr['pending'] = SalesEstimate::filter($request->all())->where('agent_id',\Auth::id())->where('status','pending')->count();
                $arr['refused'] = SalesEstimate::filter($request->all())->where('agent_id',\Auth::id())->where('status','refused')->count();
                $arr['accepted'] = SalesEstimate::filter($request->all())->where('agent_id',\Auth::id())->where('status','accepted')->count();
                $arr['closed'] = SalesEstimate::filter($request->all())->where('agent_id',\Auth::id())->where('status','closed')->count();
                $arr['total'] = SalesEstimate::filter($request->all())->where('agent_id',\Auth::id())->count();
                $arr['amount'] = SalesEstimate::filter($request->all())->where('agent_id',\Auth::id())->get()->sum('amount');

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

        $itemProductIds = Item::whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
        $itemServiceIds = Item::whereIn('reference',['SER'])->pluck('reference_id')->toArray();
        $products = Product::whereIn('id',$itemProductIds)->get();
        $services = Service::whereIn('id',$itemServiceIds)->get();
           
            $arr = [];
            $data = [];

            foreach($products as $product){
                $arr['name'] = $product->name;
                $arr['reference'] = $product->reference.''.$product->reference_number;
                $arr['pending'] = SalesEstimate::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                })->where('status','pending')->count();
                $arr['accepted'] = SalesEstimate::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                })->where('status','accepted')->count();
                $arr['closed'] = SalesEstimate::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                })->where('status','closed')->count();
                $arr['refused'] = SalesEstimate::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                })->where('status','refused')->count();
                $arr['total'] = SalesEstimate::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                })->count();
                $arr['units'] = SalesEstimate::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                })->count();
                $arr['amount'] = SalesEstimate::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                })->get()->sum('amount_with_out_vat');
                

                $data[] = $arr;
            }
            foreach($services as $service){
                $arr['name'] = $service->name;
                $arr['reference'] = $service->reference.''.$service->reference_number;
                $arr['pending'] = SalesEstimate::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                })->where('status','pending')->count();
                $arr['accepted'] = SalesEstimate::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                })->where('status','accepted')->count();
                $arr['closed'] = SalesEstimate::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                })->where('status','closed')->count();
                $arr['refused'] = SalesEstimate::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                })->where('status','refused')->count();
                $arr['total'] = SalesEstimate::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                })->count();
                $arr['units'] = SalesEstimate::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                })->count();
                $arr['amount'] = SalesEstimate::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                })->get()->sum('amount_with_out_vat');

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
                            TechnicalTable::where('reference', 'we')->where('status', 'pending')->get()->sum('amount')
                        ]
                    ], 
                    [
                            "type" => "bar", 
                            "label" => "Refused (". TechnicalTable::where('reference', 'we')->where('status', 'refused')->count().")",
                            "backgroundColor" => "#FB6363", 
                            "data" => [
                                TechnicalTable::where('reference', 'we')->where('status', 'refused')->get()->sum('amount')
                            ] 
                        ], 
                    [
                        "type" => "bar", 
                        "label" => "Accepted (". TechnicalTable::where('reference', 'we')->where('status', 'accepted')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            TechnicalTable::where('reference', 'we')->where('status', 'accepted')->get()->sum('amount')
                        ]
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed (". TechnicalTable::where('reference', 'we')->where('status', 'closed')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            TechnicalTable::where('reference', 'we')->where('status', 'closed')->get()->sum('amount')
                        ]
                    ],
                ], 
                "orders_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending (". TechnicalTable::where('reference', 'wo')->where('status', 'pending')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            TechnicalTable::where('reference', 'wo')->where('status', 'pending')->get()->sum('amount')
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Refused (". TechnicalTable::where('reference', 'wo')->where('status', 'refused')->count().")",  
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            TechnicalTable::where('reference', 'wo')->where('status', 'refused')->get()->sum('amount')
                        ]  
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "In Progress (". TechnicalTable::where('reference', 'wo')->where('status', 'in_progress')->count().")",
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            TechnicalTable::where('reference', 'wo')->where('status', 'in_progress')->get()->sum('amount')
                        ] 
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed (". TechnicalTable::where('reference', 'wo')->where('status', 'closed')->count().")",
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            TechnicalTable::where('reference', 'wo')->where('status', 'closed')->get()->sum('amount')
                        ] 
                    ],
                         
                ], 
                "delivery_notes_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending Invoice (". TechnicalTable::where('reference', 'wdn')->where('status', 'pending')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            TechnicalTable::where('reference', 'wdn')->where('status', 'pending')->get()->sum('amount')
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "In Progress (". TechnicalTable::where('reference', 'wdn')->where('status', 'in_progress')->count().")", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            TechnicalTable::where('reference', 'wdn')->where('status', 'pending')->get()->sum('amount')
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Closed (". TechnicalTable::where('reference', 'wdn')->where('status', 'closed')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            TechnicalTable::where('reference', 'wdn')->where('status', 'closed')->get()->sum('amount')
                        ] 
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Invoiced  (". TechnicalTable::where('reference', 'invoiced')->where('status', 'closed')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            TechnicalTable::where('reference', 'wdn')->where('status', 'invoiced')->get()->sum('amount')
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
        
            // $client_ids = TechnicalIncident::with('client')->pluck('client_id')->toArray();
            $clients = Client::get();

            // $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $arr = [];
            $data = [];

            foreach($clients as $client){
                $arr['name'] = $client->legal_name;
                $arr['pending'] = TechnicalIncident::filter($request->all())->where('client_id',$client->id)->where('status','pending')->count();
                $arr['refused'] = TechnicalIncident::filter($request->all())->where('client_id',$client->id)->where('status','refused')->count();
                $arr['accepted'] = TechnicalIncident::filter($request->all())->where('client_id',$client->id)->where('status','accepted')->count();
                $arr['closed'] = TechnicalIncident::filter($request->all())->where('client_id',$client->id)->where('status','closed')->count();
                $arr['total'] = TechnicalIncident::filter($request->all())->where('client_id',$client->id)->count();

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
            // $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $client_ids = TechnicalTable::pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$client_ids)->get();
            $data = [];
            $data['incident_clients'] = [];
            foreach($clients as $client){
                $data['incident_clients'][] = [
                        "type" => "bar",
                        "label" => "" .  $client->legal_name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                             TechnicalTable::filter($request->all())->where('client_id',$client->id)->get()->sum('amount'),
                            ]
                        ];
            }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);
        }elseif($request->type == 'by_client_history'){
                    
            $client_ids = TechnicalTable::pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$client_ids)->get();

            // $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $arr = [];
            $data = [];

            foreach($clients as $client){
                $arr['name'] = $client->legal_name;
                $arr['pending'] = TechnicalTable::filter($request->all())->where('client_id',$client->id)->where('status','pending')->count();
                $arr['refused'] = TechnicalTable::filter($request->all())->where('client_id',$client->id)->where('status','refused')->count();
                $arr['accepted'] = TechnicalTable::filter($request->all())->where('client_id',$client->id)->where('status','accepted')->count();
                $arr['closed'] = TechnicalTable::filter($request->all())->where('client_id',$client->id)->where('status','closed')->count();
                $arr['total'] = TechnicalTable::filter($request->all())->where('client_id',$client->id)->count();
                $arr['amount'] = TechnicalTable::filter($request->all())->where('client_id',$client->id)->get()->sum('amount');

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
            $data = [];
            $data['incident_agent'] = [];
            // foreach($clients as $client){
                $data['incident_agent'][] = [
                        "type" => "bar",
                        "label" => "" . \Auth::user()->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                             TechnicalTable::filter($request->all())->get()->sum('amount'),
                            ]
                        ];
            // }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);
        }elseif($request->type == 'by_agent_history'){

            // $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $arr = [];
            $data = [];

            $arr['name'] = \Auth::user()->name;
            $arr['pending'] = TechnicalTable::filter($request->all())->where('agent_id',\Auth::id())->where('status','pending')->count();
            $arr['refused'] = TechnicalTable::filter($request->all())->where('agent_id',\Auth::id())->where('status','refused')->count();
            $arr['accepted'] = TechnicalTable::filter($request->all())->where('agent_id',\Auth::id())->where('status','accepted')->count();
            $arr['closed'] = TechnicalTable::filter($request->all())->where('agent_id',\Auth::id())->where('status','closed')->count();
            $arr['total'] = TechnicalTable::filter($request->all())->where('agent_id',\Auth::id())->count();
            $arr['amount'] = TechnicalTable::filter($request->all())->where('agent_id',\Auth::id())->get()->sum('amount');

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
            
            $itemProductIds = Item::whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
            $itemServiceIds = Item::whereIn('reference',['SER'])->pluck('reference_id')->toArray();
            $products = Product::whereIn('id',$itemProductIds)->get();
            $services = Service::whereIn('id',$itemServiceIds)->get();

            $data = [];
            $data['invoice_items'] = [];
            foreach($products as $product){
                $data['invoice_items'][] = [
                        "type" => "bar",
                        "label" => "" .  $product->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                                TechnicalTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                                $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
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
                                TechnicalTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                                $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                                })->get()->sum('amount'),
                            ]
                        ];
            }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);
        }elseif($request->type == 'by_item_history'){

            // $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $itemProductIds = Item::whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
            $itemServiceIds = Item::whereIn('reference',['SER'])->pluck('reference_id')->toArray();
            $products = Product::whereIn('id',$itemProductIds)->get();
            $services = Service::whereIn('id',$itemServiceIds)->get();
               
                $arr = [];
                $data = [];
    
                foreach($products as $product){
                    $arr['name'] = $product->name;
                    $arr['reference'] = $product->reference.''.$product->reference_number;
                    $arr['pending'] = TechnicalTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                        $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                    })->where('status','pending')->count();
                    $arr['accepted'] = TechnicalTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                        $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                    })->where('status','accepted')->count();
                    $arr['closed'] = TechnicalTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                        $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                    })->where('status','closed')->count();
                    $arr['refused'] = TechnicalTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                        $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                    })->where('status','refused')->count();
                    $arr['total'] = TechnicalTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                        $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                    })->count();
                    $arr['units'] = TechnicalTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                        $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                    })->count();
                    $arr['amount'] = TechnicalTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                        $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                    })->get()->sum('amount');
                    
    
                    $data[] = $arr;
                }
                foreach($services as $service){
                    $arr['name'] = $service->name;
                    $arr['reference'] = $service->reference.''.$service->reference_number;
                    $arr['pending'] = TechnicalTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                        $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                    })->where('status','pending')->count();
                    $arr['accepted'] = TechnicalTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                        $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                    })->where('status','accepted')->count();
                    $arr['closed'] = TechnicalTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                        $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                    })->where('status','closed')->count();
                    $arr['refused'] = TechnicalTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                        $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                    })->where('status','refused')->count();
                    $arr['total'] = TechnicalTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                        $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                    })->count();
                    $arr['amount'] = TechnicalTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                        $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
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
        
            $clients = Client::get();

            // $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $arr = [];
            $data = [];

                $arr['name'] = \Auth::user()->name;
                $arr['pending'] = TechnicalIncident::where('assigned_to',\Auth::id())->where('status','Pending')->count();
                $arr['refused'] = TechnicalIncident::where('assigned_to',\Auth::id())->where('status','Refused')->count();
                $arr['resolved'] = TechnicalIncident::where('assigned_to',\Auth::id())->where('status','Resolved')->count();
                $arr['closed'] = TechnicalIncident::where('assigned_to',\Auth::id())->where('status','Closed')->count();
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
        
        $table = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($table);

        $productTables = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($productTables);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $table = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_expense_and_investments';
        ExpenseAndInvestment::setGlobalTable($table);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        if($request->type == "supplier"){

            $supplier_ids = PurchaseTable::with('supplier')->pluck('supplier_id')->toArray();
            $suppliers = Supplier::whereIn('id',$supplier_ids)->get();
            $data = [];
            $data['purchase_supplier'] = [];
            foreach($suppliers as $supplier){
                $data['purchase_supplier'][] = [
                        "type" => "bar",
                        "label" => "" .  $supplier->legal_name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                             PurchaseTable::filter($request->all())->where('supplier_id',$supplier->id)->where('reference','PINV')->get()->sum('amount'),
                            ]
                        ];
            }
            
            return response()->json([
                "status" => true,
                "data" =>  $data
            ]);

        }elseif($request->type == 'items'){
            $referenceType = Reference::whereIn('type', ['Purchase Invoice'])->pluck('prefix')->toArray();
            $itemProductIds = Item::with('supplier')->whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
            $itemServiceIds = Item::with('supplier')->whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
            $expenseInvestmentIds = Item::with('supplier')->whereIn('type',$referenceType)->whereIn('reference',['EAI'])->pluck('reference_id')->toArray();
            $products = Product::whereIn('id',$itemProductIds)->get();
            $services = Service::whereIn('id',$itemServiceIds)->get();
            $expenses = ExpenseAndInvestment::whereIn('id',$expenseInvestmentIds)->get();

            $data = [];
            $data['purchase_items'] = [];
            foreach($products as $product){
                $data['purchase_items'][] = [
                        "type" => "bar",
                        "label" => "" .  $product->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                            PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                                $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                                })->get()->sum('amount'),
                            ]
                        ];
            }
            foreach($services as $service){
                $data['purchase_items'][] = [
                        "type" => "bar",
                        "label" => "" .  $service->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                            PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                                $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                                })->get()->sum('amount'),
                            ]
                        ];
            }
            foreach($expenses as $expense){
                $data['purchase_items'][] = [
                        "type" => "bar",
                        "label" => "" .  $expense->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                            PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($expense) {
                                $query->where('reference_id', $expense->id)->whereIn('reference',['EAI']);
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
    public function purchaseSupplierHistory(Request $request){

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
        
        $referenceType = Reference::whereIn('type', ['Purchase Invoice'])->pluck('prefix')->toArray();
        $supplier_ids = PurchaseTable::with('supplier')->whereIn('reference',$referenceType)->pluck('supplier_id')->toArray();
        $suppliers = Supplier::whereIn('id',$supplier_ids)->get();

            // $referenceType = Reference::where('type', $request->type)->pluck('prefix')->toArray();
            $arr = [];
            $data = [];

            foreach($suppliers as $supplier){
                $arr['name'] = $supplier->legal_name;
                $arr['invoiced'] = PurchaseTable::filter($request->all())->where('supplier_id',$supplier->id)->get()->sum('amount');
                $arr['paid'] =  PurchaseTable::filter($request->all())->where('supplier_id',$supplier->id)->get()->sum('amount_paid');
                $arr['Unpaid'] =  PurchaseTable::filter($request->all())->where('supplier_id',$supplier->id)->get()->sum('amount_due');

                $data[] = $arr;
            }
            
            return response()->json([
                "status" => true,
                "data" =>  $data
            ]);
    }
    public function purchaseItemHistory(Request $request){

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
        
        $referenceType = Reference::whereIn('type', ['Purchase Invoice'])->pluck('prefix')->toArray();
        $itemProductIds = Item::with('supplier')->whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
        $itemServiceIds = Item::with('supplier')->whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
        $expenseInvestmentIds = Item::with('supplier')->whereIn('type',$referenceType)->whereIn('reference',['EAI'])->pluck('reference_id')->toArray();
        $products = Product::whereIn('id',$itemProductIds)->get();
        $services = Service::whereIn('id',$itemServiceIds)->get();
        $expenses = ExpenseAndInvestment::whereIn('id',$expenseInvestmentIds)->get();
           
            $arr = [];
            $data = [];

            foreach($products as $product){
                $arr['name'] = $product->name;
                $arr['reference'] = $product->reference.''.$product->reference_number;
                $arr['units'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                })->count();
                $arr['amount'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                })->get()->sum('amount_with_out_vat');
                

                $data[] = $arr;
            }
            foreach($services as $service){
                $arr['name'] = $service->name;
                $arr['reference'] = $service->reference.''.$service->reference_number;
                $arr['units'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                })->count();
                $arr['amount'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                })->get()->sum('amount_with_out_vat');
                

                $data[] = $arr;
            }
            foreach($expenses as $expense){
                $arr['name'] = $expense->name;
                $arr['reference'] = $expense->reference.''.$expense->reference_number;
                $arr['units'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($expense) {
                    $query->where('reference_id', $expense->id)->whereIn('reference',['EAI']);
                })->count();
                $arr['amount'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($expense) {
                    $query->where('reference_id', $expense->id)->whereIn('reference',['EAI']);
                })->get()->sum('amount_with_out_vat');
                

                $data[] = $arr;
            }
            return response()->json([
                "status" => true,
                "data" =>  $data
            ]);
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
                             InvoiceReceipt::where('type', 'inv')->where('paid', '1')->sum('amount')
                        ] 
                    ], 
                    [
                            "type" => "bar", 
                            "label" => "Withdrawals", 
                            "backgroundColor" => "#FB6363", 
                            "data" => [
                                InvoiceReceipt::where('type', 'RET')->where('paid', '1')->sum('amount')
                            ] 
                        ], 
                    [
                        "type" => "bar", 
                        "label" => "Balance", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                             InvoiceReceipt::where('type', 'inv')->where('paid', '1')->sum('amount') - InvoiceReceipt::where('type', 'RET')->where('paid', '1')->sum('amount')
                        ] 
                    ] 
                ], 
                "deposits" => [
                    [
                        "type" => "bar", 
                        "label" => "Invoices", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            InvoiceReceipt::where('type', 'inv')->where('paid', '1')->sum('amount')
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Account Deposits", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                             Deposit::where('type','deposit')->where('paid_by','1')->sum('amount')
                        ]  
                    ]
                ], 
                "withdrawals" => [
                    [
                        "type" => "bar", 
                        "label" => "Refunds", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                             InvoiceReceipt::where('type', 'RET')->where('paid', '1')->sum('amount')
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
                            "$ ". Deposit::where('type','withdraw')->where('paid_by','1')->sum('amount')
                        ] 
                    ]  
                ]
            ];
        }elseif($request->type == 'paymentOption'){
            $paymentOptions = PaymentOption::get();
            // dd($products);
                // $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
               
                $data = [];
                $data['invoice_items'] = [];
                foreach($paymentOptions as $paymentOption){
                    $data['invoice_items'][] = [
                            "type" => "bar",
                            "label" => "" .  $paymentOption->name,
                            "backgroundColor" => "#26C184",
                            "data" => [
                                Deposit::WhereHas('payment_options', function ($query) use ($paymentOption) {
                                    $query->where('payment_option', $paymentOption->id)->where('type','deposit');
                                    })->get()->sum('amount') - Deposit::WhereHas('payment_options', function ($query) use ($paymentOption) {
                                        $query->where('payment_option', $paymentOption->id)->where('type','withdraw');
                                        })->get()->sum('amount'),
                                ]
                            ];
                }
                return response()->json([
                    "status" => true,
                    "data" =>  $data
                ]);
        }elseif($request->type =='agents'){

            $data['agents'] = [];
                $data['agents'][] = [
                        "type" => "bar",
                        "label" => "" .  \Auth::user()->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                            Deposit::where('type','deposit')->where('paid_by','1')->sum('amount') -  Deposit::where('type','withdraw')->where('paid_by','1')->sum('amount'),
                            ]
                        ];
            return response()->json([
                "status" => true,
                "data" => $data
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

        $paymentOptions = PaymentOption::get();
       
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
            

            $data[] = $arr;
        }
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);

    }
    public function cashFlowAgentHistory(Request $request){
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

        $paymentOptions = PaymentOption::get();
       
        $arr = [];
        $data = [];

            $arr['name'] = \Auth::user()->name;
            $arr['deposit'] = Deposit::where('type','deposit')->where('paid_by','1')->sum('amount');
            $arr['withdrawals'] = Deposit::where('type','withdraw')->where('paid_by','1')->sum('amount');
            $arr['balance'] = Deposit::where('type','deposit')->where('paid_by','1')->sum('amount') - Deposit::where('type','withdraw')->where('paid_by','1')->sum('amount');
            

            $data[] = $arr;
        // }
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

        $table = 'company_'.$request->company_id.'_deposits';
        Deposit::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_payment_options';
        PaymentOption::setGlobalTable($table);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);

        // $paymentOptions = InvoiceTable::whereHas('payment_options')->pluck('payment_option')->toArray();
        // $paymentOption = InvoiceTable::whereIn('id',$paymentOptions)->get();

        // $purchasePaymentOption_ids = PurchaseTable::whereHas('payment_options')->pluck('payment_option')->toArray();
        // $purchasePaymentOptions = PurchaseTable::whereIn('id',$purchasePaymentOption_ids)->get();
            $paymentOption = InvoiceTable::get();
            $purchasePaymentOptions = PurchaseTable::where('reference','PINV')->get();


            $arr = [];
            $data = [];

            foreach($paymentOption as $invoiceData){
                $arr['date'] = $invoiceData->date;
                $arr['type'] = $invoiceData->reference_type;
                $arr['reference'] = $invoiceData->reference.''.$invoiceData->reference_number;
                $arr['client'] = $invoiceData->client_name;
                $arr['employee'] = \Auth::user()->name;
                $arr['payment_option'] = $invoiceData->payment_option_name;
                $arr['amount'] = $invoiceData->amount;
                $arr['paid'] = $invoiceData->set_as_paid;
                $data[] = $arr;
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

        $table = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($table);

        $table = 'company_'.$request->company_id.'_supplier_special_prices';
        SupplierSpecialPrice::setGlobalTable($table);

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
    public function stockValuationHistory(Request $request){
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
        
        $supplierSpecialPrices = SupplierSpecialPrice::get();
        // dd($supplierSpecialPrices);

        $referenceType = Reference::where('type', $request->type)->pluck('prefix')->toArray();
           
        $arr = [];
        $data = [];

        foreach($supplierSpecialPrices as $supplierSpecialPrice){
            $arr['reference'] = 'PRO00001';
            $arr['name'] = $supplierSpecialPrice->product_name;
            $arr['stock'] = '3.00';
            $arr['sales_stock_value'] = '300.00';
            $arr['purchase_stock_value'] = '300.00';

            $data[] = $arr;
        }
        return response()->json([
            "status" => true,
            "data" => $data
        ]);
        
    }
    public function ofEvolution(Request $request){
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
        if($request->type == "ofProfit"){
        $data = [
            "Of_profit" => [
                [
                    "type" => "bar", 
                    "label" => "Sales", 
                    "backgroundColor" => "#26C184", 
                    "data" => [
                        InvoiceTable::get()->sum('amount_with_out_vat'),
                    ]
                ],
                [
                    "type" => "bar", 
                    "label" => "Expenses", 
                    "backgroundColor" => "#26C184", 
                    "data" => [
                        PurchaseTable::where('reference','PINV')->get()->sum('amount_with_out_vat'),
                    ]
                ],
                [
                    "type" => "bar", 
                    "label" => "Profit", 
                    "backgroundColor" => "#26C184", 
                    "data" => [
                        InvoiceTable::get()->sum('amount_with_out_vat') - PurchaseTable::where('reference','PINV')->get()->sum('amount_with_out_vat'),
                    ]
                ]
            ]
        ];
    }elseif($request->type == "ofProfitList"){
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

        // $referenceType = Reference::whereIn('type', ['Purchase Invoice'])->pluck('prefix')->toArray();
        // $itemProductIds = Item::with('supplier')->whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
        // $itemServiceIds = Item::with('supplier')->whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
        // $expenseInvestmentIds = Item::with('supplier')->whereIn('type',$referenceType)->whereIn('reference',['EAI'])->pluck('reference_id')->toArray();
        // $products = Product::whereIn('id',$itemProductIds)->get();
        // $services = Service::whereIn('id',$itemServiceIds)->get();
        // $expenses = ExpenseAndInvestment::whereIn('id',$expenseInvestmentIds)->get();

        $invoiceAmount = InvoiceTable::get()->sum('amount_with_out_vat');
        $purchaseAmount = PurchaseTable::where('reference','PINV')->get()->sum('amount_with_out_vat');

            $arr = [];
            $data = [];

            // foreach($products as $invoiceData){
                $arr['period'] = '2023Q1';
                $arr['sales'] = $invoiceAmount;
                $arr['expense'] = $purchaseAmount;
                $arr['profit'] =  $invoiceAmount - $purchaseAmount;

                $data[] = $arr;
            // }
    }elseif($request->type == "invoicing_by_client"){
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
            
        $client_ids = InvoiceTable::with('client')->pluck('client_id')->toArray();
        $clients = Client::whereIn('id',$client_ids)->get();
        $data = [];
        $data['invoice_client'] = [];
        foreach($clients as $client){
            $data['invoice_client'][] = [
                    "type" => "bar",
                    "label" => "" .  $client->legal_name.' ('.$client->name.')',
                    "backgroundColor" => "#26C184",
                    "data" => [
                         InvoiceTable::filter($request->all())->where('client_id',$client->id)->get()->sum('amount'),
                        ]
                    ];
        }


    }elseif($request->type == "invoicing_by_client_list"){
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

            $client_ids = InvoiceTable::with('client')->pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$client_ids)->get();

            // $referenceType = Reference::where('type', $request->type)->pluck('prefix')->toArray();
            $arr = [];
            $data = [];

            foreach($clients as $client){
                $arr['name'] = $client->legal_name.' ('.$client->name.')';
                $arr['Q1'] = InvoiceTable::filter($request->all())->where('client_id',$client->id)->get()->sum('amount');
                $arr['Q2'] = '0.00';
                $arr['Q3'] = '0.00';
                $arr['Q4'] = '0.00';
                $arr['tottal'] = InvoiceTable::filter($request->all())->where('client_id',$client->id)->get()->sum('amount');
                $data[] = $arr;
            }
    }elseif($request->type == "invoicing_by_agent"){
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

            $data = [];
            $data['invoice_agents'] = [];
            $data['invoice_agents'][] = [
                    "type" => "bar",
                    "label" => "" .  \Auth::user()->name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                         InvoiceTable::filter($request->all())->get()->sum('amount'),
                        ]
                    ];
    }elseif($request->type == "invoicing_by_agent_list"){
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
            // $clients = Client::get();
                $arr = [];
                $data = [];

                $arr['name'] = \Auth::user()->name;
                $arr['Q1'] = InvoiceTable::filter($request->all())->where('agent_id',\Auth::id())->get()->sum('amount');
                $arr['Q2'] = '0.00';
                $arr['Q3'] = '0.00';
                $arr['Q4'] = '0.00';
                $arr['total'] = InvoiceTable::filter($request->all())->where('agent_id',\Auth::id())->get()->sum('amount');

                $data[] = $arr;
    }elseif($request->type == "invoicing_by_item"){
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

        $referenceType = Reference::whereIn('type', ['Normal Invoice', 'Refund Invoice'])->pluck('prefix')->toArray();
        $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
        $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
        $products = Product::whereIn('id',$itemProductIds)->get();
        $services = Service::whereIn('id',$itemServiceIds)->get();
        $data = [];
        $data['invoice_items'] = [];
        foreach($products as $product){
            $data['invoice_items'][] = [
                    "type" => "bar",
                    "label" => "" .  $product->name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                            InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                            $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
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
                            InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                            $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                            })->get()->sum('amount'),
                        ]
                    ];
        }
    }elseif($request->type == "invoicing_by_item_list"){
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

        $referenceType = Reference::whereIn('type', ['Normal Invoice', 'Refund Invoice'])->pluck('prefix')->toArray();
        $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
        $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
        $products = Product::whereIn('id',$itemProductIds)->get();
        $services = Service::whereIn('id',$itemServiceIds)->get();
           
            $arr = [];
            $data = [];

            foreach($products as $product){
                $arr['name'] = $product->name;
                $arr['reference'] = $product->reference.''.$product->reference_number;
                $arr['Q1'] = InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                })->get()->sum('amount_with_out_vat');
                $arr['Q2'] = '0.00';
                $arr['Q3'] = '0.00';
                $arr['Q4'] = '0.00';
                $arr['total'] = InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                })->get()->sum('amount_with_out_vat');
                

                $data[] = $arr;
            }
            foreach($services as $service){
                $arr['name'] = $service->name;
                $arr['reference'] = $service->reference.''.$service->reference_number;
                $arr['Q1'] = InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                })->get()->sum('amount_with_out_vat');
                $arr['Q2'] = '0.00';
                $arr['Q3'] = '0.00';
                $arr['Q4'] = '0.00';
                $arr['total'] = InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                })->get()->sum('amount_with_out_vat');

                $data[] = $arr;
            }
    }elseif($request->type == "purchase_by_provider"){
        $purchaseTables = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($purchaseTables); 

        $supplierTables = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($supplierTables); 
        
        $table = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($table);

        $productTables = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($productTables);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $table = 'company_'.$request->company_id.'_expense_and_investments';
        ExpenseAndInvestment::setGlobalTable($table);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $referenceType = Reference::whereIn('type', ['Purchase Invoice'])->pluck('prefix')->toArray();
        $supplier_ids = PurchaseTable::with('supplier')->whereIn('reference',$referenceType)->pluck('supplier_id')->toArray();
        $suppliers = Supplier::whereIn('id',$supplier_ids)->get();
            $data = [];
            $data['purchase_supplier'] = [];
            foreach($suppliers as $supplier){
                $data['purchase_supplier'][] = [
                        "type" => "bar",
                        "label" => "" .  $supplier->legal_name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                             PurchaseTable::filter($request->all())->where('reference','PINV')->where('supplier_id',$supplier->id)->get()->sum('amount'),
                            ]
                        ];
            }
    }elseif($request->type == "purchase_by_provider_list"){

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
        
        $referenceType = Reference::whereIn('type', ['Purchase Invoice'])->pluck('prefix')->toArray();
        $supplier_ids = PurchaseTable::with('supplier')->whereIn('reference',$referenceType)->pluck('supplier_id')->toArray();
        $suppliers = Supplier::whereIn('id',$supplier_ids)->get();

            // $referenceType = Reference::where('type', $request->type)->pluck('prefix')->toArray();
            $arr = [];
            $data = [];

            foreach($suppliers as $supplier){
                $arr['name'] = $supplier->legal_name.' ('.$supplier->name.')';
                $arr['Q1'] = PurchaseTable::filter($request->all())->where('supplier_id',$supplier->id)->get()->sum('amount');
                $arr['Q2'] = '0.00';
                $arr['Q3'] = '0.00';
                $arr['Q4'] = '0.00';
                $arr['total'] = PurchaseTable::filter($request->all())->where('supplier_id',$supplier->id)->get()->sum('amount');
                $data[] = $arr;
            }
    }elseif($request->type == "purchase_by_item"){
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

        $table = 'company_'.$request->company_id.'_expense_and_investments';
        ExpenseAndInvestment::setGlobalTable($table);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);

        $referenceType = Reference::whereIn('type', ['Purchase Invoice'])->pluck('prefix')->toArray();
        $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
        $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
        $expenseInvestmentIds = Item::whereIn('type',$referenceType)->whereIn('reference',['EAI'])->pluck('reference_id')->toArray();
        $products = Product::whereIn('id',$itemProductIds)->get();
        $services = Service::whereIn('id',$itemServiceIds)->get();
        $expenses = ExpenseAndInvestment::whereIn('id',$expenseInvestmentIds)->get();
         $data = [];
         $data['purchase_items'] = [];
         foreach($products as $product){
             $data['purchase_items'][] = [
                     "type" => "bar",
                     "label" => "" .  $product->name,
                     "backgroundColor" => "#26C184",
                     "data" => [
                         PurchaseTable::filter($request->all())->where('reference','PINV')->WhereHas('items', function ($query) use ($product) {
                             $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                             })->get()->sum('amount'),
                         ]
                     ];
         }
         foreach($services as $service){
             $data['purchase_items'][] = [
                     "type" => "bar",
                     "label" => "" .  $service->name,
                     "backgroundColor" => "#26C184",
                     "data" => [
                         PurchaseTable::filter($request->all())->where('reference','PINV')->WhereHas('items', function ($query) use ($service) {
                             $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                             })->get()->sum('amount'),
                         ]
                     ];
         }
         foreach($expenses as $expense){
             $data['purchase_items'][] = [
                     "type" => "bar",
                     "label" => "" .  $expense->name,
                     "backgroundColor" => "#26C184",
                     "data" => [
                         PurchaseTable::filter($request->all())->where('reference','PINV')->WhereHas('items', function ($query) use ($expense) {
                             $query->where('reference_id', $expense->id)->whereIn('reference',['EAI']);
                             })->get()->sum('amount'),
                         ]
                     ];
         }
         return response()->json([
             "status" => true,
             "data" =>  $data
         ]);
    }elseif($request->type == "purchase_by_item_list"){
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
        
        $referenceType = Reference::whereIn('type', ['Purchase Invoice'])->pluck('prefix')->toArray();
        $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
        $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
        $expenseInvestmentIds = Item::whereIn('type',$referenceType)->whereIn('reference',['EAI'])->pluck('reference_id')->toArray();
        $products = Product::whereIn('id',$itemProductIds)->get();
        $services = Service::whereIn('id',$itemServiceIds)->get();
        $expenses = ExpenseAndInvestment::whereIn('id',$expenseInvestmentIds)->get();
           
            $arr = [];
            $data = [];

            foreach($products as $product){
                $arr['name'] = $product->name;
                $arr['reference'] = $product->reference.''.$product->reference_number;
                $arr['Q1'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                })->get()->sum('amount_with_out_vat');
                $arr['Q2'] = '0.00';
                $arr['Q3'] = '0.00';
                $arr['Q4'] = '0.00';
                $arr['total'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($product) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                })->get()->sum('amount_with_out_vat');
                

                $data[] = $arr;
            }
            foreach($services as $service){
                $arr['name'] = $service->name;
                $arr['reference'] = $service->reference.''.$service->reference_number;
                $arr['Q1'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                })->get()->sum('amount_with_out_vat');
                $arr['Q2'] = '0.00';
                $arr['Q3'] = '0.00';
                $arr['Q4'] = '0.00';
                $arr['total'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($service) {
                    $query->where('reference_id', $service->id)->whereIn('reference',['SER']);
                })->get()->sum('amount_with_out_vat');

                $data[] = $arr;
            }
            foreach($expenses as $expense){
                $arr['name'] = $expense->name;
                $arr['reference'] = $expense->reference.''.$expense->reference_number;
                $arr['Q1'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($expense) {
                    $query->where('reference_id', $expense->id)->whereIn('reference',['EAI']);
                })->get()->sum('amount_with_out_vat');
                $arr['Q2'] = '0.00';
                $arr['Q3'] = '0.00';
                $arr['Q4'] = '0.00';
                $arr['total'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($expense) {
                    $query->where('reference_id', $expense->id)->whereIn('reference',['EAI']);
                })->get()->sum('amount_with_out_vat');
                

                $data[] = $arr;
            }

    }
        
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

        $taxes = 'company_'.$request->company_id.'_consumption_taxes';
        ConsumptionTax::setGlobalTable($taxes);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $data = [];
        
        if($request->type == 'taxes'){
            
        }

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
        
        $taxes = ConsumptionTax::get();
        foreach($taxes as $key => $consumptionTax){
            $data = $consumptionTax->tax;
            
        }
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);

    }
}
