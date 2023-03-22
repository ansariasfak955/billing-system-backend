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
use App\Models\ClientCategory;
use App\Models\ProductCategory;
use App\Models\IncomeTax;
use App\Models\PurchaseTicket;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    /* -------------------------------------------------------------------------- */
    /*                               Overview Report                              */
    /* -------------------------------------------------------------------------- */
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
        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);
        $client_id = Client::pluck('id')->toArray();
        $purchaseReferenceTypes = Reference::where('type', 'Purchase Invoice')->pluck('prefix')->toArray();
        $invoiceReferenceTypes = Reference::where('type', 'Normal Invoice')->pluck('prefix')->toArray();
        $data = [
            "profit" => [
                [
                    "type" => "bar", 
                    "label" => "Sales", 
                    "backgroundColor" => "#26C184", 
                    "data" => [
                         Item::filter($request->all())->whereIn('type', $invoiceReferenceTypes)->sum('subtotal')
                    ] 
                ], 
                [
                        "type" => "bar", 
                        "label" => "Expenses", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                             InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferenceTypes)->where('paid', '1')->sum('amount')
                        ]  
                ], 
                [
                    "type" => "bar", 
                    "label" => "Profit", 
                    "backgroundColor" => "#FE9140", 
                    "data" => [
                         Item::filter($request->all())->whereIn('type', $invoiceReferenceTypes)->sum('subtotal') - InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferenceTypes)->where('paid', '1')->sum('amount')
                    ] 
                ] 
            ], 
            "sales_invoicing" => [
                [
                    "type" => "bar", 
                    "label" => "Invoiced", 
                    "backgroundColor" => "#26C184", 
                    "data" => [
                         InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferenceTypes)->whereDate('expiration_date', '>', date('Y-m-d'))->sum('amount')
                    ] 
                ], 
                [
                    "type" => "bar", 
                    "label" => "Paid", 
                    "backgroundColor" => "#FB6363", 
                    "data" => [
                         InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferenceTypes)->where('paid', '1')->sum('amount')
                    ]  
                ], 
                [
                    "type" => "bar", 
                    "label" => "Unpaid", 
                    "backgroundColor" => "#FE9140", 
                    "data" => [
                         InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferenceTypes)->where('paid', '0')->sum('amount')
                    ] 
                ] 
            ], 
            "purchase_invoicing" => [
                [
                    "type" => "bar", 
                    "label" => "Invoiced", 
                    "backgroundColor" => "#26C184", 
                    "data" => [
                         PurchaseReceipt::filter($request->all())->whereIn('type', $purchaseReferenceTypes)->whereDate('expiration_date', '>', date('Y-m-d'))->sum('amount')
                    ] 
                ], 
                [
                    "type" => "bar", 
                    "label" => "Paid", 
                    "backgroundColor" => "#FB6363", 
                    "data" => [
                         PurchaseReceipt::filter($request->all())->whereIn('type', $purchaseReferenceTypes)->where('paid', '1')->sum('amount')
                    ] 
                ], 
                [
                    "type" => "bar", 
                    "label" => "Unpaid", 
                    "backgroundColor" => "#FE9140", 
                    "data" => [
                         PurchaseReceipt::filter($request->all())->whereIn('type', $purchaseReferenceTypes)->where('paid', '0')->sum('amount')
                    ] 
                ] 
            ],
            "expense_distribution" => 
            [
                [
                    "type" => "pie", 
                    "label" => "Transporte", 
                    "backgroundColor" => "#26C184", 
                    "data" => [
                        "60" 
                    ] 
                ],
                [
                    "type" => "pie", 
                    "label" => "Seguros", 
                    "backgroundColor" => "#26C184", 
                    "data" => [
                        "20" 
                    ] 
                ],
                [
                    "type" => "pie", 
                    "label" => "Service purchases", 
                    "backgroundColor" => "#26C184", 
                    "data" => [
                        "30" 
                    ] 
                ],
                [
                    "type" => "pie", 
                    "label" => "Product purchases", 
                    "backgroundColor" => "#26C184", 
                    "data" => [
                        "30" 
                    ] 
                ]

            ]  
        ];  
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
    }

    /* -------------------------------------------------------------------------- */
    /*                              Invoicing Report                              */
    /* -------------------------------------------------------------------------- */
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

        $table = 'company_'.$request->company_id.'_client_categories';
        ClientCategory::setGlobalTable($table);
        $productCategorytable = 'company_'.$request->company_id.'_product_categories';
        ProductCategory::setGlobalTable($productCategorytable);

        if($request->after_tax){
            $taxColumn = 'amount';
        }else{
            $taxColumn = 'amount_with_out_vat';
        }

        $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
        if($request->type == "clients"){
            $data = [];
            $data['invoice_client'] = [];
            if($request->category == 'client_categories'){
                $categories = ClientCategory::get();
                //    return $categories;
                $arr = [];
                $data = [];
                $request['clientCategoryNull'] = 1;
                $data['invoice_client'][] = [
                    "type" => "bar",
                    "label" => "No Selected Category",
                    "backgroundColor" => "#26C184",
                    "data" => [
                            InvoiceTable::filter($request->all())->where('reference', $referenceType)->get()->sum($taxColumn),
                        ]
                    ];
                unset($request['clientCategoryNull']);
                foreach($categories as $category){
                    $request['clientCategory'] = $category->id;
                    $data['invoice_client'][] = [
                        "type" => "bar",
                        "label" => "" .  $category->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                                InvoiceTable::filter($request->all())->where('reference', $referenceType)->get()->sum($taxColumn),
                            ]
                        ];
                }
            }else{
                $client_ids = InvoiceTable::whereHas('client')->pluck('client_id')->toArray();
                $clients = Client::whereIn('id',$client_ids)->get();

                foreach($clients as $client){
                    $data['invoice_client'][] = [
                    "type" => "bar",
                    "label" => "" .  $client->legal_name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                            InvoiceTable::filter($request->all())->where('client_id',$client->id)->where('reference', $referenceType)->get()->sum($taxColumn),
                        ]
                    ];
                }
            }           
            return response()->json([
                "status" => true,
                "data" => $data
            ]);
        }else if($request->type == "agents"){
            // $clients = Client::get();
            $data = [];
            $data['invoice_agents'] = [];
                $data['invoice_agents'][] = [
                "type" => "bar",
                "label" => "" .  \Auth::user()->name,
                "backgroundColor" => "#26C184",
                "data" => [
                        InvoiceTable::filter($request->all())->where('reference',$referenceType)->get()->sum($taxColumn),
                    ]
                ];
            return response()->json([
                "status" => true,
                "data" => $data
            ]);

        }else if($request->type == "items"){
            $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            if($request->client_id){
                $invoiceIds = InvoiceTable::where('client_id',$request->client_id)->whereIn('reference',$referenceType)->pluck('id')->toArray();
                $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->whereIn('parent_id',$invoiceIds)->pluck('reference_id')->toArray();
                $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->whereIn('parent_id',$invoiceIds)->pluck('reference_id')->toArray();
            }else{
                $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
                $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
            }

            $products = Product::filter($request->all())->whereIn('id',$itemProductIds)->get();
            $services = Service::filter($request->all())->whereIn('id',$itemServiceIds)->get();

            $data = [];
            $data['invoice_items'] = [];
            if($request->category == 'catalog'){

                foreach($products as $product){
                    $request['product_id'] = $product->id;
                    $data['invoice_items'][] = [
                        "type" => "bar",
                        "label" => "" .  $product->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                                InvoiceTable::filter($request->all())->get()->sum('amount'),
                            ]
                    ];
                }
                foreach($services as $service){
                    $request['service_id'] = $service->id;
                    $data['invoice_items'][] = [
                        "type" => "bar",
                        "label" => "" .  $service->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                                InvoiceTable::filter($request->all())->get()->sum('amount'),
                            ]
                    ];
                }
            }else{
                $categories = ProductCategory::get();
                //    return $categories;
                $arr = [];
                $data = [];
                $request['productCategoryNull'] = 1;
                $data['invoice_items'][] = [
                    "type" => "bar",
                    "label" => "Not found in catalog
                    ",
                    "backgroundColor" => "#26C184",
                    "data" => [
                            InvoiceTable::filter($request->all())->where('reference', $referenceType)->get()->sum($taxColumn),
                        ]
                    ];
                unset($request['productCategoryNull']);
                foreach($categories as $category){
                    $request['productCategory'] = $category->id;
                    $data['invoice_items'][] = [
                        "type" => "bar",
                        "label" => "" .  $category->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                            InvoiceTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($taxColumn),
                            ]
                        ];
                    }
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

        $table = 'company_'.$request->company_id.'_client_categories';
        ClientCategory::setGlobalTable($table);

        if($request->after_tax){
            $column = 'amount';
        }
        else{
            $column = 'amount_with_out_vat';
        }

            $client_ids = InvoiceTable::pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$client_ids)->get();
            $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $data = [];
            //no category history
            $request['clientCategoryNull'] = 1;
            $arr['name'] = 'No selected category';
            $arr['invoiced'] = InvoiceTable::filter($request->all())->where('reference',$referenceType)->get()->sum($column);
            $arr['paid'] = InvoiceTable::filter($request->all())->where('reference',$referenceType)->get()->sum('amount_paid');
            $arr['Unpaid'] = InvoiceTable::filter($request->all())->where('reference',$referenceType)->get()->sum('amount_due');
            $data[] = $arr;
            unset($request['clientCategoryNull']);

            if($request->category == 'client_categories'){
                $arr = [];
                // $clientCategory = Client::pluck('client_category')->toArray();
                $categories = ClientCategory::get();
                foreach($categories as $category){
                    $request['clientCategory'] = $category->id;
                    $arr['name'] = $category->name;
                    $arr['invoiced'] = InvoiceTable::filter($request->all())->where('reference',$referenceType)->get()->sum($column);
                    $arr['paid'] = InvoiceTable::filter($request->all())->where('reference',$referenceType)->get()->sum('amount_paid');
                    $arr['Unpaid'] = InvoiceTable::filter($request->all())->where('reference',$referenceType)->get()->sum('amount_due');
                    $data[] = $arr;
                }
            }else{
                foreach($clients as $client){
                    $arr['name'] = $client->legal_name;
                    $arr['invoiced'] = InvoiceTable::filter($request->all())->where('client_id',$client->id)->where('reference',$referenceType)->get()->sum($column);
                    $arr['paid'] = InvoiceTable::filter($request->all())->where('client_id',$client->id)->where('reference',$referenceType)->get()->sum('amount_paid');
                    $arr['Unpaid'] = InvoiceTable::filter($request->all())->where('client_id',$client->id)->where('reference',$referenceType)->get()->sum('amount_due');
                    $data[] = $arr;
                }
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
        if($request->after_tax){
            $column = 'amount';
        }else{
            $column = 'amount_with_out_vat';
        }

        $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
        if($request->client_id){
            $invoiceIds = InvoiceTable::where('client_id',$request->client_id)->whereIn('reference',$referenceType)->pluck('id')->toArray();
            $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->whereIn('parent_id',$invoiceIds)->pluck('reference_id')->toArray();
            $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->whereIn('parent_id',$invoiceIds)->pluck('reference_id')->toArray();
        }else{
            $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
            $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
        }

        $products = Product::filter($request->all())->whereIn('id',$itemProductIds)->get();
        $services = Service::filter($request->all())->whereIn('id',$itemServiceIds)->get();

        $arr = [];
        $data = [];
        $arr['name'] = \Auth::user()->name;
        $arr['invoiced'] = InvoiceTable::filter($request->all())->where('agent_id',\Auth::id())->where('reference',$referenceType)->get()->sum($column);
        $arr['paid'] = InvoiceTable::filter($request->all())->where('agent_id',\Auth::id())->where('reference',$referenceType)->get()->sum('amount_paid');
        $arr['Unpaid'] = InvoiceTable::filter($request->all())->where('agent_id',\Auth::id())->where('reference',$referenceType)->get()->sum('amount_due');

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

        $product_category_table = 'company_'.$request->company_id.'_product_categories';
        ProductCategory::setGlobalTable($product_category_table);
        
        $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
        if($request->client_id){
            $invoiceIds = InvoiceTable::where('client_id',$request->client_id)->whereIn('reference',$referenceType)->pluck('id')->toArray();
            $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->whereIn('parent_id',$invoiceIds)->pluck('reference_id')->toArray();
            $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->whereIn('parent_id',$invoiceIds)->pluck('reference_id')->toArray();
        }else{
            $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
            $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
        }

        $products = Product::filter($request->all())->whereIn('id',$itemProductIds)->get();
        $services = Service::filter($request->all())->whereIn('id',$itemServiceIds)->get();
        
           
            $arr = [];
            $data = [];
        if($request->category == 'catalog'){

            foreach($products as $product){
                $request['product_id'] = $product->id;
                $arr['name'] = $product->name;
                $arr['reference'] = $product->reference.''.$product->reference_number;
                $units = Item::where('reference_id', $product->id)->whereIn('reference',['PRO'])->sum('quantity');
                $arr['units'] = $units;
                $arr['amount'] = InvoiceTable::filter($request->all())->get()->sum('amount_with_out_vat');
                

                $data[] = $arr;
            }
            foreach($services as $service){
                $request['service_id'] = $service->id;
                $arr['name'] = $service->name;
                $arr['reference'] = $service->reference.''.$service->reference_number;
                $units = Item::where('reference_id', $service->id)->whereIn('reference',['SER'])->sum('quantity');
                $arr['units'] = $units;
                $arr['amount'] = InvoiceTable::filter($request->all())->get()->sum('amount_with_out_vat');

                $data[] = $arr;
            }
        }else{

            $categories = ProductCategory::get();
            $request['productCategoryNull'] = 1;
            $arr['name'] = 'Not found in catalog';
            $arr['invoiced'] = InvoiceTable::filter($request->all())->get()->sum('amount');
            $arr['paid'] = InvoiceTable::filter($request->all())->get()->sum('amount_paid');
            $arr['Unpaid'] = InvoiceTable::filter($request->all())->get()->sum('amount_due');
            unset($request['productCategoryNull']);
            $data[] = $arr;
            foreach($categories as $category){
                $request['productCategory'] = $category->id;
                $arr['name'] = $category->name;
                $arr['invoiced'] = InvoiceTable::filter($request->all())->get()->sum('amount');
                $arr['paid'] = InvoiceTable::filter($request->all())->get()->sum('amount_paid');
                $arr['Unpaid'] = InvoiceTable::filter($request->all())->get()->sum('amount_due');
                $data[] = $arr;
            }
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
        $clientCategorytable = 'company_'.$request->company_id.'_client_categories';
        ClientCategory::setGlobalTable($clientCategorytable);
        $productCategorytable = 'company_'.$request->company_id.'_product_categories';
        ProductCategory::setGlobalTable($productCategorytable);
        $salesOrderreferenceType = Reference::where('type', 'Sales Order')->pluck('prefix')->toArray();
        $salesEstimatereferenceType = Reference::where('type', 'Sales Estimate')->pluck('prefix')->toArray();
        $salesDeliveryNotesreferenceType = Reference::where('type', 'Sales Delivery Note')->pluck('prefix')->toArray();
        // dd($referenceType);
        if($request->reference){
            $referenceType = [$request->reference];
        }else{

            $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
        }
        if($request->after_tax){
            $taxColumn = 'amount';
        }
        else{
            $taxColumn = 'amount_with_out_vat';
        }

        $data = [];
        if( $request->type == "overview" ){
            $data = [
                "estimates_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending(". SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'pending')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                         SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'pending')->get()->sum($taxColumn),
                        ] 
                    ], 
                    [
                            "type" => "bar", 
                            "label" => "Refused(". SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'refused')->count().")", 
                            "backgroundColor" => "#FB6363", 
                            "data" => [
                                 SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'refused')->get()->sum($taxColumn),
                                ] 
                        ], 
                    [
                        "type" => "bar", 
                        "label" => "Accepted(". SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'accepted')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                             SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'accepted')->get()->sum($taxColumn), 
                        ] 
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed(". SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'closed')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'closed')->get()->sum($taxColumn),
                        ] 
                    ],
            ], 
                "orders_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending (". SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->where('status', 'pending')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->where('status', 'pending')->get()->sum($taxColumn),
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Refused (". SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->where('status', 'refused')->count().")", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                             SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->where('status', 'refused')->get()->sum($taxColumn),
                        ]  
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "In Progress (". SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->where('status', 'in progress')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                             SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->where('status', 'in progress')->get()->sum($taxColumn),
                        ] 
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed (". SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->where('status', 'closed')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                             SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->where('status', 'closed')->get()->sum($taxColumn),
                        ] 
                    ],
                         
                ], 
                "delivery_notes_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending Invoice (". SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->where('status', 'pending invoice')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                             SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->where('status', 'pending invoice')->get()->sum($taxColumn),
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "In Progress (". SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->where('status', 'in progress')->count().")", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                             SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->where('status', 'in progress')->get()->sum($taxColumn),
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Closed (". SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->where('status', 'closed')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                             SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->where('status', 'closed')->get()->sum($taxColumn),
                        ] 
                        ],
                    [
                        "type" => "bar", 
                        "label" => "Invoiced (". SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->where('status', 'invoiced')->count().")",
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                             SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->where('status', 'invoiced')->get()->sum($taxColumn),
                        ] 
                    ]  
                ]
            ];

        }elseif( $request->type == "clients" ){
            $data = [];
            $data['sales_clients'] = [];
            if($request->category == 'client_categories'){

                $categories = ClientCategory::get();
                //    return $categories;
                $arr = [];
                $data = [];
                $request['clientCategoryNull'] = 1;
                $data['sales_clients'][] = [
                    "type" => "bar",
                    "label" => "No Selected Category",
                    "backgroundColor" => "#26C184",
                    "data" => [
                            SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($taxColumn),
                            ]
                ];
                unset($request['clientCategoryNull']);
                foreach($categories as $category){
                    $request['clientCategory'] = $category->id;
                    $data['sales_clients'][] = [
                        "type" => "bar",
                        "label" => "" .  $category->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                                SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($taxColumn),
                            ]
                        ];
                }
            }else{
                $client_ids = SalesEstimate::filter($request->all())->whereHas('client')->pluck('client_id')->toArray();
                $clients = Client::whereIn('id',$client_ids)->get();
                foreach($clients as $client){
                    $data['sales_clients'][] = [
                    "type" => "bar",
                    "label" => "" .  $client->legal_name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                            SalesEstimate::filter($request->all())->where('client_id',$client->id)->whereIn('reference',$referenceType)->get()->sum($taxColumn),
                        ]
                    ];
                }
            }
            return response()->json([
                "status" => true,
                "data" => $data
            ]);
            
        }elseif($request->type == "agents"){
            $data = [];
            $data['sales_agents'] = [];
            $data['sales_agents'][] = [
                "type" => "bar",
                "label" => "" .  \Auth::user()->name,
                "backgroundColor" => "#26C184",
                "data" => [
                    SalesEstimate::filter($request->all())->get()->sum($taxColumn),
                ]
            ];
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
            if($request->category == 'product_categories'){
                $categories = ProductCategory::get();
                $data = [];
                $request['productCategoryNull'] = 1;
                $data['sales_items'][] = [
                    "type" => "bar",
                    "label" => "Not found in catalog
                    ",
                    "backgroundColor" => "#26C184",
                    "data" => [
                             SalesEstimate::filter($request->all())->where('reference', $referenceType)->get()->sum($taxColumn),
                        ]
                    ];
                unset($request['productCategoryNull']);
                foreach($categories as $category){
                    $request['productCategory'] = $category->id;
                    $data['sales_items'][] = [
                        "type" => "bar",
                        "label" => "" .  $category->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                             SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($taxColumn),
                        ]
                    ];
                }
            }else{
                foreach($products as $product){
                    $request['product_id'] = $product->id;
                    $data['sales_items'][] = [
                    "type" => "bar",
                    "label" => "" .  $product->name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                            SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount'),
                        ]
                    ];
                }
                foreach($services as $service){
                    $request['service_id'] = $service->id;
                    $data['sales_items'][] = [
                    "type" => "bar",
                    "label" => "" .  $service->name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                            SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount'),
                        ]
                    ];
                }
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

        $clientCategorytable = 'company_'.$request->company_id.'_client_categories';
        ClientCategory::setGlobalTable($clientCategorytable);
        $productCategorytable = 'company_'.$request->company_id.'_product_categories';
        ProductCategory::setGlobalTable($productCategorytable);
    

        if($request->after_tax){
            $column = 'amount';
            }
        else{
            $column = 'amount_with_out_vat';
        }

        if($request->reference){
            $referenceType = [$request->reference];
        }else{
            $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
        }
        $arr = [];
        $data = [];

        if($request->category == 'client_categories'){
            $request['clientCategoryNull'] = 1;
            $arr['name'] = "No Selected Category";
            $arr['pending'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->where('status','pending')->count();
            $arr['refused'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->where('status','refused')->count();
            $arr['accepted'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->where('status','accepted')->count();
            $arr['closed'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->where('status','closed')->count();
            $arr['total'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->count();
            $arr['amount'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->get()->sum($column);

            $data[] = $arr;
            unset($request['clientCategoryNull']);
            $categories = ClientCategory::get();
            foreach($categories as $category){
                $request['clientCategory'] = $category->id;
                $arr['name'] = $category->name;
                $arr['pending'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->where('status','pending')->count();
                $arr['refused'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->where('status','refused')->count();
                $arr['accepted'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->where('status','accepted')->count();
                $arr['closed'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->where('status','closed')->count();
                $arr['total'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->count();
                $arr['amount'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->get()->sum($column);

                $data[] = $arr;
            }
        }else{
            $client_ids = SalesEstimate::whereHas('client')->where('reference',$referenceType)->pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$client_ids)->get();
            foreach($clients as $client){
                $arr['name'] = $client->legal_name;
                $arr['pending'] = SalesEstimate::filter($request->all())->where('client_id',$client->id)->where('reference',$referenceType)->where('status','pending')->count();
                $arr['refused'] = SalesEstimate::filter($request->all())->where('client_id',$client->id)->where('reference',$referenceType)->where('status','refused')->count();
                $arr['in_progress'] = SalesEstimate::filter($request->all())->where('client_id',$client->id)->where('reference',$referenceType)->where('status','in progress')->count();
                $arr['closed'] = SalesEstimate::filter($request->all())->where('client_id',$client->id)->where('reference',$referenceType)->where('status','closed')->count();
                $arr['total'] = SalesEstimate::filter($request->all())->where('client_id',$client->id)->where('reference',$referenceType)->count();
                $arr['amount'] = SalesEstimate::filter($request->all())->where('client_id',$client->id)->where('reference',$referenceType)->get()->sum($column);

                $data[] = $arr;
            }
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

        if($request->after_tax){
            $column = 'amount';
        }
        else{
            $column = 'amount_with_out_vat';
        
        }
        if($request->reference){
            $referenceType = [$request->reference];
        }else{
            $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
        }

        $arr = [];
        $data = [];

        $arr['name'] = \Auth::user()->name;
        $arr['pending'] = SalesEstimate::filter($request->all())->where('agent_id',\Auth::id())->where('reference',$referenceType)->where('status','pending')->count();
        $arr['refused'] = SalesEstimate::filter($request->all())->where('agent_id',\Auth::id())->where('reference',$referenceType)->where('status','refused')->count();
        $arr['accepted'] = SalesEstimate::filter($request->all())->where('agent_id',\Auth::id())->where('reference',$referenceType)->where('status','accepted')->count();
        $arr['closed'] = SalesEstimate::filter($request->all())->where('agent_id',\Auth::id())->where('reference',$referenceType)->where('status','closed')->count();
        $arr['total'] = SalesEstimate::filter($request->all())->where('agent_id',\Auth::id())->where('reference',$referenceType)->count();
        $arr['amount'] = SalesEstimate::filter($request->all())->where('agent_id',\Auth::id())->where('reference',$referenceType)->get()->sum($column);
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

        $serviceTable = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($serviceTable);

        $productTable = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($productTable);

        $itemTable = 'company_'.$request->company_id.'_items';
        Item::setGlobalTable($itemTable);

        $table = 'company_'.$request->company_id.'_users';
        User::setGlobalTable($table);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);
        $clientCategorytable = 'company_'.$request->company_id.'_client_categories';
        ClientCategory::setGlobalTable($clientCategorytable);
        $productCategorytable = 'company_'.$request->company_id.'_product_categories';
        ProductCategory::setGlobalTable($productCategorytable);
        if($request->reference){
            $referenceType = [$request->reference];
        }else{
            $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
        }
        if($request->client_id){
            $invoiceIds = SalesEstimate::where('client_id',$request->client_id)->whereIn('reference',$referenceType)->pluck('id')->toArray();
            $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->whereIn('parent_id',$invoiceIds)->pluck('reference_id')->toArray();
            $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->whereIn('parent_id',$invoiceIds)->pluck('reference_id')->toArray();
        }else{
            $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
            $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
        }

        $products = Product::filter($request->all())->whereIn('id',$itemProductIds)->get();
        $services = Service::filter($request->all())->whereIn('id',$itemServiceIds)->get();
           
            $arr = [];
            $data = [];
            if($request->category == 'product_categories'){
                $categories = ProductCategory::get();
                //    return $categories;
                $arr = [];
                $data = [];
                $request['productCategoryNull'] = 1;
                $arr['name'] = 'No Selected Category';
                $arr['reference'] = '';
                $arr['pending'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->count();
                $arr['accepted'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','accepted')->count();
                $arr['closed'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','closed')->count();
                $arr['refused'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','refused')->count();
                $arr['total'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->count();
                $arr['units'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->count();
                $arr['amount'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_with_out_vat');
                $data[] = $arr;
                unset($request['productCategoryNull']);
                foreach($categories as $category){
                    $request['productCategory'] = $category->id;
                    $arr['name'] = $category->name;
                    $arr['reference'] = '';
                    $arr['pending'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->count();
                    $arr['accepted'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','accepted')->count();
                    $arr['closed'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','closed')->count();
                    $arr['refused'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','refused')->count();
                    $arr['total'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->count();
                    $arr['units'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->count();
                    $arr['amount'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_with_out_vat');
                    $data[] = $arr;
                }
            }else{

                foreach($products as $product){
                    $request['product_id'] = $product->id;
                    $arr['name'] = $product->name;
                    $arr['reference'] = $product->reference.''.$product->reference_number;
                    $arr['pending'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->count();
                    $arr['accepted'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->where('status','accepted')->count();
                    $arr['closed'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->where('status','closed')->count();
                    $arr['refused'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->where('status','refused')->count();
                    $arr['total'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->count();
                    $arr['units'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->count();
                    $arr['amount'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->get()->sum('amount_with_out_vat');
                    $data[] = $arr;
                }
                foreach($services as $service){
                    $request['service_id'] = $service->id;
                    $arr['name'] = $service->name;
                    $arr['reference'] = $service->reference.''.$service->reference_number;
                    $arr['pending'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->where('status','pending')->count();
                    $arr['accepted'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->where('status','accepted')->count();
                    $arr['closed'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->where('status','closed')->count();
                    $arr['refused'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->where('status','refused')->count();
                    $arr['total'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->count();
                    $arr['units'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->count();
                    $arr['amount'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->get()->sum('amount_with_out_vat');
    
                    $data[] = $arr;
                }
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
        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);

        $categoryTable = 'company_'.$request->company_id.'_client_categories';
        ClientCategory::setGlobalTable($categoryTable);
        $productCategorytable = 'company_'.$request->company_id.'_product_categories';
        ProductCategory::setGlobalTable($productCategorytable);
        $technicalIncidentReference =  Reference::where('type', 'Incident')->pluck('prefix')->toArray();
        $workOrderReference =  Reference::where('type', 'Work Order')->pluck('prefix')->toArray();
        $workDeliveryNotesReference =  Reference::where('type', 'Work Delivery Note')->pluck('prefix')->toArray();
        $workEstimatesReference =  Reference::where('type', 'Work Estimate')->pluck('prefix')->toArray();

        if($request->after_tax){
            $taxColumn = 'amount';
        }else{
            $taxColumn = 'amount_with_out_vat';
        }
        if($request->reference){
            $referenceType = [$request->reference];
        }else{

            $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
        }
        $data = [];
        if( $request->type == "overview" ){
            $data = [
                "incidents_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            TechnicalIncident::filter($request->all())->whereIn('reference',$technicalIncidentReference)->where('status', 'pending')->count()
                        ] 
                    ], 
                    [
                            "type" => "bar", 
                            "label" => "Refused", 
                            "backgroundColor" => "#FB6363", 
                            "data" => [
                                TechnicalIncident::filter($request->all())->whereIn('reference',$technicalIncidentReference)->where('status', 'refused')->count()
                            ]
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Resolved", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            TechnicalIncident::filter($request->all())->whereIn('reference',$technicalIncidentReference)->where('status', 'resolved')->count()
                        ]
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            TechnicalIncident::filter($request->all())->whereIn('reference',$technicalIncidentReference)->where('status', 'closed')->count()
                        ]
                    ],
                ],
                "estimates_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending (". TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'pending')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'pending')->get()->sum($taxColumn)
                        ]
                    ], 
                    [
                            "type" => "bar", 
                            "label" => "Refused (". TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'refused')->count().")",
                            "backgroundColor" => "#FB6363", 
                            "data" => [
                                TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'refused')->get()->sum($taxColumn)
                            ] 
                        ], 
                    [
                        "type" => "bar", 
                        "label" => "Accepted (". TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'accepted')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'accepted')->get()->sum($taxColumn)
                        ]
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed (". TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'closed')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'closed')->get()->sum($taxColumn)
                        ]
                    ],
                ], 
                "orders_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending (". TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'pending')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'pending')->get()->sum($taxColumn)
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Refused (". TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'refused')->count().")",  
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'refused')->get()->sum($taxColumn)
                        ]  
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "In Progress (". TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'in_progress')->count().")",
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'in_progress')->get()->sum($taxColumn)
                        ] 
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed (". TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'closed')->count().")",
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'closed')->get()->sum($taxColumn)
                        ] 
                    ],
                         
                ], 
                "delivery_notes_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending Invoice (". TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'pending')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'pending')->get()->sum($taxColumn)
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "In Progress (". TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'in_progress')->count().")", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'pending')->get()->sum($taxColumn)
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Closed (". TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'closed')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'closed')->get()->sum($taxColumn)
                        ] 
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Invoiced  (". TechnicalTable::filter($request->all())->where('reference', 'invoiced')->where('status', 'closed')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'invoiced')->get()->sum($taxColumn)
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
                             TechnicalIncident::filter($request->all())->whereIn('reference',$technicalIncidentReference)->where('status', 'pending')->count()
                        ]
                    ], 
                    [
                            "type" => "bar", 
                            "label" => "Refused", 
                            "backgroundColor" => "#FB6363", 
                            "data" => [
                                 TechnicalIncident::filter($request->all())->whereIn('reference',$technicalIncidentReference)->where('status', 'refused')->count()
                            ]
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Resolved", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                             TechnicalIncident::filter($request->all())->whereIn('reference',$technicalIncidentReference)->where('status', 'resolved')->count()
                        ]
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                             TechnicalIncident::filter($request->all())->whereIn('reference',$technicalIncidentReference)->where('status', 'closed')->count()
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
                            TechnicalIncident::filter($request->all())->whereIn('reference',$technicalIncidentReference)->where('status', 'pending')->count()
                        ] 
                    ], 
                    [
                            "type" => "bar", 
                            "label" => "Refused", 
                            "backgroundColor" => "#FB6363", 
                            "data" => [
                                TechnicalIncident::filter($request->all())->whereIn('reference',$technicalIncidentReference)->where('status', 'refused')->count()
                            ]
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Resolved", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            TechnicalIncident::filter($request->all())->whereIn('reference',$technicalIncidentReference)->where('status', 'resolved')->count()
                        ]
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            TechnicalIncident::filter($request->all())->whereIn('reference',$technicalIncidentReference)->where('status', 'closed')->count()
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
    public function technicalByClient(Request $request){
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
        $categoryTable = 'company_'.$request->company_id.'_client_categories';
        ClientCategory::setGlobalTable($categoryTable);
        $productCategorytable = 'company_'.$request->company_id.'_product_categories';
        ProductCategory::setGlobalTable($productCategorytable);
        if($request->after_tax){
            $taxColumn = 'amount';
        }else{
            $taxColumn = 'amount_with_out_vat';
        }
        if($request->reference){
            $referenceType = [$request->reference];
        }else{

            $referenceType = Reference::whereIn('type', ['Work Delivery Note','Work Estimate', 'Work Order'])->pluck('prefix')->toArray();
        }

        $data = [];
        if($request->type == 'incident_by_client'){
            $data['incident_clients'] = [];
            if($request->category == 'client_categories'){
                $categories = ClientCategory::get();
                $request['clientCategoryNull'] = 1;
                $data['incident_clients'][] = [
                    "type" => "bar",
                    "label" => "No Selected Category",
                    "backgroundColor" => "#26C184",
                    "data" => [
                        TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($taxColumn),
                    ]
                ];
                unset($request['clientCategoryNull']);
                foreach($categories as $category){
                    $request['clientCategory'] = $category->id;
                    $data['incident_clients'][] = [
                    "type" => "bar",
                    "label" => "" .  $category->name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                            TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($taxColumn),
                        ]
                    ];
                }
            }else{
                $client_ids = TechnicalTable::filter($request->all())->whereHas('client')->pluck('client_id')->toArray();
                $clients = Client::whereIn('id',$client_ids)->get();
                foreach($clients as $client){
                    $data['incident_clients'][] = [
                    "type" => "bar",
                    "label" => "" .  $client->legal_name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                            TechnicalTable::filter($request->all())->where('client_id',$client->id)->whereIn('reference',$referenceType)->get()->sum($taxColumn),
                        ]
                    ];
                }
            }    
        }elseif($request->type == 'by_client_history'){
            $arr = [];
            $data = [];
            if($request->category == 'client_categories'){
                $categories = ClientCategory::get();
                $request['clientCategoryNull'] = 1;
                $arr['name'] = "No Selected Category";
                $arr['pending'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->count();
                $arr['refused'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','refused')->count();
                $arr['accepted'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','accepted')->count();
                $arr['closed'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','closed')->count();
                $arr['total'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                $arr['amount'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($taxColumn);

                $data[] = $arr;
                unset($request['clientCategoryNull']);
                foreach($categories as $category){
                    $request['clientCategory'] = $category->id;
                    $arr['name'] = $category->name;
                    $arr['pending'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->count();
                    $arr['refused'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','refused')->count();
                    $arr['accepted'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','accepted')->count();
                    $arr['closed'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','closed')->count();
                    $arr['total'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                    $arr['amount'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($taxColumn);
    
                    $data[] = $arr;
                }
            }
            else{

                $client_ids = TechnicalTable::pluck('client_id')->toArray();
                $clients = Client::whereIn('id',$client_ids)->get();
    
                foreach($clients as $client){
                    $arr['name'] = $client->legal_name;
                    $arr['pending'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('client_id',$client->id)->where('status','pending')->count();
                    $arr['refused'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('client_id',$client->id)->where('status','refused')->count();
                    $arr['accepted'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('client_id',$client->id)->where('status','accepted')->count();
                    $arr['closed'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('client_id',$client->id)->where('status','closed')->count();
                    $arr['total'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('client_id',$client->id)->count();
                    $arr['amount'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('client_id',$client->id)->get()->sum($taxColumn);
    
                    $data[] = $arr;
                }
            }    
            
        }
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
            
    }
    public function technicalByAgent(Request $request){
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
        $categoryTable = 'company_'.$request->company_id.'_client_categories';
        ClientCategory::setGlobalTable($categoryTable);
        $productCategorytable = 'company_'.$request->company_id.'_product_categories';
        ProductCategory::setGlobalTable($productCategorytable);
        if($request->after_tax){
            $taxColumn = 'amount';
        }else{
            $taxColumn = 'amount_with_out_vat';
        }
        if($request->reference){
            $referenceType = [$request->reference];
        }else{

            $referenceType = Reference::whereIn('type', ['Work Delivery Note','Work Estimate', 'Work Order'])->pluck('prefix')->toArray();
        }

        $data = [];
        if($request->type == 'incident_by_agent'){
            $data['incident_agent'] = [];
            $data['incident_agent'][] = [
            "type" => "bar",
            "label" => "" . \Auth::user()->name,
            "backgroundColor" => "#26C184",
            "data" => [
                    TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->get()->sum( $taxColumn),
                ]
            ];
        }elseif($request->type == 'by_agent_history'){

            // $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $arr = [];
            $data = [];

            $arr['name'] = \Auth::user()->name;
            $arr['pending'] = TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->where('agent_id',\Auth::id())->where('status','pending')->count();
            $arr['refused'] = TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->where('agent_id',\Auth::id())->where('status','refused')->count();
            $arr['accepted'] = TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->where('agent_id',\Auth::id())->where('status','accepted')->count();
            $arr['closed'] = TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->where('agent_id',\Auth::id())->where('status','closed')->count();
            $arr['total'] = TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->where('agent_id',\Auth::id())->count();
            $arr['amount'] = TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->where('agent_id',\Auth::id())->get()->sum($taxColumn);

                $data[] = $arr;
            
        }
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
            
    }
    public function technicalByItem(Request $request){
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
        $categoryTable = 'company_'.$request->company_id.'_client_categories';
        ClientCategory::setGlobalTable($categoryTable);
        $productCategorytable = 'company_'.$request->company_id.'_product_categories';
        ProductCategory::setGlobalTable($productCategorytable);
        if($request->after_tax){
            $taxColumn = 'amount';
        }else{
            $taxColumn = 'amount_with_out_vat';
        }
        if($request->reference){
            $referenceType = [$request->reference];
        }else{
            $referenceType = Reference::whereIn('type', ['Work Delivery Note','Work Estimate', 'Work Order'])->pluck('prefix')->toArray();
        }
        $data = [];
        if($request->type == 'incident_by_item'){
            
            $itemProductIds = Item::whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
            $itemServiceIds = Item::whereIn('reference',['SER'])->pluck('reference_id')->toArray();
            $products = Product::filter($request->all())->whereIn('id',$itemProductIds)->get();
            $services = Service::filter($request->all())->whereIn('id',$itemServiceIds)->get();

            if($request->category == 'catalog'){

                $data['invoice_items'] = [];
                foreach($products as $product){
                    $request['product_id'] = $product->id;
                    $data['invoice_items'][] = [
                    "type" => "bar",
                    "label" => "" .  $product->name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                            TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->get()->sum('amount'),
                        ]
                    ];
                }
                foreach($services as $service){
                    $request['service_id'] = $service->id;
                    $data['invoice_items'][] = [
                    "type" => "bar",
                    "label" => "" .  $service->name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                            TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->get()->sum('amount'),
                        ]
                    ];
                }
            }else{
                $categories = ProductCategory::get();
                $request['productCategoryNull'] = 1;
                $arr['name'] = 'Not found in catalog';
                $data['invoice_items'][] = [
                "type" => "bar",
                "label" => 'Not found in catalog',
                "backgroundColor" => "#26C184",
                "data" => [
                        TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->get()->sum('amount'),
                    ]
                ];
                unset($request['productCategoryNull']);
                foreach($categories as $category){
                    $request['productCategory'] = $category->id;
                    $data['invoice_items'][] = [
                        "type" => "bar",
                        "label" => "" .  $category->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                            TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($taxColumn),
                        ]
                    ];
                }
            }
        }
        elseif($request->type == 'by_item_history'){

            $productReferenceType = Reference::where('type', 'Product')->pluck('prefix')->toArray();
            $serviceReferenceType = Reference::where('type', 'Service')->pluck('prefix')->toArray();
            $itemProductIds = Item::whereIn('reference',$productReferenceType)->pluck('reference_id')->toArray();
            $itemServiceIds = Item::whereIn('reference', $serviceReferenceType)->pluck('reference_id')->toArray();
            $products = Product::filter($request->all())->whereIn('id',$itemProductIds)->get();
            $services = Service::filter($request->all())->whereIn('id',$itemServiceIds)->get();
               
            $arr = [];
            $data = [];
            if($request->category == 'catalog'){

                foreach($products as $product){
                    $request['product_id'] = $product->id;
                    $arr['name'] = $product->name;
                    $arr['reference'] = $product->reference.''.$product->reference_number;
                    $arr['pending'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->count();
                    $arr['accepted'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','accepted')->count();
                    $arr['closed'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','closed')->count();
                    $arr['refused'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','refused')->count();
                    $arr['total'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                    $arr['units'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                    $arr['amount'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount');
                    
    
                    $data[] = $arr;
                }
                foreach($services as $service){
                    $request['service_id'] = $service->id;
                    $arr['name'] = $service->name;
                    $arr['reference'] = $service->reference.''.$service->reference_number;
                    $arr['pending'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->count();
                    $arr['accepted'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','accepted')->count();
                    $arr['closed'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','closed')->count();
                    $arr['refused'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','refused')->count();
                    $arr['total'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                    $arr['amount'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount');
                    $data[] = $arr;
                }
            }else{
                $categories = ProductCategory::get();
                $request['productCategoryNull'] = 1;
                $arr['name'] = 'Not found in catalog';
                $arr['reference'] = '-';
                $arr['pending'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->count();
                $arr['accepted'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','accepted')->count();
                $arr['closed'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','closed')->count();
                $arr['refused'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','refused')->count();
                $arr['total'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                $arr['amount'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount');
                $data[] = $arr;
                unset($request['productCategoryNull']);
                foreach($categories as $category){
                    $request['productCategory'] = $category->id;
                    $arr['name'] = $category->name;
                    $arr['reference'] = '-';
                    $arr['pending'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->count();
                    $arr['accepted'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','accepted')->count();
                    $arr['closed'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','closed')->count();
                    $arr['refused'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','refused')->count();
                    $arr['total'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                    $arr['amount'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount');
                    $data[] = $arr;
                }
            }
    
        }
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
            
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
    /* -------------------------------------------------------------------------- */
    /*                               Purchase Report                              */
    /* -------------------------------------------------------------------------- */
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
        $productCategorytable = 'company_'.$request->company_id.'_product_categories';
        ProductCategory::setGlobalTable($productCategorytable);
        $clientCategorytable = 'company_'.$request->company_id.'_client_categories';
        ClientCategory::setGlobalTable($clientCategorytable);
        if($request->after_tax){
            $column = 'amount';
        }else{
            $column = 'amount_with_out_vat';
        }
        if($request->reference){
            $referenceType = [$request->reference];
        }else{
            $referenceType = Reference::whereIn('type', ['Purchase Invoice'])->pluck('prefix')->toArray();
        }
        if($request->type == "supplier"){

            $supplier_ids = PurchaseTable::whereHas('supplier')->pluck('supplier_id')->toArray();
            $suppliers = Supplier::whereIn('id',$supplier_ids)->get();
            $data = [];
            if($request->category == 'supplier_categories'){
                $categories = ClientCategory::where('type','supplier')->get();
                $data = [];
                $request['supplierCategoryNull'] = 1;
                $data['purchase_supplier'][] = [
                    "type" => "bar",
                    "label" => "No Selected Category",
                    "backgroundColor" => "#26C184",
                    "data" => [
                        PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($column),
                    ]
                ];
                unset($request['supplierCategoryNull']);
                foreach($categories as $category){
                    $request['supplierCategory'] = $category->id;
                    $data['purchase_supplier'][] = [
                    "type" => "bar",
                    "label" => "" .  $category->name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                            PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($column),
                        ]
                    ];
                }
            }else{
                $data['purchase_supplier'] = [];

                foreach($suppliers as $supplier){
                    $data['purchase_supplier'][] = [
                    "type" => "bar",
                    "label" => "" .  $supplier->legal_name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                            PurchaseTable::filter($request->all())->where('supplier_id',$supplier->id)->whereIn('reference',$referenceType)->get()->sum($column),
                        ]
                    ];
                }
            }
            
            return response()->json([
                "status" => true,
                "data" =>  $data
            ]);

        }elseif($request->type == 'items'){
            $itemProductIds = Item::with('supplier')->whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
            $itemServiceIds = Item::with('supplier')->whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
            $expenseInvestmentIds = Item::with('supplier')->whereIn('type',$referenceType)->whereIn('reference',['EAI'])->pluck('reference_id')->toArray();
            $products = Product::whereIn('id',$itemProductIds)->get();
            $services = Service::whereIn('id',$itemServiceIds)->get();
            $expenses = ExpenseAndInvestment::whereIn('id',$expenseInvestmentIds)->get();

            $data = [];
            if($request->category == 'catalog'){

                $data['purchase_items'] = [];
    
                foreach($products as $product){
                    $request['product_id'] = $product->id;
                    $data['purchase_items'][] = [
                    "type" => "bar",
                    "label" => "" .  $product->name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                        PurchaseTable::filter($request->all())->whereIn('type',$referenceType)->get()->sum('amount'),
                        ]
                    ];
                }
                foreach($services as $service){
                    $request['service_id'] = $service->id;
                    $data['purchase_items'][] = [
                    "type" => "bar",
                    "label" => "" .  $service->name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                        PurchaseTable::filter($request->all())->whereIn('type',$referenceType)->get()->sum('amount'),
                        ]
                    ];
                }
                foreach($expenses as $expense){
                    $request['expense_id'] = $expense->id;
                    $data['purchase_items'][] = [
                    "type" => "bar",
                    "label" => "" .  $expense->name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                        PurchaseTable::filter($request->all())->whereIn('type',$referenceType)->get()->sum('amount'),
                        ]
                    ];
                }
            }else{
                $categories = ProductCategory::get();
                $data = [];
                $request['productCategoryNull'] = 1;
                $data['purchase_items'][] = [
                    "type" => "bar",
                    "label" => "No Selected Category",
                    "backgroundColor" => "#26C184",
                    "data" => [
                        PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($column),
                    ]
                ];
                unset($request['productCategoryNull']);
                foreach($categories as $category){
                    $request['productCategory'] = $category->id;
                    $data['purchase_items'][] = [
                    "type" => "bar",
                    "label" => "" .  $category->name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                            PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($column),
                        ]
                    ];
                }
            }
            return response()->json([
                "status" => true,
                "data" =>  $data
            ]);
        }
    }

    /* -------------------------------------------------------------------------- */
    /*                 Purchase report history data by supplier                */
    /* -------------------------------------------------------------------------- */

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
        $productCategorytable = 'company_'.$request->company_id.'_product_categories';
        ProductCategory::setGlobalTable($productCategorytable);
        $clientCategorytable = 'company_'.$request->company_id.'_client_categories';
        ClientCategory::setGlobalTable($clientCategorytable);
        if($request->after_tax){
            $column = 'amount';
        }else{
            $column = 'amount_with_out_vat';
        }
        
        if($request->reference){
            $referenceType = [$request->reference];
        }else{
            $referenceType = Reference::whereIn('type', ['Purchase Invoice'])->pluck('prefix')->toArray();
        }
        $supplier_ids = PurchaseTable::whereHas('supplier')->whereIn('reference',$referenceType)->pluck('supplier_id')->toArray();
        $suppliers = Supplier::whereIn('id',$supplier_ids)->get();

        // $referenceType = Reference::where('type', $request->type)->pluck('prefix')->toArray();
        $arr = [];
        $data = [];
        if($request->category == 'supplier_categories'){
            $categories = ClientCategory::where('type','supplier')->get();
            $request['supplierCategoryNull'] = 1;
            $arr['name'] = "No Selected Category";
            $arr['invoiced'] = PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($column);
            $arr['paid'] =  PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_paid');
            $arr['Unpaid'] =  PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_due');
            $data[] = $arr;
            unset($request['supplierCategoryNull']);
            foreach($categories as $category){
                $request['supplierCategory'] = $category->id;
                $arr['name'] = $category->name;
                $arr['invoiced'] = PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($column);
                $arr['paid'] =  PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_paid');
                $arr['Unpaid'] =  PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_due');
                $data[] = $arr;
            }
        }else{

            foreach($suppliers as $supplier){
                $arr['name'] = $supplier->legal_name;
                $arr['invoiced'] = PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->where('supplier_id',$supplier->id)->get()->sum($column);
                $arr['paid'] =  PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->where('supplier_id',$supplier->id)->get()->sum('amount_paid');
                $arr['Unpaid'] =  PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->where('supplier_id',$supplier->id)->get()->sum('amount_due');
                $data[] = $arr;
            }
        }
            
            return response()->json([
                "status" => true,
                "data" =>  $data
            ]);
    }

    /* -------------------------------------------------------------------------- */
    /*                       Purchase Report history by item                      */
    /* -------------------------------------------------------------------------- */

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
        $productCategorytable = 'company_'.$request->company_id.'_product_categories';
        ProductCategory::setGlobalTable($productCategorytable);
        $clientCategorytable = 'company_'.$request->company_id.'_client_categories';
        ClientCategory::setGlobalTable($clientCategorytable);
        if($request->after_tax){
            $column = 'amount';
        }else{
            $column = 'amount_with_out_vat';
        }
        
        if($request->reference){
            $referenceType = [$request->reference];
        }else{
            $referenceType = Reference::whereIn('type', ['Purchase Invoice'])->pluck('prefix')->toArray();
        }
        
        $referenceType = Reference::whereIn('type', ['Purchase Invoice'])->pluck('prefix')->toArray();
        $itemProductIds = Item::with('supplier')->whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
        $itemServiceIds = Item::with('supplier')->whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
        $expenseInvestmentIds = Item::with('supplier')->whereIn('type',$referenceType)->whereIn('reference',['EAI'])->pluck('reference_id')->toArray();
        $products = Product::filter($request->all())->whereIn('id',$itemProductIds)->get();
        $services = Service::filter($request->all())->whereIn('id',$itemServiceIds)->get();
        $expenses = ExpenseAndInvestment::whereIn('id',$expenseInvestmentIds)->get();
           
        $arr = [];
        $data = [];
        if($request->category == 'catalog'){

            foreach($products as $product){
                $request['product_id'] = $product->id;
                $arr['name'] = $product->name;
                $arr['reference'] = $product->reference.''.$product->reference_number;
                $arr['units'] = PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                $arr['amount'] = PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_with_out_vat');
                
    
                $data[] = $arr;
            }
            foreach($services as $service){
                $request['service_id'] = $service->id;
                $arr['name'] = $service->name;
                $arr['reference'] = $service->reference.''.$service->reference_number;
                $arr['units'] = PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                $arr['amount'] = PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_with_out_vat');
                $data[] = $arr;
            }
            foreach($expenses as $expense){
                $request['expense_id'] = $expense->id;
                $arr['name'] = $expense->name;
                $arr['reference'] = $expense->reference.''.$expense->reference_number;
                $arr['units'] = PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                $arr['amount'] = PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_with_out_vat');
                $data[] = $arr;
            }
        }else{
            $categories = ProductCategory::get();
            $data = [];
            $request['productCategoryNull'] = 1;
            $arr['name'] = "No Selected Category";
            $arr['reference'] = '-';
            $arr['units'] = PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->count();
            $arr['amount'] = PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_with_out_vat');
            $data[] = $arr;
            unset($request['productCategoryNull']);
            foreach($categories as $category){
                $request['productCategory'] = $category->id;
                $arr['name'] = $category->name;
                $arr['reference'] = '-';
                $arr['units'] = PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                $arr['amount'] = PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_with_out_vat');
                $data[] = $arr;
            }
        }
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
    }

    /* -------------------------------------------------------------------------- */
    /*                               Cash Flow Report                              */
    /* -------------------------------------------------------------------------- */
    public function cashFlow(Request $request){
        $purchaseTables = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($purchaseTables); 
        $ticketTables = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTicket::setGlobalTable($ticketTables); 

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
        $purchaseReceiptTable = 'company_'.$request->company_id.'_purchase_receipts';
        PurchaseReceipt::setGlobalTable($purchaseReceiptTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);
        $invoiceReferences =  Reference::where('type', 'Normal Invoice')->pluck('prefix')->toArray();
        $purchaseReferences =  Reference::where('type', 'Purchase Invoice')->pluck('prefix')->toArray();
        $returnInvoiceReferences =  Reference::where('type', 'Refund Invoice')->pluck('prefix')->toArray();
        $data = [];
        if($request->type == "overview"){
            $data = [
                "cash_flow" => [
                    [
                        "type" => "bar", 
                        "label" => "Deposits", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                             InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferences)->where('paid', '1')->sum('amount')
                        ] 
                    ], 
                    [
                            "type" => "bar", 
                            "label" => "Withdrawals", 
                            "backgroundColor" => "#FB6363", 
                            "data" => [
                                InvoiceReceipt::filter($request->all())->whereIn('type', $returnInvoiceReferences)->where('paid', '1')->sum('amount')
                            ] 
                        ], 
                    [
                        "type" => "bar", 
                        "label" => "Balance", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                             InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferences)->where('paid', '1')->sum('amount') - InvoiceReceipt::filter($request->all())->whereIn('type', $returnInvoiceReferences)->where('paid', '1')->sum('amount')
                        ] 
                    ] 
                ], 
                "deposits" => [
                    [
                        "type" => "bar", 
                        "label" => "Invoices", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferences)->where('paid', '1')->sum('amount')
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Account Deposits", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            Deposit::filter($request->all())->where('type','deposit')->where('paid_by','1')->sum('amount')
                        ]  
                    ]
                ], 
                "withdrawals" => [
                    [
                        "type" => "bar", 
                        "label" => "Refunds", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                             InvoiceReceipt::filter($request->all())->whereIn('type', $returnInvoiceReferences)->where('paid', '1')->sum('amount')
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Purchases", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            "$ ". PurchaseReceipt::filter($request->all())->whereIn('type', $purchaseReferences)->where('paid', '1')->sum('amount')
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Tickets and other expenses", 
                        "backgroundColor" => "#FE9140", 
                            "data" => [
                                // "$ ".PurchaseTicket::filter($request->all())->where('paid', '1')->sum('amount')
                                "$ 0"
                            ] 
                        ],
                    [
                        "type" => "bar", 
                        "label" => "Account Withdrawals", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            "$ ". Deposit::filter($request->all())->where('type','withdraw')->where('paid_by','1')->sum('amount')
                        ] 
                    ]  
                ]
            ];
        }elseif($request->type == 'paymentOption'){
            $paymentOptions = PaymentOption::get();
               
            $data = [];
            $data['invoice_items'] = [];
            foreach($paymentOptions as $paymentOption){
                $data['invoice_items'][] = [
                "type" => "bar",
                "label" => "" .  $paymentOption->name,
                "backgroundColor" => "#26C184",
                "data" => [
                        InvoiceTable::filter($request->all())->whereHas('payment_options', function ($query) use ($paymentOption) {
                            $query->where('id', $paymentOption->id);
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
                            InvoiceTable::filter($request->all())->get()->sum('amount'),
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

        $paymentOptions = PaymentOption::filter($request->all())->get();
       
        $arr = [];
        $data = [];

        foreach($paymentOptions as $paymentOption){
            $deposit =  Deposit::filter($request->all())->WhereHas('payment_options', function ($query) use ($paymentOption) {
                $query->where('payment_option', $paymentOption->id);
            })->where('type','deposit')->get()->sum('amount');
            $withdrawal = Deposit::filter($request->all())->WhereHas('payment_options', function ($query) use ($paymentOption) {
                $query->where('payment_option', $paymentOption->id);
            })->where('type','withdraw')->get()->sum('amount');
            $invoiceAmount = InvoiceTable::filter($request->all())->WhereHas('payment_options', function ($query) use ($paymentOption) {
                $query->where('id', $paymentOption->id);
            })->get()->sum('amount');
                // dd($invoiceAmount);
            $arr['name'] = $paymentOption->name;
            $arr['deposit'] = $deposit + $invoiceAmount;
            $arr['withdrawals'] = $withdrawal;
            $arr['balance'] = (($invoiceAmount + $deposit) - $withdrawal);
            

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

        $arr = [];
        $data = [];
            
        $deposit = Deposit::filter($request->all())->where('type','deposit')->where('paid_by','1')->sum('amount');
        $withdrawal = Deposit::filter($request->all())->where('type','withdraw')->where('paid_by','1')->sum('amount');
        $invoiceAmount = InvoiceTable::filter($request->all())->where('agent_id',\Auth::id())->get()->sum('amount');

        $arr['name'] = \Auth::user()->name;
        $arr['deposit'] = $deposit + $invoiceAmount;
        $arr['withdrawals'] = $withdrawal;
        $arr['balance'] = (($invoiceAmount + $deposit) -  $withdrawal);

        $data[] = $arr;
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

            $paymentOption = InvoiceTable::filter($request->all())->get();
            $purchasePaymentOptions = PurchaseTable::filter($request->all())->where('reference','PINV')->get();
            
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
    /* -------------------------------------------------------------------------- */
    /*                           Stock Valuation Report                           */
    /* -------------------------------------------------------------------------- */
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
        $column  = 'virtual_stock';
        if($request->stock  == 'stock'){
            $column  = 'stock';
        }
        $data = [];
        $data = [
            "stock_valuation" => [
                [
                    "type" => "bar", 
                    "label" => "Sales stock value", 
                    "backgroundColor" => "#26C184", 
                    "data" => [
                        Product::filter($request->all())->sum('price') * Product::filter($request->all())->get()->sum($column)
                    ] 
                ], 
                [
                    "type" => "bar", 
                    "label" => "Purchase stock value", 
                    "backgroundColor" => "#FB6363", 
                    "data" => [
                        Product::filter($request->all())->sum('purchase_price')  * Product::filter($request->all())->get()->sum($column)
                    ] 
                ], 
            ]
        ];
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);
    }
    /* -------------------------------------------------------------------------- */
    /*                           Stock Valuation History                          */
    /* -------------------------------------------------------------------------- */
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

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);
        $products = Product::filter($request->all())->get();
        $arr = [];
        $data = [];
        $column  = 'virtual_stock';
        if($request->stock  == 'stock'){
            $column  = 'stock';
        }
        foreach($products as $product){
            $arr['reference'] = $product->reference.$product->reference_number;
            $arr['name'] = $product->name;
            $stock = $product->$column;
            $arr['stock'] = $stock;
            $arr['sales_stock_value'] = $product->price*$stock;
            $arr['purchase_stock_value'] = $product->purchase_price*$stock;

            $data[] = $arr;
        }
        return response()->json([
            "status" => true,
            "data" => $data
        ]);
        
    }

    /* -------------------------------------------------------------------------- */
    /*                             OF Evolution Report                            */
    /* -------------------------------------------------------------------------- */
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

        if($request->after_tax){
            $column = 'amount';
            }else{
            $column = 'amount_with_out_vat';
        }

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
                         InvoiceTable::filter($request->all())->where('client_id',$client->id)->get()->sum($column),
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
                $arr['Q1'] = InvoiceTable::filter($request->all())->where('client_id',$client->id)->get()->sum($column);
                $arr['Q2'] = '0.00';
                $arr['Q3'] = '0.00';
                $arr['Q4'] = '0.00';
                $arr['tottal'] = InvoiceTable::filter($request->all())->where('client_id',$client->id)->get()->sum($column);
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
                         InvoiceTable::filter($request->all())->get()->sum($column),
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
                $arr['Q1'] = InvoiceTable::filter($request->all())->where('agent_id',\Auth::id())->get()->sum($column);
                $arr['Q2'] = '0.00';
                $arr['Q3'] = '0.00';
                $arr['Q4'] = '0.00';
                $arr['total'] = InvoiceTable::filter($request->all())->where('agent_id',\Auth::id())->get()->sum($column);

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
        $products = Product::filter($request->all())->whereIn('id',$itemProductIds)->get();
        $services = Service::filter($request->all())->whereIn('id',$itemServiceIds)->get();
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
                            })->get()->sum($column),
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
        $products = Product::filter($request->all())->whereIn('id',$itemProductIds)->get();
        $services = Service::filter($request->all())->whereIn('id',$itemServiceIds)->get();
           
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
                             PurchaseTable::filter($request->all())->where('reference','PINV')->where('supplier_id',$supplier->id)->get()->sum($column),
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
                $arr['Q1'] = PurchaseTable::filter($request->all())->where('supplier_id',$supplier->id)->get()->sum($column);
                $arr['Q2'] = '0.00';
                $arr['Q3'] = '0.00';
                $arr['Q4'] = '0.00';
                $arr['total'] = PurchaseTable::filter($request->all())->where('supplier_id',$supplier->id)->get()->sum($column);
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
        $products = Product::filter($request->all())->whereIn('id',$itemProductIds)->get();
        $services = Service::filter($request->all())->whereIn('id',$itemServiceIds)->get();
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
        $products = Product::filter($request->all())->whereIn('id',$itemProductIds)->get();
        $services = Service::filter($request->all())->whereIn('id',$itemServiceIds)->get();
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
        $incomeTaxTable = 'company_'.$request->company_id.'_income_taxes';
        IncomeTax::setGlobalTable($incomeTaxTable);

        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);

        $data = [];
        $taxes =  ConsumptionTax::get();
        $column = 'vat';
        if($request->name  == 'income_tax'){
            $taxes = IncomeTax::get();
            $column='tax';
        }else{
            $taxes =  ConsumptionTax::get();
        }
        if($request->type == 'taxSummary'){
        $referenceType = Reference::whereIn('type', ['Normal Invoice', 'Refund Invoice','Purchase Invoice'])->pluck('prefix')->toArray();
         $data = [];
         $data['tax_Summary'] = [];
        foreach($taxes as $tax){
                $data['tax_Summary'][] = [
                    "type" => "bar", 
                    "label" => "Collected", 
                    "backgroundColor" => "#26C184", 
                    "data" => [
                        InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($tax,$column) {
                            $query->where($column, $tax->tax);
                        })->get()->sum('tax_amount'),
                    ]  
                ];
                $data['tax_Summary'][] = [
                    "type" => "bar", 
                    "label" => "Paid", 
                    "backgroundColor" => "#FB6363", 
                    "data" => [
                        PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($tax,$column) {
                            $query->where($column, $tax->tax);
                        })->get()->sum('tax_amount'),
                    ]  
                ];
                $data['tax_Summary'][] = [
                    "type" => "bar", 
                    "label" => "Total", 
                    "backgroundColor" => "#FE9140", 
                    "data" => [
                        PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($tax,$column) {
                            $query->where($column, $tax->tax);
                        })->get()->sum('tax_amount'),
                    ]  
                ];
            }
        }else{
            $arr = [];
            $data = [];
            foreach($taxes as $tax){

                $arr['vat'] = $tax->primary_name.' '.$tax->tax.' '.'%';
                $arr['Collected'] = 'Collected';
                $arr['Paid'] = 'Paid';
                $arr['Total'] = 'Total';
                $arr['Subtotal'] = 'Subtotal';
                $arr['Tax'] = 'Tax';
                $arr['collected'] = InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($tax,$column) {
                    $query->where($column, $tax->tax);
                })->get()->sum('amount');
                $arr['ctax'] = InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use($tax,$column) {
                    $query->where($column, $tax->tax);
                })->get()->sum('tax_amount');
                $arr['paid'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use($tax,$column) {
                    $query->where($column, $tax->tax);
                })->get()->sum('amount');
                $arr['ptax'] = PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use($tax,$column) {
                    $query->where($column, $tax->tax);
                })->get()->sum('tax_amount');
                $arr['total'] = InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use($tax,$column) {
                    $query->where($column, $tax->tax);
                })->get()->sum('amount') - PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use($tax,$column) {
                    $query->where($column, $tax->tax);
                })->get()->sum('amount');
                $arr['ttax'] = InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use($tax,$column) {
                    $query->where($column, $tax->tax);
                })->get()->sum('tax_amount') - PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use($tax,$column) {
                    $query->where($column, $tax->tax);
                })->get()->sum('tax_amount');

                $data[] = $arr;
            }   
        }
        
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);

    }
}
