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

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $productTables = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($productTables);

        
        if($request->type == "agent"){
            // $agent_ids  = InvoiceTable::pluck('agent_id')->groupBy('agent_id')->toArray();        
            // $clients = Client::whereIn('id', $agent_ids)->get();
            $clients = Client::get();
            $data = [];
            $data['sales_invoicing'] = [];
            foreach($clients as $client){
                $data['sales_invoicing'][] = [
                        "type" => "bar",
                        "label" => "" .  $client->email,
                        "backgroundColor" => "#26C184",
                        "data" => [
                            Client::where('id', $client->id)->get()->sum('amount'),
                            ]
                        ];
            }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);
        }else if($request->type == "product"){
            $productTables = Product::get();
            $data = [];
            $data['products'] = [];
            foreach($productTables as $productTable){
                $data['products'][] = [
                        "type" => "bar",
                        "label" => "" .  $productTable->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                            Product::where('id', $productTable->id)->get()->sum('amount'),
                            ]
                        ];
            }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);

        }else{
            $items = Item::get();
            $data = [];
            $data['Items'] = [];
            foreach($items as $item){
                $data ['Items'] [] = [
                    "type" => "bar", 
                    "label" => "" . $item->name,
                    "backgroundColor" => "#26C184", 
                    "data" => [
                        " " . Item::where('id', $item->id)->get()->sum('amount'),
                    ]
                ];
            }
            
            return response()->json([
                "status" => true,
                "data" =>  $data
            ]);
        }
        
       
        
        
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
                            "$". SalesEstimate::where('reference',$referenceType)->get()->sum('amount'),
                            ]
                        ];
            }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);

        }elseif($request->type == "agents"){
            $agentNames = User::get();
            $data = [];
            $data['agents'] = [];
            foreach($agentNames as $agentName){
            $data = [
                    "agents" => [
                        [
                            "type" => "bar", 
                            "label" => "" . $agentName->name, 
                            "backgroundColor" => "#26C184", 
                            "data" => [
                            " " . SalesEstimate::where('reference', $referenceType)->get()->sum('amount'),
                            ] 
                        ], 
                    ]
                ];
        }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);
        }elseif($request->type == "items"){
            $productTables = Product::get();
            $data = [];
            $data['products'] = [];
            foreach($productTables as $productTable){
                $data['products'][] = [
                        "type" => "bar",
                        "label" => "" .  $productTable->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                            SalesEstimate::with('items')->where('reference', $referenceType)->get()->sum('amount'),
                            //  SalesEstimate::with(['items'])->WhereHas('items', function ($query) use ($request) {
                            //     $query->where('reference_id', $request->id)->where('parent_id',   $request->parent_id);
                            // })->get()->sum('amount'),
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

        // if($request->type == "clientHistory" ){

        if($request->type){

            $referenceType = Reference::where('type', urldecode($request->type))->pluck('prefix')->toArray();
            $clients = SalesEstimate::where('reference', $referenceType)->get();
            $arr = [];
            $data = [];

            foreach($clients as $client){
                $arr['name'] = $client->client_name;
                $arr['pending'] = $client->where('reference', $referenceType)->where('status','pending')->count();
                $arr['refused'] = $client->where('reference', $referenceType)->where('status','refused')->count();
                $arr['accepted'] = $client->where('reference', $referenceType)->where('status','accepted')->count();
                $arr['closed'] = $client->where('reference', $referenceType)->where('status','closed')->count();
                $arr['amount'] = $client->amount;

                $data[] = $arr;
            }
            
        }
            // $arr = [];
            // $data = [];
            // $data['clientHistory'] = [];
            // foreach($querys as $client){

                // $arr['name'] = $client->client_name;

                // $data[] = $arr;

                // $data = [
                //     "clientHistory" => [
                //         [
                //             "client_name" => "" . $client->legal_name, 
                //             "data" => [
                //             " " . SalesEstimate::where('reference', $referenceType)->get()->sum('amount'),
                //             ] 
                //         ],
                //         [
                //             "label" => "total(".SalesEstimate::where('reference', $referenceType)
                //             ->where('status', 'pending')->where('status', 'refused')->where('status', 'accepted')->where('status', 'closed')
                //             ->count().")", 
                //         ],
                //         [
 
                //             "label" => "Pending(".SalesEstimate::where('reference', $referenceType)->where('status', 'pending')->count().")",
                //             "data" => [
                //                 " ".SalesEstimate::where('reference', $referenceType)->where('status', 'pending')->get()->sum('amount'),
                //             ] 
                //         ],
                //         [
 
                //             "label" => "Refused(". SalesEstimate::where('reference', $referenceType)->where('status', 'refused')->count().")",  
                //             "data" => [
                //                 " " . SalesEstimate::where('reference', $referenceType)->where('status', 'refused')->get()->sum('amount'),
                //                 ] 
                //         ], 
                //     [ 
                //         "label" => "Accepted(". SalesEstimate::where('reference', $referenceType)->where('status', 'accepted')->count().")", 
                //         "data" => [
                //             " " . SalesEstimate::where('reference', $referenceType)->where('status', 'accepted')->get()->sum('amount'),
                //         ] 
                //     ],
                //     [ 
                //         "label" => "Closed(". SalesEstimate::where('reference', $referenceType)->where('status', 'closed')->count().")", 
                //         "data" => [
                //             "" . SalesEstimate::where('reference', $referenceType)->where('status', 'closed')->get()->sum('amount'),
                //         ] 
                //     ],
                //     ],
                // ];
            // }
            return response()->json([
                "status" => true,
                "data" =>  $data
            ]);
        // }
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

        if($request->type){

            $referenceType = Reference::where('type', urldecode($request->type))->pluck('prefix')->toArray();
            $clients = SalesEstimate::where('reference', $referenceType)->get();
            $arr = [];
            $data = [];

            foreach($clients as $client){
                $arr['legal_name'] = $client->agent_name;
                $arr['pending'] = $client->where('reference', $referenceType)->where('status','pending')->count();
                $arr['refused'] = $client->where('reference', $referenceType)->where('status','refused')->count();
                $arr['accepted'] = $client->where('reference', $referenceType)->where('status','accepted')->count();
                $arr['closed'] = $client->where('reference', $referenceType)->where('status','closed')->count();
                $arr['total'] = $client->where('reference', $referenceType)->where('status','closed')->count();
                $arr['amount'] = $client->amount;

                $data[] = $arr;
            }
            
        }
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);

        // if($request->type == "agentsHistory" ){
        //     $agentNames = User::get();
        //     $data = [];
        //     $data['agentsHistory'] = [];
        //     foreach($agentNames as $agentName){
        //         $data = [
        //             "agentsHistory" => [
        //                 [
        //                     "agent_name" => "" . $agentName->name, 
        //                 ],
        //                 [
        //                     "total_amount" => [
        //                     " " . SalesEstimate::where('reference', $referenceType)->get()->sum('amount_with_out_vat'),
        //                     ] 
        //                 ],
        //                 [
        //                     "label" => "total(".SalesEstimate::where('reference', $referenceType)->where('client_id', $request->client_id)
        //                     ->where('status', 'pending')->where('status', 'refused')->where('status', 'accepted')->where('status', 'closed')
        //                     ->count().")", 
        //                 ],
        //                 [
        //                     "label" => "Pending(".SalesEstimate::where('reference', $referenceType)->where('client_id', $request->client_id)->where('status', 'pending')->count().")",
        //                 ],
        //                 [
        //                     "label" => "Refused(". SalesEstimate::where('reference', $referenceType)->where('client_id', $request->client_id)->where('status', 'refused')->count().")",  

        //                 ], 
        //                 [ 
        //                     "label" => "Accepted(". SalesEstimate::where('reference', $referenceType)->where('client_id', $request->client_id)->where('status', 'accepted')->count().")", 
        //                 ],
        //                 [ 
        //                     "label" => "Closed(". SalesEstimate::where('reference', $referenceType)->where('client_id', $request->client_id)->where('status', 'closed')->count().")", 
        //                 ],
        //             ]
        //         ];
        //     }
        //     return response()->json([
        //         "status" => true,
        //         "data" =>  $data
        //     ]);
        // }
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

            $referenceType = Reference::where('type', urldecode($request->type))->pluck('prefix')->toArray();
            // $clients = SalesEstimate::where('reference', $referenceType)->get();
            $clients = SalesEstimate::with(['items'])->where('reference', $referenceType)->WhereHas('items', function ($query) use ($request) {
                $query->where('reference_id', $request->id)->where('reference',   $request->reference);
            })->get();
            $arr = [];
            $data = [];

            foreach($clients as $client){
                $arr['name'] = $client->agent_name;
                $arr['pending'] = $client->where('reference', $referenceType)->where('status','pending')->count();
                $arr['refused'] = $client->where('reference', $referenceType)->where('status','refused')->count();
                $arr['accepted'] = $client->where('reference', $referenceType)->where('status','accepted')->count();
                $arr['closed'] = $client->where('reference', $referenceType)->where('status','closed')->count();
                $arr['total'] = $client->where('reference', $referenceType)->sum('status');
                $arr['amount'] = $client->amount;

                $data[] = $arr;
            }

        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);

        // if($request->type == "itemsHistory" ){
        //     $products = Product::get();
        //     $data = [];
        //     $data['itemsHistory'] = [];
        //     foreach($products as $product){
        //         $data = [
        //             "itemsHistory" => [
        //                 [
        //                     "label" => "" . $product->name, 
        //                 ],
        //                 [
        //                     "reference" => "" . $product->reference_number, 
        //                 ],
        //                 [

        //                     "label" => "total(".SalesEstimate::where('reference', $referenceType)
        //                     ->where('status', 'pending')->where('status', 'refused')->where('status', 'accepted')->where('status', 'closed')
        //                     ->count().")", 
        //                 ],
        //                 [
 
        //                     "label" => "Pending(".SalesEstimate::where('reference', $referenceType)->where('status', 'pending')->count().")",
        //                     "data" => [
        //                         " ".SalesEstimate::where('reference', $referenceType)->where('status', 'pending')->get()->sum('amount'),
        //                     ] 
        //                 ],
        //                 [
 
        //                     "label" => "Refused(". SalesEstimate::where('reference', $referenceType)->where('status', 'refused')->count().")",  
        //                     "data" => [
        //                         " " . SalesEstimate::where('reference', $referenceType)->where('status', 'refused')->get()->sum('amount'),
        //                         ] 
        //                 ], 
        //                 [ 
        //                     "label" => "Accepted(". SalesEstimate::where('reference', $referenceType)->where('status', 'accepted')->count().")", 
        //                     "data" => [
        //                         " " . SalesEstimate::where('reference', $referenceType)->where('status', 'accepted')->get()->sum('amount'),
        //                     ] 
        //                 ],
        //                 [ 
        //                     "label" => "Closed(". SalesEstimate::where('reference', $referenceType)->where('status', 'closed')->count().")", 
        //                     "data" => [
        //                         "" . SalesEstimate::where('reference', $referenceType)->where('status', 'closed')->get()->sum('amount'),
        //                     ] 
        //                 ],
        //             ],
        //         ];
        //     }
        //     return response()->json([
        //         "status" => true,
        //         "data" =>  $data
        //     ]);
        // }
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

        $supplierTables = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($supplierTables); 

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

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
