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
use App\Exports\ReportExport\OfProfitExport;
use App\Exports\ReportExport\InvoiceClientExport;
use App\Exports\ReportExport\OverViewExport;
use App\Exports\ReportExport\InvoiceAgentExport;
use App\Exports\ReportExport\InvoiceItemExport;
use App\Exports\ReportExport\CashFlowExport;
use App\Exports\ReportExport\PaymentOptionExport;
use App\Exports\ReportExport\CashFlowByAgentExport;
use App\Exports\ReportExport\SalesOverViewExport;
use App\Exports\ReportExport\SalesClientExport;
use App\Exports\ReportExport\SalesAgentsExport;
use App\Exports\ReportExport\SalesItemsExport;
use App\Exports\ReportExport\TechnicalOverViewExport;
use App\Exports\ReportExport\IncidentsByClientExport;
use App\Exports\ReportExport\IncidentsByAgentExport;
use App\Exports\ReportExport\IncidentByClientExport;
use App\Exports\ReportExport\IncidentByAgentExport;
use App\Exports\ReportExport\IncidentByItemExport;
use App\Exports\ReportExport\PurchaseSupplierExport;
use App\Exports\ReportExport\PurchaseItemExport;
use App\Exports\ReportExport\StockValuationExport;
use App\Exports\ReportExport\InvoiceByClientEvoluationExport;
use App\Exports\ReportExport\InvoiceByAgentEvoluationExport;
use App\Exports\ReportExport\InvoiceByItemEvoluationExport;
use Maatwebsite\Excel\Facades\Excel;

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
        $finalData = [
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
        if($request->export){
            $fileName = 'OVERVIEWREPORT-'.time().$request->company_id.'.xlsx';

                $arr = [];

                $arr['Sales'] = Item::filter($request->all())->whereIn('type', $invoiceReferenceTypes)->sum('subtotal');
                $arr['Expenses'] = InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferenceTypes)->where('paid', '1')->sum('amount');
                $arr['Profit'] = Item::filter($request->all())->whereIn('type', $invoiceReferenceTypes)->sum('subtotal') - InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferenceTypes)->where('paid', '1')->sum('amount');
                $arr['Invoiced'] = InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferenceTypes)->whereDate('expiration_date', '>', date('Y-m-d'))->sum('amount');
                $arr['Paid'] = InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferenceTypes)->where('paid', '1')->sum('amount');
                $arr['Unpaid'] = InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferenceTypes)->where('paid', '0')->sum('amount');
                $arr['PInvoiced'] = PurchaseReceipt::filter($request->all())->whereIn('type', $purchaseReferenceTypes)->whereDate('expiration_date', '>', date('Y-m-d'))->sum('amount');
                $arr['PPaid'] = PurchaseReceipt::filter($request->all())->whereIn('type', $purchaseReferenceTypes)->where('paid', '1')->sum('amount');
                $arr['PUnpaid'] = PurchaseReceipt::filter($request->all())->whereIn('type', $purchaseReferenceTypes)->where('paid', '0')->sum('amount');

                $finalData = $arr;

            Excel::store(new OverViewExport($finalData, $request), 'public/xlsx/'.$fileName);

            
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }
        return response()->json([
            "status" => true,
            "data" =>  $finalData
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

        $referenceType = Reference::whereIn('type', ['Normal Invoice', 'Refund Invoice'])->pluck('prefix')->toArray();
        $refundReferenceType = Reference::where('type', 'Refund Invoice')->pluck('prefix')->toArray();
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
                            number_format(InvoiceTable::filter($request->all())->where('reference', $referenceType)->get()->sum($taxColumn), 2, '.', ''),
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
                                number_format(InvoiceTable::filter($request->all())->where('reference', $referenceType)->get()->sum($taxColumn), 2, '.', ''),
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
                            number_format(InvoiceTable::filter($request->all())->where('client_id',$client->id)->where('reference', $referenceType)->get()->sum($taxColumn)+InvoiceTable::filter($request->all())->where('reference', $refundReferenceType)->get()->sum($taxColumn), 2, '.', ''),
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
                        number_format(InvoiceTable::filter($request->all())->where('agent_id',\Auth::id())->where('reference',$referenceType)->get()->sum($taxColumn), 2, '.', '')
                    ]
                ];
            return response()->json([
                "status" => true,
                "data" => $data
            ]);

        }else if($request->type == "items"){
            $taxColumn = 'amount';
            $referenceType = Reference::where('type', 'Normal Invoice')->pluck('prefix')->toArray();
            if($request->client_id){
                $invoiceIds = InvoiceTable::where('client_id',$request->client_id)->whereIn('reference',$referenceType)->pluck('id')->toArray();
                $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->whereIn('parent_id',$invoiceIds)->pluck('reference_id')->toArray();
                $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->whereIn('parent_id',$invoiceIds)->pluck('reference_id')->toArray();
            }else{
                $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
                $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
            }

            $products = Product::whereIn('id',$itemProductIds)->get();
            $services = Service::whereIn('id',$itemServiceIds)->get();

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
                            number_format(InvoiceTable::filter($request->all())->get()->sum('amount'), 2, '.', ''),
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
                            number_format(InvoiceTable::filter($request->all())->get()->sum('amount'), 2, '.', ''),
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
                            number_format(InvoiceTable::filter($request->all())->where('reference', $referenceType)->get()->sum($taxColumn), 2, '.', ''),
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
                            number_format(InvoiceTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($taxColumn), 2, '.', ''),
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
            $refundReferenceType = Reference::where('type', 'Refund Invoice')->pluck('prefix')->toArray();
            $client_ids = InvoiceTable::pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$client_ids)->get();
            $referenceType = Reference::whereIn('type', ['Normal Invoice', 'Refund Invoice'])->pluck('prefix')->toArray();
            $finalData = [];
            
            if($request->category == 'client_categories'){
                $arr = [];
                //no category history
                $request['clientCategoryNull'] = 1;
                $arr['name'] = 'No selected category';
                $arr['invoiced'] = number_format(InvoiceTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($column), 2, '.', '');
                $arr['paid'] = number_format(InvoiceTable::filter($request->all())->whereIn('reference',$referenceType)->where('set_as_paid', 1)->get()->sum($column), 2, '.', '');
                $arr['Unpaid'] = number_format(InvoiceTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($column)-InvoiceTable::filter($request->all())->whereIn('reference',$referenceType)->where('set_as_paid', 1)->get()->sum($column), 2, '.', '');
                $finalData[] = $arr;
                unset($request['clientCategoryNull']);
                // $clientCategory = Client::pluck('client_category')->toArray();
                $categories = ClientCategory::get();
                foreach($categories as $category){
                    $request['clientCategory'] = $category->id;
                    $arr['name'] = $category->name;
                    $arr['invoiced'] = number_format(InvoiceTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($column), 2, '.', '');
                    
                    $arr['paid'] = number_format(InvoiceTable::filter($request->all())->whereIn('reference',$referenceType)->where('set_as_paid', 1)->get()->sum($column), 2, '.', '');
                    $arr['Unpaid'] =  number_format(InvoiceTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($column)-InvoiceTable::filter($request->all())->whereIn('reference',$referenceType)->where('set_as_paid', 1)->get()->sum($column), 2, '.', '');
                    $finalData[] = $arr;
                }
            }else{
                foreach($clients as $client){
                    $arr['name'] = $client->legal_name;
                    $arr['reference'] = $client->reference.''.$client->reference_number;
                    $arr['ruc'] = $client->tin;
                    $arr['category'] = $client->client_category_name;
                    $arr['invoiced'] = number_format(InvoiceTable::filter($request->all())->where('client_id',$client->id)->whereIn('reference',$referenceType)->get()->sum($column), 2, '.', '');
                    $arr['paid'] = number_format(InvoiceTable::filter($request->all())->where('client_id',$client->id)->where('set_as_paid', 1)->whereIn('reference',$referenceType)->get()->sum($column), 2, '.', '');
                    $arr['Unpaid'] =  number_format(InvoiceTable::filter($request->all())->where('client_id',$client->id)->whereIn('reference',$referenceType)->get()->sum($column)-InvoiceTable::filter($request->all())->where('client_id',$client->id)->where('set_as_paid', 1)->whereIn('reference',$referenceType)->get()->sum($column), 2, '.', '');
                    $finalData[] = $arr;
                }
            }

            if($request->export){
                $fileName = 'INVOICECLIENTREPORT-'.time().$request->company_id.'.xlsx';
                Excel::store(new InvoiceClientExport($finalData, $request), 'public/xlsx/'.$fileName);
                return response()->json([
                    'status' => true,
                    'url' => url('/storage/xlsx/'.$fileName),
                 ]);
            }
            
            return response()->json([
                "status" => true,
                "data" =>  $finalData
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

        // $referenceType = Reference::where('type', 'Normal Invoice','Refund Invoice')->pluck('prefix')->toArray();
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
        $finalData = [];
        $arr['name'] = \Auth::user()->name;
        $arr['invoiced'] = number_format(InvoiceTable::filter($request->all())->where('agent_id',\Auth::id())->where('reference',$referenceType)->get()->sum($column), 2, '.', '');
        $arr['paid'] = number_format(InvoiceTable::filter($request->all())->where('agent_id',\Auth::id())->where('reference',$referenceType)->get()->sum('amount_paid'), 2, '.', '');
        $arr['Unpaid'] = number_format(InvoiceTable::filter($request->all())->where('agent_id',\Auth::id())->where('reference',$referenceType)->get()->sum('amount_due'), 2, '.', '');

        $finalData[] = $arr;

        if($request->export){
            $fileName = 'INVOICEAGENTSREPORT-'.time().$request->company_id.'.xlsx';
            Excel::store(new InvoiceAgentExport($finalData, $request), 'public/xlsx/'.$fileName);
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }
        
        
        return response()->json([
            "status" => true,
            "data" =>  $finalData
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
        
        $referenceType = Reference::where('type', 'Normal Invoice')->pluck('prefix')->toArray();
        if($request->client_id){
            $invoiceIds = InvoiceTable::where('client_id',$request->client_id)->whereIn('reference',$referenceType)->pluck('id')->toArray();
            $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->whereIn('parent_id',$invoiceIds)->pluck('reference_id')->toArray();
            $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->whereIn('parent_id',$invoiceIds)->pluck('reference_id')->toArray();
        }else{
            $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
            $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
        }

        $products = Product::whereIn('id',$itemProductIds)->get();
        $services = Service::whereIn('id',$itemServiceIds)->get();
        
           
            $arr = [];
            $finalData = [];
        if($request->category == 'catalog'){

            foreach($products as $product){
                $request['product_id'] = $product->id;
                $arr['id'] = $product->id;
                $arr['name'] = $product->name;
                $arr['referenceType'] = $product->reference;
                $arr['category'] = $product->product_category_name;
                $arr['reference'] = $product->reference.''.$product->reference_number;
                // $units = Item::where('reference_id', $product->id)->whereIn('reference',['PRO'])->count();
                $units = InvoiceTable::with(['items'])->WhereHas('items', function ($query) use ($product) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['PRO']);
                })->get()->count('quantity');
                $arr['units'] = $units;
                $arr['amount'] = number_format(InvoiceTable::filter($request->all())->get()->sum('amount'), 2, '.', '');
                

                $finalData[] = $arr;
            }
            foreach($services as $service){
                $request['service_id'] = $service->id;
                $arr['id'] = $service->id;
                $arr['name'] = $service->name;
                $arr['referenceType'] = $service->reference;
                $arr['category'] = $product->product_category_name;
                $arr['reference'] = $service->reference.''.$service->reference_number;
                // $units = Item::where('reference_id', $service->id)->whereIn('reference',['SER'])->count();
                $units = InvoiceTable::with(['items'])->WhereHas('items', function ($query) use ($product) {
                    $query->where('reference_id', $product->id)->whereIn('reference',['SER']);
                })->get()->count('quantity');
                $arr['units'] = $units;
                $arr['amount'] =  number_format(InvoiceTable::filter($request->all())->get()->sum('amount'), 2, '.', '');

                $finalData[] = $arr;
            }
        }else{

            $categories = ProductCategory::get();
            $request['productCategoryNull'] = 1;
            $arr['name'] = 'Not found in catalog';
            $arr['invoiced'] = number_format(InvoiceTable::filter($request->all())->get()->sum('amount'), 2, '.', '');
            $arr['paid'] = number_format(InvoiceTable::filter($request->all())->get()->sum('amount_paid'), 2, '.', '');
            $arr['Unpaid'] = number_format(InvoiceTable::filter($request->all())->get()->sum('amount_due'), 2, '.', '');
            $arr['amount'] =  number_format(InvoiceTable::filter($request->all())->get()->sum('amount'), 2, '.', '');
            unset($request['productCategoryNull']);
            $finalData[] = $arr;
            foreach($categories as $category){
                $request['productCategory'] = $category->id;
                $arr['name'] = $category->name;
                $arr['invoiced'] = number_format(InvoiceTable::filter($request->all())->get()->sum('amount'), 2, '.', '');
                $arr['paid'] = number_format(InvoiceTable::filter($request->all())->get()->sum('amount_paid'), 2, '.', '');
                $arr['Unpaid'] = number_format(InvoiceTable::filter($request->all())->get()->sum('amount_due'), 2, '.', '');
                $arr['amount'] =  number_format(InvoiceTable::filter($request->all())->get()->sum('amount'), 2, '.', '');
                // $arr['units'] = $units;
                $finalData[] = $arr;
            }
        }

        if($request->export){
            $fileName = 'INVOICEITEMSREPORT-'.time().$request->company_id.'.xlsx';
            Excel::store(new InvoiceItemExport($finalData, $request), 'public/xlsx/'.$fileName);
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }

        return response()->json([
            "status" => true,
            "data" =>  $finalData
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

        $finalData = [];
        if( $request->type == "overview" ){
            $finalData = [
                "estimates_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending(". SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'pending')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                         number_format(SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'pending')->get()->sum($taxColumn), 2, '.', '')
                        ] 
                    ], 
                    [
                            "type" => "bar", 
                            "label" => "Refused(". SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'refused')->count().")", 
                            "backgroundColor" => "#FB6363", 
                            "data" => [
                                number_format( SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'refused')->get()->sum($taxColumn), 2, '.', '')
                            ] 
                        ], 
                    [
                        "type" => "bar", 
                        "label" => "Accepted(". SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'accepted')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            number_format( SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'accepted')->get()->sum($taxColumn), 2, '.', ''), 
                        ] 
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed(". SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'closed')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            number_format(SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'closed')->get()->sum($taxColumn), 2, '.', ''),
                        ] 
                    ],
            ], 
                "orders_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending (". SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->where('status', 'pending')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            number_format( SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->where('status', 'pending')->get()->sum($taxColumn), 2, '.', ''),
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Refused (". SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->where('status', 'refused')->count().")", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            number_format( SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->where('status', 'refused')->get()->sum($taxColumn), 2, '.', ''),
                        ]  
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "In Progress (". SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->whereIn('status', ['in progress', 'in_progress'])->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            number_format( SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->whereIn('status', ['in progress', 'in_progress'])->get()->sum($taxColumn), 2, '.', ''),
                        ] 
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed (". SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->where('status', 'closed')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            number_format(SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->where('status', 'closed')->get()->sum($taxColumn), 2, '.', ''),
                        ] 
                    ],
                         
                ], 
                "delivery_notes_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending Invoice (". SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->where('status', 'pending invoice')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            number_format(SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->where('status', 'pending invoice')->get()->sum($taxColumn), 2, '.', ''),
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "In Progress (". SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->whereIn('status', ['in progress', 'in_progress'])->count().")", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            number_format(SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->whereIn('status', ['in progress', 'in_progress'])->get()->sum($taxColumn), 2, '.', ''),
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Closed (". SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->where('status', 'closed')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            number_format(SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->where('status', 'closed')->get()->sum($taxColumn), 2, '.', ''),
                        ] 
                        ],
                    [
                        "type" => "bar", 
                        "label" => "Invoiced (". SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->where('status', 'invoiced')->count().")",
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            number_format(SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->where('status', 'invoiced')->get()->sum($taxColumn), 2, '.', ''),
                        ] 
                    ]  
                ]
            ];

        }elseif( $request->type == "clients" ){
            $finalData = [];
            $finalData['sales_clients'] = [];
            if($request->category == 'client_categories'){

                $categories = ClientCategory::get();
                //    return $categories;
                $arr = [];
                $finalData = [];
                $request['clientCategoryNull'] = 1;
                $finalData['sales_clients'][] = [
                    "type" => "bar",
                    "label" => "No Selected Category",
                    "backgroundColor" => "#26C184",
                    "data" => [
                        number_format(SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($taxColumn), 2, '.', ''),
                    ]
                ];
                unset($request['clientCategoryNull']);
                foreach($categories as $category){
                    $request['clientCategory'] = $category->id;
                    $finalData['sales_clients'][] = [
                        "type" => "bar",
                        "label" => "" .  $category->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                            number_format(SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($taxColumn), 2, '.', ''),
                            ]
                        ];
                }
            }else{
                $client_ids = SalesEstimate::whereHas('client')->pluck('client_id')->toArray();
                $clients = Client::whereIn('id',$client_ids)->get();
                foreach($clients as $client){
                    $finalData['sales_clients'][] = [
                    "type" => "bar",
                    "label" => "" .  $client->legal_name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                        number_format(SalesEstimate::filter($request->all())->where('client_id',$client->id)->whereIn('reference',$referenceType)->get()->sum($taxColumn), 2, '.', ''),
                        ]
                    ];
                }
            }
            return response()->json([
                "status" => true,
                "data" => $finalData
            ]);
            
        }elseif($request->type == "agents"){
            $finalData = [];
            $finalData['sales_agents'] = [];
            $finalData['sales_agents'][] = [
                "type" => "bar",
                "label" => "" .  \Auth::user()->name,
                "backgroundColor" => "#26C184",
                "data" => [
                    number_format(SalesEstimate::filter($request->all())->whereIn('reference', $referenceType)->get()->sum($taxColumn), 2, '.', ''),
                ]
            ];
            return response()->json([
                "status" => true,
                "data" => $finalData
            ]);
        }elseif($request->type == "items"){
            $taxColumn = 'amount_with_out_vat';
            // $referenceType = Reference::whereIn('type', ['Normal Invoice', 'Refund Invoice'])->pluck('prefix')->toArray();
            $invoiceIds = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->pluck('id')->toArray();
            $itemProductIds = Item::whereIn('reference',['PRO'])->whereIn('type', $referenceType)->whereIn('parent_id', $invoiceIds)->pluck('reference_id')->toArray();
            $itemServiceIds = Item::whereIn('reference',['SER'])->whereIn('type', $referenceType)->whereIn('parent_id', $invoiceIds)->pluck('reference_id')->toArray();
            $products = Product::whereIn('id',$itemProductIds)->get();
            $services = Service::whereIn('id',$itemServiceIds)->get();
            $finalData = [];
            $finalData['sales_items'] = [];
            if($request->category == 'product_categories'){
                $categories = ProductCategory::get();
                $finalData = [];
                $request['productCategoryNull'] = 1;
                $finalData['sales_items'][] = [
                    "type" => "bar",
                    "label" => "Not found in catalog
                    ",
                    "backgroundColor" => "#26C184",
                    "data" => [
                        number_format(SalesEstimate::filter($request->all())->whereIn('reference', $referenceType)->get()->sum($taxColumn), 2, '.', ''),
                        ]
                    ];
                unset($request['productCategoryNull']);
                foreach($categories as $category){
                    $request['productCategory'] = $category->id;
                    $finalData['sales_items'][] = [
                        "type" => "bar",
                        "label" => "" .  $category->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                            number_format(SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($taxColumn), 2, '.', ''),
                        ]
                    ];
                }
            }else{
                $finalData['sales_items'][] = [
                    "type" => "bar",
                    "label" => "Not found in catalog",
                    "backgroundColor" => "#26C184",
                    "data" => [
                        number_format(SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->whereDoesntHave('products')->get()->sum($taxColumn), 2, '.', '')
                        ]
                ];
                foreach($products as $product){
                    $request['product_id'] = $product->id;
                    $finalData['sales_items'][] = [
                    "type" => "bar",
                    "label" => "" .  $product->name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                        number_format(SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->get()->sum   ($taxColumn), 2, '.', '')
                        ]
                    ];
                }
                foreach($services as $service){
                    $request['service_id'] = $service->id;
                    $finalData['sales_items'][] = [
                    "type" => "bar",
                    "label" => "" .  $service->name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                        number_format(SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($taxColumn), 2, '.', '')
                        ]
                    ];
                }
            }
            return response()->json([
                "status" => true,
                "data" => $finalData
            ]);

        }

        if($request->export){
            $fileName = 'SALESOVERVIEWREPORT-'.time().$request->company_id.'.xlsx';
            $arr = [];

            $arr['SPendingQuantity'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'pending')->count();
            $arr['SPending'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'pending')->get()->sum($taxColumn);
            $arr['SRefusedQuantity'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'refused')->count();
            $arr['SRefused'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'refused')->get()->sum($taxColumn);
            $arr['SAcceptedQuantity'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'refused')->get()->sum($taxColumn);
            $arr['SAccepted'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'accepted')->get()->sum($taxColumn);
            $arr['SClosedQuantity'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'closed')->count();
            $arr['SClosed'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesEstimatereferenceType)->where('status', 'closed')->get()->sum($taxColumn);
            $arr['OPendingQuantity'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->where('status', 'pending')->count();
            $arr['OPending'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->where('status', 'pending')->get()->sum($taxColumn);
            $arr['ORefusedQuantity'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->where('status', 'refused')->count();
            $arr['ORefused'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->where('status', 'refused')->get()->sum($taxColumn);
            $arr['OProgressQuantity'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->whereIn('status', ['in progress', 'in_progress'])->count();
            $arr['OProgress'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->whereIn('status', ['in progress', 'in_progress'])->get()->sum($taxColumn);
            $arr['OClosedQuantity'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->where('status', 'closed')->count();
            $arr['OClosed'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesOrderreferenceType)->where('status', 'closed')->get()->sum($taxColumn);
            $arr['DPendingInvoiceQuantity'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->where('status', 'pending invoice')->count();
            $arr['DPendingInvoice'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->where('status', 'pending invoice')->get()->sum($taxColumn);
            $arr['DInProgressQuantity'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->whereIn('status', ['in progress', 'in_progress'])->count();
            $arr['DInProgress'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->whereIn('status', ['in progress', 'in_progress'])->get()->sum($taxColumn);
            $arr['DClosedQuantity'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->where('status', 'closed')->count();
            $arr['DClosed'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->where('status', 'closed')->get()->sum($taxColumn);
            $arr['DInvoicedQuantity'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->where('status', 'invoiced')->count();
            $arr['DInvoiced'] = SalesEstimate::filter($request->all())->whereIn('reference', $salesDeliveryNotesreferenceType)->where('status', 'invoiced')->get()->sum($taxColumn);


            $finalData = $arr;
            Excel::store(new SalesOverViewExport($finalData, $request), 'public/xlsx/'.$fileName);

            
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }

        return response()->json([
            "status" => true,
            "data" =>  $finalData
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
        $finalData = [];

        if($request->category == 'client_categories'){
            $request['clientCategoryNull'] = 1;
            $arr['name'] = "No Selected Category";
            $arr['pending'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->where('status','pending')->count();
            $arr['refused'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->where('status','refused')->count();
            $arr['accepted'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->where('status','accepted')->count();
            $arr['closed'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->where('status','closed')->count();
            $arr['total'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->count();
            $arr['amount'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->get()->sum($column);

            $finalData[] = $arr;
            unset($request['clientCategoryNull']);
            $categories = ClientCategory::get();
            foreach($categories as $category){
                $request['clientCategory'] = $category->id;
                $arr['name'] = $category->name;
                $arr['pending'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->whereIn('status',['pending', 'in_progress','pending_invoice','invoiced'])->count();
                $arr['refused'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->where('status','refused')->count();
                $arr['accepted'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->where('status','accepted')->count();
                $arr['closed'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->where('status','closed')->count();
                $arr['total'] = SalesEstimate::filter($request->all())->where('reference',$referenceType)->count();
                $arr['amount'] = number_format(SalesEstimate::filter($request->all())->where('reference',$referenceType)->get()->sum($column), 2, '.', '');

                $finalData[] = $arr;
            }
        }else{
            $client_ids = SalesEstimate::whereHas('client')->where('reference',$referenceType)->pluck('client_id')->toArray();
            $clients = Client::whereIn('id',$client_ids)->get();
            foreach($clients as $client){
                $arr['name'] = $client->legal_name;
                $arr['reference'] = $client->reference.''.$client->reference_number;
                $arr['tin'] = $client->tin;
                $arr['category'] = $client->client_category_name;
                $arr['pending'] = SalesEstimate::filter($request->all())->where('client_id',$client->id)->where('reference',$referenceType)->whereIn('status',['pending', 'in_progress','pending_invoice','invoiced'])->count();
                $arr['accepted'] = SalesEstimate::filter($request->all())->where('client_id',$client->id)->where('reference',$referenceType)->where('status','accepted')->count();
                $arr['refused'] = SalesEstimate::filter($request->all())->where('client_id',$client->id)->where('reference',$referenceType)->where('status','refused')->count();
                $arr['in_progress'] = SalesEstimate::filter($request->all())->where('client_id',$client->id)->where('reference',$referenceType)->where('status','in_progress')->count();
                $arr['closed'] = SalesEstimate::filter($request->all())->where('client_id',$client->id)->where('reference',$referenceType)->where('status','closed')->count();
                $arr['total'] = SalesEstimate::filter($request->all())->where('client_id',$client->id)->where('reference',$referenceType)->count();
                $arr['amount'] = number_format(SalesEstimate::filter($request->all())->where('client_id',$client->id)->where('reference',$referenceType)->get()->sum($column), 2, '.', '');

                $finalData[] = $arr;
            }
        }
        if($request->export){
            $fileName = 'CLIENTSALESREPORT-'.time().$request->company_id.'.xlsx';

            Excel::store(new SalesClientExport($finalData, $request), 'public/xlsx/'.$fileName);
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }
        return response()->json([
            "status" => true,
            "data" =>  $finalData
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

        // if($request->client){
        //     $client = Client::where('id', $request->client_id)->first();
        //     $clientName = $client->legal_name;
            
        // }
        // echo "Client name: " . $clientName;die;

        $arr = [];
        $finalData = [];

        $arr['name'] = \Auth::user()->name;
        // $arr['clientName'] = $client->reference.''.$client->reference_number.' '.$client->legal_name;
        $arr['pending'] = SalesEstimate::filter($request->all())->where('agent_id',\Auth::id())->where('reference',$referenceType)->where('status','pending')->count();
        $arr['refused'] = SalesEstimate::filter($request->all())->where('agent_id',\Auth::id())->where('reference',$referenceType)->where('status','refused')->count();
        $arr['accepted'] = SalesEstimate::filter($request->all())->where('agent_id',\Auth::id())->where('reference',$referenceType)->where('status','accepted')->count();
        $arr['closed'] = SalesEstimate::filter($request->all())->where('agent_id',\Auth::id())->where('reference',$referenceType)->where('status','closed')->count();
        $arr['total'] = SalesEstimate::filter($request->all())->where('agent_id',\Auth::id())->where('reference',$referenceType)->count();
        $arr['amount'] = number_format(SalesEstimate::filter($request->all())->where('agent_id',\Auth::id())->where('reference',$referenceType)->get()->sum($column), 2, '.', '');
        $finalData[] = $arr;

        if($request->export){
            $fileName = 'CLIENTSALESREPORT-'.time().$request->company_id.'.xlsx';

            Excel::store(new SalesAgentsExport($finalData, $request), 'public/xlsx/'.$fileName);
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }

        return response()->json([
            "status" => true,
            "data" =>  $finalData
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
            $invoiceIds = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->pluck('id')->toArray();
            $itemProductIds = Item::whereIn('reference',['PRO'])->whereIn('type', $referenceType)->whereIn('parent_id', $invoiceIds)->pluck('reference_id')->toArray();
            $itemServiceIds = Item::whereIn('reference',['SER'])->whereIn('type', $referenceType)->whereIn('parent_id', $invoiceIds)->pluck('reference_id')->toArray();
        }

        $products = Product::whereIn('id',$itemProductIds)->get();
        $services = Service::whereIn('id',$itemServiceIds)->get();
           
            $arr = [];
            $finalData = [];
            if($request->category == 'product_categories'){
                $categories = ProductCategory::get();
                //    return $categories;
                $arr = [];
                $finalData = [];
                $request['productCategoryNull'] = 1;
                $arr['name'] = 'No Selected Category';
                $arr['reference'] = '';
                $arr['pending'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->count();
                $arr['accepted'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','accepted')->count();
                $arr['closed'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','closed')->count();
                $arr['refused'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','refused')->count();
                $arr['total'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->count();
                $arr['units'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->count();
                $arr['amount'] = number_format(SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_with_out_vat'), 2, '.', '');
                $finalData[] = $arr;
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
                    $arr['amount'] =  number_format(SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_with_out_vat'), 2, '.', '');
                    $finalData[] = $arr;
                }
            }else{
                $arr['name'] = 'Not Found in Catalog';
                $arr['reference'] = '-';
                $arr['pending'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->whereDoesntHave('products')->where('status','pending')->count();
                $arr['accepted'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->whereDoesntHave('products')->where('status','accepted')->count();
                $arr['closed'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->whereDoesntHave('products')->where('status','closed')->count();
                $arr['refused'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->whereDoesntHave('products')->where('status','refused')->count();
                $arr['total'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->whereDoesntHave('products')->count();
                $arr['units'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->whereDoesntHave('products')->count();
                $arr['amount'] = number_format(SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->whereDoesntHave('products')->get()->sum('amount_with_out_vat'), 2, '.', '');
                $finalData[] = $arr;
                foreach($products as $product){
                    $request['product_id'] = $product->id;
                    $arr['name'] = $product->name;
                    $arr['reference'] = $product->reference.''.$product->reference_number;
                    $arr['category'] = $product->product_category_name;
                    $arr['pending'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->count();
                    $arr['accepted'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','accepted')->count();
                    $arr['closed'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','closed')->count();
                    $arr['refused'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','refused')->count();
                    $arr['total'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->count();
                    $arr['units'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->count();
                    $arr['amount'] = number_format(SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_with_out_vat'), 2, '.', '');
                    $finalData[] = $arr;
                }
                foreach($services as $service){
                    $request['service_id'] = $service->id;
                    $arr['name'] = $service->name;
                    $arr['reference'] = $service->reference.''.$service->reference_number;
                    $arr['category'] = $product->product_category_name;
                    $arr['pending'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','pending')->count();
                    $arr['accepted'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','accepted')->count();
                    $arr['closed'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','closed')->count();
                    $arr['refused'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->where('status','refused')->count();
                    $arr['total'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->count();
                    $arr['units'] = SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->count();
                    $arr['amount'] = number_format(SalesEstimate::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_with_out_vat'), 2, '.', '');
    
                    $finalData[] = $arr;
                }
            }
            if($request->export){
                $fileName = 'ITEMSSALESREPORT-'.time().$request->company_id.'.xlsx';
    
                Excel::store(new SalesItemsExport($finalData, $request), 'public/xlsx/'.$fileName);
                return response()->json([
                    'status' => true,
                    'url' => url('/storage/xlsx/'.$fileName),
                 ]);
            }

        return response()->json([
            "status" => true,
            "data" =>  $finalData
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
                            number_format(TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'pending')->get()->sum($taxColumn), 2, '.', '')
                            
                        ]
                    ], 
                    [
                            "type" => "bar", 
                            "label" => "Refused (". TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'refused')->count().")",
                            "backgroundColor" => "#FB6363", 
                            "data" => [
                                number_format(TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'refused')->get()->sum($taxColumn), 2, '.', '')
                                
                            ] 
                        ], 
                    [
                        "type" => "bar", 
                        "label" => "Accepted (". TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'accepted')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            number_format(TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'accepted')->get()->sum($taxColumn), 2, '.', '')
                            
                        ]
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed (". TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'closed')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            number_format(TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'closed')->get()->sum($taxColumn), 2, '.', '')
                            
                        ]
                    ],
                ], 
                "orders_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending (". TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'pending')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            number_format(TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'pending')->get()->sum($taxColumn), 2, '.', '')
                            
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Refused (". TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'refused')->count().")",  
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            number_format(TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'refused')->get()->sum($taxColumn), 2, '.', '')
                            
                        ]  
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "In Progress (". TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'in_progress')->count().")",
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            number_format(TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'in_progress')->get()->sum($taxColumn), 2, '.', '')
                            
                        ] 
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Closed (". TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'closed')->count().")",
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            number_format(TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'closed')->get()->sum($taxColumn), 2, '.', '')
                            
                        ] 
                    ],
                         
                ], 
                "delivery_notes_by_state" => [
                    [
                        "type" => "bar", 
                        "label" => "Pending Invoice (". TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'pending invoice')->count().")", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            number_format(TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'pending invoice')->get()->sum($taxColumn), 2, '.', '')
                            
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "In Progress (". TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'In Progress')->count().")", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            number_format(TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'In Progress')->get()->sum($taxColumn), 2, '.', '')
                            
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Closed (". TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'closed')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            number_format(TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'closed')->get()->sum($taxColumn), 2, '.', '')
                            
                        ] 
                    ],
                    [
                        "type" => "bar", 
                        "label" => "Invoiced  (". TechnicalTable::filter($request->all())->where('reference', $workDeliveryNotesReference)->where('status', 'Invoiced')->count().")", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            number_format(TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'invoiced')->get()->sum($taxColumn), 2, '.', '')
                            
                        ] 
                    ]  
                ]
            ];

        }elseif($request->type == "incidents_by_client"){
            if($request->reference){
                $referenceType = [$request->reference];
            }else{
    
                $referenceType = Reference::where('type', 'Incident')->pluck('prefix')->toArray();
            }
            // return  $referenceType; 
            $statusesArr = ['Pending', 'Refused', 'Resolved','Closed'];
            if($request->category == 'client_categories'){
                $categories =  ClientCategory::get();
                $tempData = [];
                $clients = Client::get();
                foreach($statusesArr as $status){

                    $tempData['labels'][] =  $status;
                    $tempData['no_client'][] =  TechnicalIncident::filter(['agent_id' =>$request->agent_id, 'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereIn('reference',$referenceType)->whereDoesntHave('client')->where('status', $status)->count();
                    $tempData['no_category'][] =  TechnicalIncident::filter(['agent_id' =>$request->agent_id,'clientCategoryNull' => 1,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereIn('reference',$referenceType)->where('status', $status)->count();
                    foreach($categories as $category){
                        $tempData[$category->id][] =  TechnicalIncident::filter([ 'agent_id' =>$request->agent_id,'clientCategory' => $category->id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', $status)->count();
                    }
                }
                $data['labels'] = @$tempData['labels'];
                $data['data'][] = 
                [
                    "label" => 'Without Client',
                    "fill" => true,
                    "lineTension" => 0.5,
                    "backgroundColor" => generateRandomColor(),
                    "borderColor" => "#3c4ccf",
                    "borderCapStyle" => "butt",
                    "borderDash" => [],
                    "borderDashOffset" => 0.0,
                    "borderJoinStyle" => "miter",
                    "pointBorderColor" => "#3c4ccf",
                    "pointBackgroundColor" => "#fff",
                    "pointBorderWidth" => 1,
                    "pointHoverRadius" => 5,
                    "pointHoverBackgroundColor" => "#3c4ccf",
                    "pointHoverBorderColor" => "#fff",
                    "pointHoverBorderWidth" => 2,
                    "pointRadius" => 1,
                    "pointHitRadius" => 1000,
                    "data" => @$tempData['no_client']
                ];
                $data['data'][] = 
                [
                    "label" => 'No Selected Category',
                    "fill" => true,
                    "lineTension" => 0.5,
                    "backgroundColor" => generateRandomColor(),
                    "borderColor" => "#3c4ccf",
                    "borderCapStyle" => "butt",
                    "borderDash" => [],
                    "borderDashOffset" => 0.0,
                    "borderJoinStyle" => "miter",
                    "pointBorderColor" => "#3c4ccf",
                    "pointBackgroundColor" => "#fff",
                    "pointBorderWidth" => 1,
                    "pointHoverRadius" => 5,
                    "pointHoverBackgroundColor" => "#3c4ccf",
                    "pointHoverBorderColor" => "#fff",
                    "pointHoverBorderWidth" => 2,
                    "pointRadius" => 1,
                    "pointHitRadius" => 1000,
                    "data" => @$tempData['no_category']
                ];
                foreach($categories as $category){
    
                    $data['data'][] = 
                    [
                        "label" => $category->name,
                        "fill" => true,
                        "lineTension" => 0.5,
                        "backgroundColor" => generateRandomColor(),
                        "borderColor" => "#3c4ccf",
                        "borderCapStyle" => "butt",
                        "borderDash" => [],
                        "borderDashOffset" => 0.0,
                        "borderJoinStyle" => "miter",
                        "pointBorderColor" => "#3c4ccf",
                        "pointBackgroundColor" => "#fff",
                        "pointBorderWidth" => 1,
                        "pointHoverRadius" => 5,
                        "pointHoverBackgroundColor" => "#3c4ccf",
                        "pointHoverBorderColor" => "#fff",
                        "pointHoverBorderWidth" => 2,
                        "pointRadius" => 1,
                        "pointHitRadius" => 1000,
                        "data" => @$tempData[$category->id]
                    ];
                }
            }else{
                $tempData = [];
                $clients = Client::get();
                foreach($statusesArr as $status){

                    $tempData['labels'][] =  $status;
                    $tempData['no_client'][] =  TechnicalIncident::filter(['agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereIn('reference',$referenceType)->whereDoesntHave('client')->where('status', $status)->count();
                    foreach($clients as $client){
                        $tempData[$client->id][] =  TechnicalIncident::filter(['client_id' => $client->id, 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', $status)->count();
                    }
                }
                $data['labels'] = @$tempData['labels'];
                $data['data'][] = 
                [
                    "label" => 'Without Client',
                    "fill" => true,
                    "lineTension" => 0.5,
                    "backgroundColor" => generateRandomColor(),
                    "borderColor" => "#3c4ccf",
                    "borderCapStyle" => "butt",
                    "borderDash" => [],
                    "borderDashOffset" => 0.0,
                    "borderJoinStyle" => "miter",
                    "pointBorderColor" => "#3c4ccf",
                    "pointBackgroundColor" => "#fff",
                    "pointBorderWidth" => 1,
                    "pointHoverRadius" => 5,
                    "pointHoverBackgroundColor" => "#3c4ccf",
                    "pointHoverBorderColor" => "#fff",
                    "pointHoverBorderWidth" => 2,
                    "pointRadius" => 1,
                    "pointHitRadius" => 1000,
                    "data" => @$tempData['no_client']
                ];
                foreach($clients as $client){
    
                    $data['data'][] = 
                    [
                        "label" => $client->legal_name.' ('.$client->name.')',
                        "fill" => true,
                        "lineTension" => 0.5,
                        "backgroundColor" => generateRandomColor(),
                        "borderColor" => "#3c4ccf",
                        "borderCapStyle" => "butt",
                        "borderDash" => [],
                        "borderDashOffset" => 0.0,
                        "borderJoinStyle" => "miter",
                        "pointBorderColor" => "#3c4ccf",
                        "pointBackgroundColor" => "#fff",
                        "pointBorderWidth" => 1,
                        "pointHoverRadius" => 5,
                        "pointHoverBackgroundColor" => "#3c4ccf",
                        "pointHoverBorderColor" => "#fff",
                        "pointHoverBorderWidth" => 2,
                        "pointRadius" => 1,
                        "pointHitRadius" => 1000,
                        "data" => @$tempData[$client->id]
                    ];
                }
               
            }
        }elseif($request->type == "incidents_by_agent"){
            if($request->reference){
                $referenceType = [$request->reference];
            }else{
    
                $referenceType = Reference::where('type', 'Incident')->pluck('prefix')->toArray();
            }
            $statusesArr = ['Pending', 'Refused', 'Resolved','Closed'];
            foreach($statusesArr as $status){

                $tempData['labels'][] =  $status;
                $tempData['no_client'][] =  TechnicalIncident::filter(['client_id' =>$request->client_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereIn('reference',$referenceType)->whereNull('assigned_to')->where('status', $status)->count();
                
                $tempData[\Auth::id()][] =  TechnicalIncident::filter(['client_id' => $request->client_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('assigned_to', \Auth::id())->where('reference',$referenceType)->where('status', $status)->count();
            }
            $data['labels'] = @$tempData['labels'];
            $data['data'][] = 
            [
                "label" => 'Unassigned',
                "fill" => true,
                "lineTension" => 0.5,
                "backgroundColor" => generateRandomColor(),
                "borderColor" => "#3c4ccf",
                "borderCapStyle" => "butt",
                "borderDash" => [],
                "borderDashOffset" => 0.0,
                "borderJoinStyle" => "miter",
                "pointBorderColor" => "#3c4ccf",
                "pointBackgroundColor" => "#fff",
                "pointBorderWidth" => 1,
                "pointHoverRadius" => 5,
                "pointHoverBackgroundColor" => "#3c4ccf",
                "pointHoverBorderColor" => "#fff",
                "pointHoverBorderWidth" => 2,
                "pointRadius" => 1,
                "pointHitRadius" => 1000,
                "data" => @$tempData['no_client']
            ];

            $data['data'][] = 
            [
                "label" => \Auth::user()->name,
                "fill" => true,
                "lineTension" => 0.5,
                "backgroundColor" => generateRandomColor(),
                "borderColor" => "#3c4ccf",
                "borderCapStyle" => "butt",
                "borderDash" => [],
                "borderDashOffset" => 0.0,
                "borderJoinStyle" => "miter",
                "pointBorderColor" => "#3c4ccf",
                "pointBackgroundColor" => "#fff",
                "pointBorderWidth" => 1,
                "pointHoverRadius" => 5,
                "pointHoverBackgroundColor" => "#3c4ccf",
                "pointHoverBorderColor" => "#fff",
                "pointHoverBorderWidth" => 2,
                "pointRadius" => 1,
                "pointHitRadius" => 1000,
                "data" => @$tempData[\Auth::id()]
            ];
        }
        if($request->export){
            $fileName = 'TECHNICALSERVICEOVERVIEWREPORT-'.time().$request->company_id.'.xlsx';
            $arr = [];

            $arr['IPendingQuantity'] = TechnicalIncident::filter($request->all())->whereIn('reference',$technicalIncidentReference)->where('status', 'pending')->count();
            $arr['IRefused'] = TechnicalIncident::filter($request->all())->whereIn('reference',$technicalIncidentReference)->where('status', 'refused')->count();
            $arr['IResolved'] = TechnicalIncident::filter($request->all())->whereIn('reference',$technicalIncidentReference)->where('status', 'resolved')->count();
            $arr['IClosed'] = TechnicalIncident::filter($request->all())->whereIn('reference',$technicalIncidentReference)->where('status', 'closed')->count();
            $arr['EPendingQuantity'] = TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'pending')->count();
            $arr['EPending'] = TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'pending')->get()->sum($taxColumn);
            $arr['ERefusedQuantity'] = TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'refused')->count();
            $arr['ERefused'] = TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'refused')->get()->sum($taxColumn);
            $arr['EAcceptedQuantity'] = TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'accepted')->count();
            $arr['EAccepted'] = TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'accepted')->get()->sum($taxColumn);
            $arr['EClosedQuantity'] = TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'closed')->count();
            $arr['EClosed'] = TechnicalTable::filter($request->all())->whereIn('reference', $workEstimatesReference )->where('status', 'closed')->get()->sum($taxColumn);
            $arr['OPendingQuantity'] = TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'pending')->count();
            $arr['OPending'] = TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'pending')->get()->sum($taxColumn);
            $arr['ORefusedQuantity'] = TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'refused')->count();
            $arr['ORefused'] = TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'refused')->get()->sum($taxColumn);
            $arr['OInProgressQuantity'] = TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'in_progress')->count();
            $arr['OInProgress'] = TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'in_progress')->get()->sum($taxColumn);
            $arr['OClosedQuantity'] = TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'closed')->count();
            $arr['OClosed'] = TechnicalTable::filter($request->all())->whereIn('reference', $workOrderReference)->where('status', 'closed')->get()->sum($taxColumn);
            $arr['DPendingInvoiceQuantity'] = TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'pending invoice')->count();
            $arr['PPendingInvoice'] = TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'pending invoice')->get()->sum($taxColumn);
            $arr['DInProgressQuantity'] = TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'In Progress')->count();
            $arr['DInProgress'] = TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'In Progress')->get()->sum($taxColumn);
            $arr['DClosedQuantity'] = TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'closed')->count();
            $arr['DClosed'] = TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'closed')->get()->sum($taxColumn);
            $arr['DInvoicedQuantity'] = TechnicalTable::filter($request->all())->where('reference', $workDeliveryNotesReference)->where('status', 'Invoiced')->count();
            $arr['DInvoiced'] = TechnicalTable::filter($request->all())->whereIn('reference',$workDeliveryNotesReference)->where('status', 'invoiced')->get()->sum($taxColumn);


            $data = $arr;
            Excel::store(new TechnicalOverViewExport($data, $request), 'public/xlsx/'.$fileName); 

            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }

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
        $categoryTable = 'company_'.$request->company_id.'_client_categories';
        ClientCategory::setGlobalTable($categoryTable);
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
            $finalData = [];
            if($request->reference){
                $referenceType = [$request->reference];
            }else{
    
                $referenceType = Reference::where('type', 'Incident')->pluck('prefix')->toArray();
            }
            // return  $referenceType; 
            if($request->category == 'client_categories'){
                $categories =  ClientCategory::get();
                $arr['name'] = 'No category Selected';
                $arr['pending'] = TechnicalIncident::filter(['clientCategoryNull' => 1, 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', 'pending')->count();
                $arr['resolved'] = TechnicalIncident::filter(['clientCategoryNull' => 1, 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', 'resolved')->count();
                $arr['refused'] = TechnicalIncident::filter(['clientCategoryNull' => 1, 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', 'refused')->count();
                $arr['accepted'] = TechnicalIncident::filter(['clientCategoryNull' => 1, 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', 'accepted')->count();
                $arr['closed'] = TechnicalIncident::filter(['clientCategoryNull' => 1, 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', 'closed')->count();
                $arr['total'] = TechnicalIncident::filter(['clientCategoryNull' => 1, 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->count();
    
                $finalData[] = $arr;
                $arr['name'] = 'Without Client';
                $arr['pending'] = TechnicalIncident::filter([ 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', 'pending')->whereDoesntHave('client')->count();
                $arr['resolved'] = TechnicalIncident::filter([ 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', 'resolved')->whereDoesntHave('client')->count();

                $arr['refused'] = TechnicalIncident::filter([ 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', 'refused')->whereDoesntHave('client')->count();

                $arr['accepted'] = TechnicalIncident::filter([ 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->whereDoesntHave('client')->where('status', 'accepted')->count();
                $arr['closed'] = TechnicalIncident::filter([ 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', 'closed')->whereDoesntHave('client')->count();
                $arr['total'] = TechnicalIncident::filter(['agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereDoesntHave('client')->count();
    
                $finalData[] = $arr;
                foreach($categories as $category){
    
                    $arr['name'] = $category->name;
                    $arr['pending'] = TechnicalIncident::filter(['clientCategory' => $category->id, 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', 'pending')->count();
                    $arr['resolved'] = TechnicalIncident::filter(['clientCategory' => $category->id, 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', 'resolved')->count();
                    $arr['refused'] = TechnicalIncident::filter(['clientCategory' => $category->id, 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', 'refused')->count();
                    $arr['accepted'] = TechnicalIncident::filter(['clientCategory' => $category->id, 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', 'accepted')->count();
                    $arr['closed'] = TechnicalIncident::filter(['clientCategory' => $category->id, 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', 'closed')->count();
                    $arr['total'] = TechnicalIncident::filter(['clientCategory' => $category->id, 'agent_id' =>$request->agent_id ])->count();
        
                    $finalData[] = $arr;
                }
            }else{
                $clients = Client::get();
                $arr['name'] = 'Without Client';
                $arr['pending'] = TechnicalIncident::filter(['agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereDoesntHave('client')->where('reference',$referenceType)->where('status', 'pending')->count();
                $arr['resolved'] = TechnicalIncident::filter(['agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereDoesntHave('client')->where('reference',$referenceType)->where('status', 'resolved')->count();
                $arr['refused'] = TechnicalIncident::filter(['agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereDoesntHave('client')->where('reference',$referenceType)->where('status', 'refused')->count();
                $arr['accepted'] = TechnicalIncident::filter(['agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereDoesntHave('client')->where('reference',$referenceType)->where('status', 'accepted')->count();
                $arr['closed'] = TechnicalIncident::filter(['agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereDoesntHave('client')->where('reference',$referenceType)->where('status', 'closed')->count();
                $arr['total'] = TechnicalIncident::filter(['agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereDoesntHave('client')->count();
    
                $finalData[] = $arr;
                foreach($clients as $client){
    
                    
                    $arr['name'] = $client->legal_name.' ('.$client->name.')';
                    $arr['reference'] = $client->reference.''.$client->reference_number;
                    $arr['ruc'] = $client->tin;
                    $arr['pending'] = TechnicalIncident::filter(['client_id' => $client->id, 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', 'pending')->count();
                    $arr['resolved'] = TechnicalIncident::filter(['client_id' => $client->id, 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', 'resolved')->count();
                    $arr['refused'] = TechnicalIncident::filter(['client_id' => $client->id, 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', 'refused')->count();
                    $arr['accepted'] = TechnicalIncident::filter(['client_id' => $client->id, 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', 'accepted')->count();
                    $arr['closed'] = TechnicalIncident::filter(['client_id' => $client->id, 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->where('reference',$referenceType)->where('status', 'closed')->count();
                    $arr['total'] = TechnicalIncident::filter(['client_id' => $client->id, 'agent_id' =>$request->agent_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->count();
        
                    $finalData[] = $arr;
                }
               
            }
            if($request->export){
                $fileName = 'CLIENTINCIDENTSTECHNICALSERVICEREPORT-'.time().$request->company_id.'.xlsx';
    
                Excel::store(new IncidentsByClientExport($finalData, $request), 'public/xlsx/'.$fileName);
                return response()->json([
                    'status' => true,
                    'url' => url('/storage/xlsx/'.$fileName),
                 ]);
            }
            
            return response()->json([
                "status" => true,
                "data" =>  $finalData
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

        if($request->reference){
            $referenceType = [$request->reference];
        }else{

            $referenceType = Reference::where('type', 'Incident')->pluck('prefix')->toArray();
        };
        $arr = [];
        $finalData = [];

        $arr['name'] = 'Unassigned';
        $arr['pending'] = TechnicalIncident::filter(['client_id' =>$request->client_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereIn('reference',$referenceType)->whereNull('assigned_to')->where('status', 'Pending')->count();
        $arr['refused'] = TechnicalIncident::filter(['client_id' =>$request->client_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereIn('reference',$referenceType)->whereNull('assigned_to')->where('status', 'Refused')->count();
        $arr['resolved'] = TechnicalIncident::filter(['client_id' =>$request->client_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereIn('reference',$referenceType)->whereNull('assigned_to')->where('status', 'Resolved')->count();
        $arr['closed'] = TechnicalIncident::filter(['client_id' =>$request->client_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereIn('reference',$referenceType)->whereNull('assigned_to')->where('status', 'Closed')->count();
        $arr['total'] = TechnicalIncident::filter(['client_id' =>$request->client_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereIn('reference',$referenceType)->whereNull('assigned_to')->count();

        $finalData[] = $arr;
        $arr['name'] = \Auth::user()->name;
        $arr['pending'] = TechnicalIncident::filter(['client_id' => $request->client_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereIn('reference',$referenceType)->where('assigned_to',\Auth::id())->where('status', 'Pending')->count();
        $arr['refused'] = TechnicalIncident::filter(['client_id' =>$request->client_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereIn('reference',$referenceType)->where('assigned_to',\Auth::id())->where('status', 'Refused')->count();
        $arr['resolved'] = TechnicalIncident::filter(['client_id' =>$request->client_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereIn('reference',$referenceType)->where('assigned_to',\Auth::id())->where('status', 'resolved')->count();
        $arr['closed'] = TechnicalIncident::filter(['client_id' =>$request->client_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereIn('reference',$referenceType)->where('assigned_to',\Auth::id())->where('status', 'Closed')->count();
        $arr['total'] = TechnicalIncident::filter(['client_id' =>$request->client_id,'startDate' => $request->startDate,'endDate' => $request->endDate,'year' => $request->year ])->whereIn('reference',$referenceType)->where('assigned_to',\Auth::id())->count();

        $finalData[] = $arr;
        

        if($request->export){
            $fileName = 'EMPLOYEEINCIDENTSTECHNICALSERVICEREPORT-'.time().$request->company_id.'.xlsx';

            Excel::store(new IncidentsByAgentExport($finalData, $request), 'public/xlsx/'.$fileName);
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }

        return response()->json([
            "status" => true,
            "data" =>  $finalData
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

            $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
        }

        $finalData = [];
        if($request->type == 'incident_by_client'){
            $finalData['incident_clients'] = [];
            if($request->category == 'client_categories'){
                $categories = ClientCategory::get();
                $request['clientCategoryNull'] = 1;
                $finalData['incident_clients'][] = [
                    "type" => "bar",
                    "label" => "No Selected Category",
                    "backgroundColor" => "#26C184",
                    "data" => [
                        number_format(TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($taxColumn), 2, '.', ''),
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
                            number_format(TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($taxColumn), 2, '.', ''),
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
                        number_format(TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('client_id',$client->id)->get()->sum($taxColumn), 2, '.', ''),
                        ]
                    ];
                }
            }    
        }elseif($request->type == 'by_client_history'){
            $arr = [];
            $finalData = [];
            if($request->category == 'client_categories'){
                $categories = ClientCategory::get();
                $request['clientCategoryNull'] = 1;
                $arr['name'] = "No Selected Category";
                $arr['pending'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->whereIn('status',['pending','pending invoice'])->count();
                $arr['refused'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','refused')->count();
                $arr['accepted'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','accepted')->count();
                $arr['closed'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','closed')->count();
                $arr['total'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                $arr['amount'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($taxColumn);

                $finalData[] = $arr;
                unset($request['clientCategoryNull']);
                foreach($categories as $category){
                    $request['clientCategory'] = $category->id;
                    $arr['name'] = $category->name;
                    $arr['pending'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->whereIn('status',['pending','pending invoice'])->count();
                    $arr['refused'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','refused')->count();
                    $arr['accepted'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','accepted')->count();
                    $arr['closed'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','closed')->count();
                    $arr['total'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                    $arr['amount'] = number_format(TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($taxColumn), 2, '.', '');
    
                    $finalData[] = $arr;
                }
            }
            else{

                $client_ids = TechnicalTable::pluck('client_id')->toArray();
                $clients = Client::whereIn('id',$client_ids)->get();
    
                foreach($clients as $client){
                    $arr['name'] = $client->legal_name;
                    $arr['reference'] = $client->reference.''.$client->reference_number;
                    $arr['ruc'] = $client->tin;
                    $arr['category'] = $client->client_category_name;
                    $arr['pending'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('client_id',$client->id)->whereIn('status',['pending','pending invoice'])->count();
                    $arr['refused'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('client_id',$client->id)->where('status','refused')->count();
                    $arr['accepted'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('client_id',$client->id)->where('status','accepted')->count();
                    $arr['closed'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('client_id',$client->id)->where('status','closed')->count();
                    $arr['total'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('client_id',$client->id)->count();
                    $arr['amount'] = number_format(TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('client_id',$client->id)->get()->sum($taxColumn), 2, '.', '');
    
                    $finalData[] = $arr;
                }
            }    
            
        }
        if($request->export){
            $fileName = 'CLIENTTECHNICALSERVICEREPORT-'.time().$request->company_id.'.xlsx';

            Excel::store(new IncidentByClientExport($finalData, $request), 'public/xlsx/'.$fileName);
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }
        return response()->json([
            "status" => true,
            "data" =>  $finalData
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

            $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
        }

        $finalData = [];
        if($request->type == 'incident_by_agent'){
            $finalData['incident_agent'] = [];
            $finalData['incident_agent'][] = [
            "type" => "bar",
            "label" => "" . \Auth::user()->name,
            "backgroundColor" => "#26C184",
            "data" => [
                    number_format(TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->get()->sum( $taxColumn), 2, '.', ''),
                ]
            ];
        }elseif($request->type == 'by_agent_history'){

            // $referenceType = Reference::where('type', $request->referenceType)->pluck('prefix')->toArray();
            $arr = [];
            $finalData = [];

            $arr['name'] = \Auth::user()->name;
            $arr['pending'] = TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->where('agent_id',\Auth::id())->whereIn('status',['pending','pending invoice'])->count();
            $arr['refused'] = TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->where('agent_id',\Auth::id())->where('status','refused')->count();
            $arr['accepted'] = TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->where('agent_id',\Auth::id())->where('status','accepted')->count();
            $arr['closed'] = TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->where('agent_id',\Auth::id())->where('status','closed')->count();
            $arr['total'] = TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->where('agent_id',\Auth::id())->count();
            $arr['amount'] = number_format(TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->where('agent_id',\Auth::id())->get()->sum($taxColumn), 2, '.', '');

                $finalData[] = $arr;
            
        }

        if($request->export){
            $fileName = 'EMPLOYEETECHNICALSERVICEREPORT-'.time().$request->company_id.'.xlsx';

            Excel::store(new IncidentByAgentExport($finalData, $request), 'public/xlsx/'.$fileName);
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }

        return response()->json([
            "status" => true,
            "data" =>  $finalData
        ]);
            
    }
    public function technicalByItem(Request $request){
        $salesTables = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($salesTables);

        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables);

        $table = 'company_'.$request->company_id.'_technical_incidents';
        TechnicalIncident::setGlobalTable($table);

        $serviceTable = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($serviceTable);

        $productTable = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($productTable);

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
        $finalData = [];
        if($request->type == 'incident_by_item'){
            
            $itemProductIds = Item::whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
            $itemServiceIds = Item::whereIn('reference',['SER'])->pluck('reference_id')->toArray();
            $products = Product::whereIn('id',$itemProductIds)->get();
            $services = Service::whereIn('id',$itemServiceIds)->get();

            if($request->category == 'catalog'){

                $finalData['invoice_items'] = [];
                foreach($products as $product){
                    $request['product_id'] = $product->id;
                    $finalData['invoice_items'][] = [
                    "type" => "bar",
                    "label" => "" .  $product->name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                            number_format(TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->get()->sum('amount'), 2, '.', ''),
                        ]
                    ];
                }
                foreach($services as $service){
                    $request['service_id'] = $service->id;
                    $finalData['invoice_items'][] = [
                    "type" => "bar",
                    "label" => "" .  $service->name,
                    "backgroundColor" => "#26C184",
                    "data" => [
                            number_format(TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->get()->sum('amount'), 2, '.', ''),
                        ]
                    ];
                }
            }else{
                $categories = ProductCategory::get();
                $request['productCategoryNull'] = 1;
                $arr['name'] = 'Not found in catalog';
                $finalData['invoice_items'][] = [
                "type" => "bar",
                "label" => 'Not found in catalog',
                "backgroundColor" => "#26C184",
                "data" => [
                        number_format(TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->get()->sum('amount'), 2, '.', ''),
                    ]
                ];
                unset($request['productCategoryNull']);
                foreach($categories as $category){
                    $request['productCategory'] = $category->id;
                    $finalData['invoice_items'][] = [
                        "type" => "bar",
                        "label" => "" .  $category->name,
                        "backgroundColor" => "#26C184",
                        "data" => [
                            number_format(TechnicalTable::filter($request->all())->whereIn('reference', $referenceType)->get()->sum('amount'), 2, '.', ''),
                        ]
                    ];
                }
            }
        }
        elseif($request->type == 'by_item_history'){

            $productReferenceType = Reference::where('type', 'Product')->pluck('prefix')->toArray();
            $serviceReferenceType = Reference::where('type', 'Service')->pluck('prefix')->toArray();
            $itemProductIds = Item::whereIn('reference', $serviceReferenceType)->pluck('reference_id')->toArray();
            $itemServiceIds = Item::whereIn('reference', $serviceReferenceType)->pluck('reference_id')->toArray();
            $products = Product::whereIn('id',$itemProductIds)->get();
            $services = Service::whereIn('id',$itemServiceIds)->get();

            // $itemProductIds = Item::whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
            // $itemServiceIds = Item::whereIn('reference',['SER'])->pluck('reference_id')->toArray();
            // $products = Product::whereIn('id',$itemProductIds)->get();
            // $services = Service::whereIn('id',$itemServiceIds)->get();


            $arr = [];
            $finalData = [];
            if($request->category == 'catalog'){

                foreach($products as $product){
                    $request['product_id'] = $product->id;
                    $arr['name'] = $product->name;
                    $arr['reference'] = $product->reference.''.$product->reference_number;
                    $arr['category'] = $product->product_category_name;
                    $arr['pending'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->whereIn('status',['pending','pending invoice'])->count();
                    $arr['accepted'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','accepted')->count();
                    $arr['closed'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','closed')->count();
                    $arr['refused'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','refused')->count();
                    $arr['total'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                    $arr['units'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                    $arr['amount'] = number_format(TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount'), 2, '.', '');
                    
    
                    $finalData[] = $arr;
                }
                foreach($services as $service){
                    $request['service_id'] = $service->id;
                    $arr['name'] = $service->name;
                    $arr['reference'] = $service->reference.''.$service->reference_number;
                    $arr['category'] = $service->product_category_name;
                    $arr['pending'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->whereIn('status',['pending','pending invoice'])->count();
                    $arr['accepted'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','accepted')->count();
                    $arr['closed'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','closed')->count();
                    $arr['refused'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','refused')->count();
                    $arr['total'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                    $arr['units'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                    $arr['amount'] = number_format(TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount'), 2, '.', '');
                    $finalData[] = $arr;
                }
            }else{
                $categories = ProductCategory::get();
                $request['productCategoryNull'] = 1;
                $arr['name'] = 'Not found in catalog';
                $arr['reference'] = '-';
                $arr['pending'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->whereIn('status',['pending','pending invoice'])->count();
                $arr['accepted'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','accepted')->count();
                $arr['closed'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','closed')->count();
                $arr['refused'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','refused')->count();
                $arr['total'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                $arr['units'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                $arr['amount'] = number_format(TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount'), 2, '.', '');
                $finalData[] = $arr;
                unset($request['productCategoryNull']);
                foreach($categories as $category){
                    $request['productCategory'] = $category->id;
                    $arr['name'] = $category->name;
                    $arr['reference'] = '-';
                    $arr['pending'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->whereIn('status',['pending','pending invoice'])->count();
                    $arr['accepted'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','accepted')->count();
                    $arr['closed'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','closed')->count();
                    $arr['refused'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->where('status','refused')->count();
                    $arr['total'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                    $arr['units'] = TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                    $arr['amount'] = number_format(TechnicalTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount'), 2, '.', '');
                    $finalData[] = $arr;
                }
            }
    
        }

        if($request->export){
            $fileName = 'CATALOGTECHNICALSERVICEREPORT-'.time().$request->company_id.'.xlsx';

            Excel::store(new IncidentByItemExport($finalData, $request), 'public/xlsx/'.$fileName);
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }

        return response()->json([
            "status" => true,
            "data" =>  $finalData
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
                        number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($column), 2, '.', ''),
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
                            number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($column), 2, '.', ''),
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
                            number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount'), 2, '.', ''),
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
                            number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount'), 2, '.', ''),
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
                            number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount'), 2, '.', ''),
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
                        number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($column), 2, '.', ''),
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
                            number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($column), 2, '.', ''),
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
        $finalData = [];
        if($request->category == 'supplier_categories'){
            $categories = ClientCategory::where('type','supplier')->get();
            $request['supplierCategoryNull'] = 1;
            $arr['name'] = "No Selected Category";
            $arr['invoiced'] =  number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($column), 2, '.', '');
            $arr['paid'] =  number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_paid'), 2, '.', '');
            $arr['Unpaid'] =  number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_due'), 2, '.', '');
            $finalData[] = $arr;
            unset($request['supplierCategoryNull']);
            foreach($categories as $category){
                $request['supplierCategory'] = $category->id;
                $arr['name'] = $category->name;
                $arr['invoiced'] = number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum($column), 2, '.', '');
                $arr['paid'] =  number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_paid'), 2, '.', '');
                $arr['Unpaid'] =  number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_due'), 2, '.', '');
                $finalData[] = $arr;
            }
        }else{

            foreach($suppliers as $supplier){
                $arr['name'] = $supplier->legal_name;
                $arr['reference'] = $supplier->reference.''.$supplier->reference_number;
                $arr['ruc'] = $supplier->tin;
                $arr['category'] = $supplier->supplier_category_name;
                $arr['invoiced'] =  number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->where('supplier_id',$supplier->id)->get()->sum($column), 2, '.', '');
                $arr['paid'] =  number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->where('supplier_id',$supplier->id)->get()->sum('amount_paid'), 2, '.', '');
                $arr['Unpaid'] =  number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->where('supplier_id',$supplier->id)->get()->sum('amount_due'), 2, '.', '');
                $finalData[] = $arr;
            }
        }

        if($request->export){
            $fileName = 'SUPPLIERPURCHASEREPORT-'.time().$request->company_id.'.xlsx';

            Excel::store(new PurchaseSupplierExport($finalData, $request), 'public/xlsx/'.$fileName);
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }
            
            return response()->json([
                "status" => true,
                "data" =>  $finalData
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
        $products = Product::whereIn('id',$itemProductIds)->get();
        $services = Service::whereIn('id',$itemServiceIds)->get();
        $expenses = ExpenseAndInvestment::whereIn('id',$expenseInvestmentIds)->get();
           
        $arr = [];
        $finalData = [];
        if($request->category == 'catalog'){

            foreach($products as $product){
                $request['product_id'] = $product->id;
                $arr['name'] = $product->name;
                $arr['reference'] = $product->reference.''.$product->reference_number;
                $arr['category'] = $product->product_category_name;
                $arr['units'] = PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                $arr['amount'] = number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_with_out_vat'), 2, '.', '');
                
    
                $finalData[] = $arr;
            }
            foreach($services as $service){
                $request['service_id'] = $service->id;
                $arr['name'] = $service->name;
                $arr['reference'] = $service->reference.''.$service->reference_number;
                $arr['category'] = $product->product_category_name;
                $arr['units'] = PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                $arr['amount'] = number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_with_out_vat'), 2, '.', '');
                $finalData[] = $arr;
            }
            foreach($expenses as $expense){
                $request['expense_id'] = $expense->id;
                $arr['name'] = $expense->name;
                $arr['reference'] = $expense->reference.''.$expense->reference_number;
                $arr['category'] = $product->expense_category_name;
                $arr['units'] = PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                $arr['amount'] = number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_with_out_vat'), 2, '.', '');
                $finalData[] = $arr;
            }
        }else{
            $categories = ProductCategory::get();
            $finalData = [];
            $request['productCategoryNull'] = 1;
            $arr['name'] = "No Selected Category";
            $arr['reference'] = '-';
            $arr['units'] = PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->count();
            $arr['amount'] = number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_with_out_vat'), 2, '.', '');
            $finalData[] = $arr;
            unset($request['productCategoryNull']);
            foreach($categories as $category){
                $request['productCategory'] = $category->id;
                $arr['name'] = $category->name;
                $arr['reference'] = '-';
                $arr['units'] = PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->count();
                $arr['amount'] =  number_format(PurchaseTable::filter($request->all())->whereIn('reference',$referenceType)->get()->sum('amount_with_out_vat'), 2, '.', '');
                $finalData[] = $arr;
            }
        }

        if($request->export){
            $fileName = 'CATALOGPURCHASEREPORT-'.time().$request->company_id.'.xlsx';

            Excel::store(new PurchaseItemExport($finalData, $request), 'public/xlsx/'.$fileName);
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }

        return response()->json([
            "status" => true,
            "data" =>  $finalData
        ]);
    }

    /* -------------------------------------------------------------------------- */
    /*                               Cash Flow Report                              */
    /* -------------------------------------------------------------------------- */
    public function cashFlow(Request $request){
        $purchaseTables = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($purchaseTables); 
        $ticketTables = 'company_'.$request->company_id.'_purchase_tickets';
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
                            number_format(InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferences)->where('paid', '1')->sum('amount'), 2, '.', '')
                             
                        ] 
                    ], 
                    [
                            "type" => "bar", 
                            "label" => "Withdrawals", 
                            "backgroundColor" => "#FB6363", 
                            "data" => [
                                number_format(InvoiceReceipt::filter($request->all())->whereIn('type', $returnInvoiceReferences)->where('paid', '1')->sum('amount'), 2, '.', '')
                                
                            ] 
                        ], 
                    [
                        "type" => "bar", 
                        "label" => "Balance", 
                        "backgroundColor" => "#FE9140", 
                        "data" => [
                            number_format( InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferences)->where('paid', '1')->sum('amount') - InvoiceReceipt::filter($request->all())->whereIn('type', $returnInvoiceReferences)->where('paid', '1')->sum('amount'), 2, '.', '')
                            
                        ] 
                    ] 
                ], 
                "deposits" => [
                    [
                        "type" => "bar", 
                        "label" => "Invoices", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            number_format(InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferences)->where('paid', '1')->sum('amount'), 2, '.', '')
                            
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Account Deposits", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            number_format(Deposit::filter($request->all())->where('type','deposit')->where('paid_by','1')->sum('amount'), 2, '.', '')
                            
                        ]  
                    ]
                ], 
                "withdrawals" => [
                    [
                        "type" => "bar", 
                        "label" => "Refunds", 
                        "backgroundColor" => "#26C184", 
                        "data" => [
                            number_format(InvoiceReceipt::filter($request->all())->whereIn('type', $returnInvoiceReferences)->where('paid', '1')->sum('amount'), 2, '.', '')
                             
                        ] 
                    ], 
                    [
                        "type" => "bar", 
                        "label" => "Purchases", 
                        "backgroundColor" => "#FB6363", 
                        "data" => [
                            "$ ". number_format(PurchaseReceipt::filter($request->all())->whereIn('type', $purchaseReferences)->where('paid', '1')->sum('amount'), 2, '.', '')
                            
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
                            "$ ". number_format( Deposit::filter($request->all())->where('type','withdraw')->where('paid_by','1')->sum('amount'), 2, '.', '')
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
                        number_format( InvoiceTable::filter($request->all())->whereHas('payment_options', function ($query) use ($paymentOption) {
                            $query->where('id', $paymentOption->id);
                        })->get()->sum('amount'), 2, '.', ''),
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
                                number_format( InvoiceTable::filter($request->all())->get()->sum('amount'), 2, '.', ''),
                            ]
                        ];
            return response()->json([
                "status" => true,
                "data" => $data
            ]);
        }

        if($request->export){
            $fileName = 'CASHFLOWREPORT-'.time().$request->company_id.'.xlsx';

                $arr = [];
                $invoiceReferences =  Reference::where('type', 'Normal Invoice')->pluck('prefix')->toArray();
                $purchaseReferences =  Reference::where('type', 'Purchase Invoice')->pluck('prefix')->toArray();
                $returnInvoiceReferences =  Reference::where('type', 'Refund Invoice')->pluck('prefix')->toArray();
                
                $arr['Deposits'] = InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferences)->where('paid', '1')->sum('amount');
                $arr['Withdrawals'] = InvoiceReceipt::filter($request->all())->whereIn('type', $returnInvoiceReferences)->where('paid', '1')->sum('amount');
                $arr['Balance'] = InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferences)->where('paid', '1')->sum('amount') - InvoiceReceipt::filter($request->all())->whereIn('type', $returnInvoiceReferences)->where('paid', '1')->sum('amount');
                $arr['Invoices'] = InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferences)->where('paid', '1')->sum('amount');
                $arr['account_deposits'] = Deposit::filter($request->all())->where('type','deposit')->where('paid_by','1')->sum('amount');
                $arr['Refunds'] = InvoiceReceipt::filter($request->all())->whereIn('type', $returnInvoiceReferences)->where('paid', '1')->sum('amount');
                $arr['Purchases'] = PurchaseReceipt::filter($request->all())->whereIn('type', $purchaseReferences)->where('paid', '1')->sum('amount');
                $arr['Tickets_expenses'] = '0';
                $arr['Account_withdrawals'] = Deposit::filter($request->all())->where('type','withdraw')->where('paid_by','1')->sum('amount');

                $data = $arr;

            Excel::store(new CashFlowExport($data, $request), 'public/xlsx/'.$fileName);

            
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
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
        
        if($request->after_tax){
            $column = 'amount';
        }else{
            $column = 'amount_with_out_vat';
        }

        $arr = [];
        $finalData = [];

        foreach($paymentOptions as $paymentOption){
            $deposit =  number_format( Deposit::filter($request->all())->WhereHas('payment_options', function ($query) use ($paymentOption) {
                $query->where('payment_option', $paymentOption->id);
            })->where('type','deposit')->get()->sum('amount'), 2, '.', '');
            $withdrawal = number_format(Deposit::filter($request->all())->WhereHas('payment_options', function ($query) use ($paymentOption) {
                $query->where('payment_option', $paymentOption->id);
            })->where('type','withdraw')->get()->sum('amount'), 2, '.', '');
            $invoiceAmount = number_format(InvoiceTable::filter($request->all())->WhereHas('payment_options', function ($query) use ($paymentOption) {
                $query->where('id', $paymentOption->id);
            })->get()->sum($column), 2, '.', '');
                // dd($invoiceAmount);
            $arr['name'] = $paymentOption->name;
            $arr['deposit'] = $deposit + $invoiceAmount;
            $arr['withdrawals'] = $withdrawal;
            $arr['balance'] = (($invoiceAmount + $deposit) - $withdrawal);
            

            $finalData[] = $arr;
        }

        if($request->export){
            $fileName = 'PAYMENTOPTIONCASHFLOWREPORT-'.time().$request->company_id.'.xlsx';

            Excel::store(new PaymentOptionExport($finalData, $request), 'public/xlsx/'.$fileName);

            
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }

        return response()->json([
            "status" => true,
            "data" =>  $finalData
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

        if($request->after_tax){
            $column = 'amount';
        }else{
            $column = 'amount_with_out_vat';
        }

        $arr = [];
        $finalData = [];
            
        $deposit = number_format(Deposit::filter($request->all())->where('type','deposit')->where('paid_by','1')->sum('amount'), 2, '.', '');
        $withdrawal = number_format(Deposit::filter($request->all())->where('type','withdraw')->where('paid_by','1')->sum('amount'), 2, '.', '');
        $invoiceAmount = number_format(InvoiceTable::filter($request->all())->where('agent_id',\Auth::id())->get()->sum($column), 2, '.', '');

        $arr['name'] = \Auth::user()->name;
        $arr['deposit'] = $deposit + $invoiceAmount;
        $arr['withdrawals'] = $withdrawal;
        $arr['balance'] = (($invoiceAmount + $deposit) -  $withdrawal);

        $finalData[] = $arr;

        if($request->export){
            $fileName = 'EMPLOYEECASHFLOWREPORT-'.time().$request->company_id.'.xlsx';

            Excel::store(new CashFlowByAgentExport($finalData, $request), 'public/xlsx/'.$fileName);

            
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }

        return response()->json([
            "status" => true,
            "data" =>  $finalData
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
            $finalData = []; 

            foreach($paymentOption as $invoiceData){
                $arr['date'] = $invoiceData->date;
                $arr['type'] = $invoiceData->reference_type;
                $arr['reference'] = $invoiceData->reference.''.$invoiceData->reference_number;
                $arr['client'] = $invoiceData->client_name;
                $arr['employee'] = \Auth::user()->name;
                $arr['payment_option'] = $invoiceData->payment_option_name;
                $arr['amount'] = $invoiceData->amount;
                $arr['paid'] = $invoiceData->set_as_paid;
                $finalData[] = $arr;
            }
            foreach($purchasePaymentOptions as $purchaseData){
                $arr['date'] = $purchaseData->date;
                $arr['type'] = $purchaseData->reference_type;
                $arr['reference'] = $purchaseData->reference.''.$purchaseData->reference_number;
                $arr['client'] = $purchaseData->supplier_name;
                $arr['employee'] = \Auth::user()->name;
                $arr['amount'] = $purchaseData->amount;
                $arr['payment_option'] = $purchaseData->payment_option_name;
                $arr['paid'] = $purchaseData->set_as_paid;
                $finalData[] = $arr;
            }

                $invoiceReferences =  Reference::where('type', 'Normal Invoice')->pluck('prefix')->toArray();
                $purchaseReferences =  Reference::where('type', 'Purchase Invoice')->pluck('prefix')->toArray();
                $returnInvoiceReferences =  Reference::where('type', 'Refund Invoice')->pluck('prefix')->toArray();
                
                $arr['Deposits'] = InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferences)->where('paid', '1')->sum('amount');
                $arr['Withdrawals'] = InvoiceReceipt::filter($request->all())->whereIn('type', $returnInvoiceReferences)->where('paid', '1')->sum('amount');
                $arr['Balance'] = InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferences)->where('paid', '1')->sum('amount') - InvoiceReceipt::filter($request->all())->whereIn('type', $returnInvoiceReferences)->where('paid', '1')->sum('amount');
                $arr['Invoices'] = InvoiceReceipt::filter($request->all())->whereIn('type', $invoiceReferences)->where('paid', '1')->sum('amount');
                $arr['account_deposits'] = Deposit::filter($request->all())->where('type','deposit')->where('paid_by','1')->sum('amount');
                $arr['Refunds'] = InvoiceReceipt::filter($request->all())->whereIn('type', $returnInvoiceReferences)->where('paid', '1')->sum('amount');
                $arr['Purchases'] = PurchaseReceipt::filter($request->all())->whereIn('type', $purchaseReferences)->where('paid', '1')->sum('amount');
                $arr['Tickets_expenses'] = '0';
                $arr['Account_withdrawals'] = Deposit::filter($request->all())->where('type','withdraw')->where('paid_by','1')->sum('amount');

                $overview = $arr;

            if($request->export){
                $fileName = 'CASHFLOWREPORT-'.time().$request->company_id.'.xlsx';
                
                Excel::store(new CashFlowExport($finalData,$overview, $request), 'public/xlsx/'.$fileName);
    
                
                return response()->json([
                    'status' => true,
                    'url' => url('/storage/xlsx/'.$fileName),
                 ]);
            }
            
            return response()->json([
                "status" => true,
                "data" =>  $finalData
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
                        number_format(Product::filter($request->all())->sum('price') * Product::filter($request->all())->get()->sum($column), 2, '.', '')
                        
                    ] 
                ], 
                [
                    "type" => "bar", 
                    "label" => "Purchase stock value", 
                    "backgroundColor" => "#FB6363", 
                    "data" => [
                        number_format(Product::filter($request->all())->sum('purchase_price')  * Product::filter($request->all())->get()->sum($column), 2, '.', '')
                        
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
        $finalData = [];
        $column  = 'virtual_stock';
        if($request->stock  == 'stock'){
            $column  = 'stock';
        }
        foreach($products as $product){
            $arr['reference'] = $product->reference.$product->reference_number;
            $arr['category'] = $product->product_category_name;
            $arr['name'] = $product->name;
            $stock = $product->$column;
            $arr['stock'] = $stock;
            $arr['sales_stock_value'] = $product->price*$stock;
            $arr['purchase_stock_value'] = $product->purchase_price*$stock;

            $finalData[] = $arr;
        }
        if($request->export){
            $fileName = 'STOCKVALUATIONREPORT-'.time().$request->company_id.'.xlsx';

            Excel::store(new StockValuationExport($finalData, $request), 'public/xlsx/'.$fileName);
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }
        return response()->json([
            "status" => true,
            "data" => $finalData
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
                        number_format(InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($tax,$column) {
                            $query->where($column, $tax->tax);
                        })->get()->sum('tax_amount'), 2, '.', ''),
                    ]  
                ];
                $data['tax_Summary'][] = [
                    "type" => "bar", 
                    "label" => "Paid", 
                    "backgroundColor" => "#FB6363", 
                    "data" => [
                        number_format(PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($tax,$column) {
                            $query->where($column, $tax->tax);
                        })->get()->sum('tax_amount'), 2, '.', ''),
                    ]  
                ];
                $data['tax_Summary'][] = [
                    "type" => "bar", 
                    "label" => "Total", 
                    "backgroundColor" => "#FE9140", 
                    "data" => [
                        number_format( PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use ($tax,$column) {
                            $query->where($column, $tax->tax);
                        })->get()->sum('tax_amount'), 2, '.', ''),
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
                $arr['collected'] = number_format( InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use ($tax,$column) {
                    $query->where($column, $tax->tax);
                })->get()->sum('amount'), 2, '.', '');
                $arr['ctax'] = number_format(InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use($tax,$column) {
                    $query->where($column, $tax->tax);
                })->get()->sum('tax_amount'), 2, '.', '');
                $arr['paid'] = number_format(PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use($tax,$column) {
                    $query->where($column, $tax->tax);
                })->get()->sum('amount'), 2, '.', '');
                $arr['ptax'] = number_format(PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use($tax,$column) {
                    $query->where($column, $tax->tax);
                })->get()->sum('tax_amount'), 2, '.', '');

                $arr['total'] = number_format(InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use($tax,$column) {
                    $query->where($column, $tax->tax);
                })->get()->sum('amount') - PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use($tax,$column) {
                    $query->where($column, $tax->tax);
                })->get()->sum('amount'), 2, '.', '');

                $arr['ttax'] = number_format(InvoiceTable::filter($request->all())->WhereHas('items', function ($query) use($tax,$column) {
                    $query->where($column, $tax->tax);
                })->get()->sum('tax_amount') - PurchaseTable::filter($request->all())->WhereHas('items', function ($query) use($tax,$column) {
                    $query->where($column, $tax->tax);
                })->get()->sum('tax_amount'), 2, '.', '');

                $data[] = $arr;
            }   
        }
        
        return response()->json([
            "status" => true,
            "data" =>  $data
        ]);

    }

    /* -------------------------------------------------------------------------- */
    /*                    Of profit sub report of of evolution                    */
    /* -------------------------------------------------------------------------- */
    public function ofProfit(Request $request){
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
        $purchaseReferenceTypes = Reference::where('type', 'Purchase Invoice')->pluck('prefix')->toArray();
        //set the year for the report
        if(!$request->year){
            $request['year'] =  date('Y');
        }
        $dates =  getDateToIterate($request);
        if($request->type == 'graph'){
            $data = [];
            foreach($dates as $date){
                $data['labels'][] =  $date['name'];
                $data['Sales'][] = number_format(InvoiceTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date']])->get()->sum('amount_with_out_vat'), 2, '.', '');
                $data['Expenses'][] =  number_format(PurchaseTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date']])->whereIn('reference',$purchaseReferenceTypes)->get()->sum('amount_with_out_vat'), 2, '.', '');
                $data['Profit'][] =  number_format(InvoiceTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date']])->get()->sum('amount_with_out_vat') - PurchaseTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date']])->whereIn('reference',$purchaseReferenceTypes)->get()->sum('amount_with_out_vat'), 2, '.', '');
            }
            $finalData['labels'] = @$data['labels'];
            if($request->module){
                $finalData['data'] = [
                    [
                        "label" => $request->module,
                        "fill" => true,
                        "lineTension" => 0.5,
                        "backgroundColor" => "rgba(60, 76, 207, 0.2)",
                        "borderColor" => "#3c4ccf",
                        "borderCapStyle" => "butt",
                        "borderDash" => [],
                        "borderDashOffset" => 0.0,
                        "borderJoinStyle" => "miter",
                        "pointBorderColor" => "#3c4ccf",
                        "pointBackgroundColor" => "#fff",
                        "pointBorderWidth" => 1,
                        "pointHoverRadius" => 5,
                        "pointHoverBackgroundColor" => "#3c4ccf",
                        "pointHoverBorderColor" => "#fff",
                        "pointHoverBorderWidth" => 2,
                        "pointRadius" => 1,
                        "pointHitRadius" => 1000,
                        "data" => @$data[$request->module]
                    ],
                ];
            }else{

                $finalData['data'] = [
                    [
                        "label" => "Sales",
                        "fill" => true,
                        "lineTension" => 0.5,
                        "backgroundColor" => "rgba(60, 76, 207, 0.2)",
                        "borderColor" => "#3c4ccf",
                        "borderCapStyle" => "butt",
                        "borderDash" => [],
                        "borderDashOffset" => 0.0,
                        "borderJoinStyle" => "miter",
                        "pointBorderColor" => "#3c4ccf",
                        "pointBackgroundColor" => "#fff",
                        "pointBorderWidth" => 1,
                        "pointHoverRadius" => 5,
                        "pointHoverBackgroundColor" => "#3c4ccf",
                        "pointHoverBorderColor" => "#fff",
                        "pointHoverBorderWidth" => 2,
                        "pointRadius" => 1,
                        "pointHitRadius" => 1000,
                        "data" => @$data['Sales']
                    ],
                    [
                        "label" => "Expenses",
                        "fill" => true,
                        "lineTension" => 0.5,
                        "backgroundColor" => "rgba(235, 239, 242, 0.2)",
                        "borderColor" => "#ebeff2",
                        "borderCapStyle" => "butt",
                        "borderDash" => [],
                        "borderDashOffset" => 0.0,
                        "borderJoinStyle" => "miter",
                        "pointBorderColor" => "#ebeff2",
                        "pointBackgroundColor" => "#fff",
                        "pointBorderWidth" => 1,
                        "pointHoverRadius" => 5,
                        "pointHoverBackgroundColor" => "#ebeff2",
                        "pointHoverBorderColor" => "#eef0f2",
                        "pointHoverBorderWidth" => 2,
                        "pointRadius" => 1,
                        "pointHitRadius" => 1000,
                        "data" => @$data['Expenses']
                    ],
                    [
                        "label" => "Profit",
                        "fill" => true,
                        "lineTension" => 0.5,
                        "backgroundColor" => "rgba(235, 239, 242, 0.2)",
                        "borderColor" => "#ebeff2",
                        "borderCapStyle" => "butt",
                        "borderDash" => [],
                        "borderDashOffset" => 0.0,
                        "borderJoinStyle" => "miter",
                        "pointBorderColor" => "#ebeff2",
                        "pointBackgroundColor" => "#fff",
                        "pointBorderWidth" => 1,
                        "pointHoverRadius" => 5,
                        "pointHoverBackgroundColor" => "#ebeff2",
                        "pointHoverBorderColor" => "#eef0f2",
                        "pointHoverBorderWidth" => 2,
                        "pointRadius" => 1,
                        "pointHitRadius" => 1000,
                        "data" => @$data['Profit']
                    ]
                ];
            }
        }else{
            $finalData = [];
            
            foreach($dates as $date){
                $arr = [];
                $arr['period'] = $date['name'];
                $arr['sales'] = number_format(InvoiceTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date']])->get()->sum('amount_with_out_vat'), 2, '.', '');
                $arr['expense'] =  number_format(PurchaseTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date']])->whereIn('reference',$purchaseReferenceTypes)->get()->sum('amount_with_out_vat'), 2, '.', '');
                $arr['profit'] = number_format(InvoiceTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date']])->get()->sum('amount_with_out_vat') - PurchaseTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date']])->whereIn('reference',$purchaseReferenceTypes)->get()->sum('amount_with_out_vat'), 2, '.', '');

                $finalData[] = $arr;
            }
            if($request->export){
                $fileName = 'PROFITEVOLUTIONREPORT-'.time().$request->company_id.'.xlsx';
                Excel::store(new OfProfitExport($finalData, $request), 'public/xlsx/'.$fileName);
                return response()->json([
                    'status' => true,
                    'url' => url('/storage/xlsx/'.$fileName),
                 ]);
            }
        }
        return response()->json([
            'status' => true,
            'data' => $finalData
        ]);
    }
    /* -------------------------------------------------------------------------- */
    /*                   Invoicing By Client sub report of of evolution           */
    /* -------------------------------------------------------------------------- */
    public function InvoicingByClient(Request $request){
        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables);

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
        $purchaseReferenceTypes = Reference::where('type', 'Purchase Invoice')->pluck('prefix')->toArray();
        $client_ids = InvoiceTable::whereHas('client')->pluck('client_id')->toArray();
        $clients = Client::whereIn('id',$client_ids)->get();
        //set the year for the report
        if(!$request->year){
            $request['year'] =  date('Y');
        }
        if($request->after_tax){
            $column = 'amount';
            }else{
            $column = 'amount_with_out_vat';
        }
        $dates =  getDateToIterate($request);
        if($request->type == 'graph'){
            $data = [];
            foreach($dates as $date){
                $data['labels'][] =  $date['name'];
                foreach($clients as $client){
                    $data[$client->id][] =  number_format(InvoiceTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date'], 'client_id' => $client->id, 'reference' => $request->reference, 'agent_id' =>$request->agent_id , 'product_id' => $request->product_id, 'service_id' => $request->service_id])->get()->sum($column), 2, '.', '');
                }
            }
            $finalData['labels'] = @$data['labels'];
            
            foreach($clients as $client){

                $finalData['data'][] = 
                [
                    "label" => $client->legal_name.' ('.$client->name.')',
                    "fill" => true,
                    "lineTension" => 0.5,
                    "backgroundColor" => generateRandomColor(),
                    "borderColor" => "#3c4ccf",
                    "borderCapStyle" => "butt",
                    "borderDash" => [],
                    "borderDashOffset" => 0.0,
                    "borderJoinStyle" => "miter",
                    "pointBorderColor" => "#3c4ccf",
                    "pointBackgroundColor" => "#fff",
                    "pointBorderWidth" => 1,
                    "pointHoverRadius" => 5,
                    "pointHoverBackgroundColor" => "#3c4ccf",
                    "pointHoverBorderColor" => "#fff",
                    "pointHoverBorderWidth" => 2,
                    "pointRadius" => 1,
                    "pointHitRadius" => 1000,
                    "data" => @$data[$client->id]
                ];
            }
        }else{
            $finalData = [];
            foreach($dates as $date){
                $finalData['labels'][] =  $date['name'];
            }
            foreach($clients as $client){
                $arr = [];
                $arr['name'] = $client->legal_name.' ('.$client->name.')';
                $arr['reference'] = $client->reference.''.$client->reference_number;
                $arr['tin'] = $client->tin;
                $arr['client_category'] = $client->client_category_name;
                $arr['zip_code'] = $client->zip_code;
                $arr['total'] = 0;
                foreach($dates as $date){
                    $amount = number_format(InvoiceTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date'], 'client_id' => $client->id, 'reference' => $request->reference, 'agent_id' =>$request->agent_id , 'product_id' => $request->product_id, 'service_id' => $request->service_id])->get()->sum($column), 2, '.', '');
                    $arr['data'][] =  $amount;
                    $arr['total'] = $arr['total']+ $amount;
                }
                
    
                $finalData['data'][] = $arr;
                
            }
            
        }
        if($request->export){
            $fileName = 'CLIENTINVOICINGEVOLUTIONREPORT-'.time().$request->company_id.'.xlsx';

            Excel::store(new InvoiceByClientEvoluationExport($finalData, $request), 'public/xlsx/'.$fileName);
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }
        return response()->json([
            'status' => true,
            'data' => $finalData
        ]);
    }
    /* -------------------------------------------------------------------------- */
    /*                   Invoicing By Agent sub report of of evolution            */
    /* -------------------------------------------------------------------------- */
    public function InvoicingByAgent(Request $request){
        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables);

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
        $purchaseReferenceTypes = Reference::where('type', 'Purchase Invoice')->pluck('prefix')->toArray();
        $client_ids = InvoiceTable::whereHas('client')->pluck('client_id')->toArray();
        $clients = Client::whereIn('id',$client_ids)->get();
        //set the year for the report
        if(!$request->year){
            $request['year'] =  date('Y');
        }
        if($request->after_tax){
            $column = 'amount';
            }else{
            $column = 'amount_with_out_vat';
        }
        $dates =  getDateToIterate($request);
        if($request->type == 'graph'){
            $data = [];
            foreach($dates as $date){
                $data['labels'][] =  $date['name'];
                $data[\Auth::id()][] =  number_format(InvoiceTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date'], 'client_id' => $request->client_id, 'reference' => $request->reference, 'agent_id' =>\Auth::id() , 'product_id' => $request->product_id , 'service_id' => $request->service_id])->get()->sum($column), 2, '.', '');
            }
            $finalData['labels'] = @$data['labels'];
            $finalData['data'][] = 
            [
                "label" => \Auth::user()->name,
                "fill" => true,
                "lineTension" => 0.5,
                "backgroundColor" => generateRandomColor(),
                "borderColor" => "#3c4ccf",
                "borderCapStyle" => "butt",
                "borderDash" => [],
                "borderDashOffset" => 0.0,
                "borderJoinStyle" => "miter",
                "pointBorderColor" => "#3c4ccf",
                "pointBackgroundColor" => "#fff",
                "pointBorderWidth" => 1,
                "pointHoverRadius" => 5,
                "pointHoverBackgroundColor" => "#3c4ccf",
                "pointHoverBorderColor" => "#fff",
                "pointHoverBorderWidth" => 2,
                "pointRadius" => 1,
                "pointHitRadius" => 1000,
                "data" => @$data[\Auth::id()]
            ];
        }else{
            $finalData = [];
            foreach($dates as $date){
                $finalData['labels'][] =  $date['name'];
            }
            $arr = [];
            $arr['name'] =  \Auth::user()->name;
            $arr['total'] = 0;
            foreach($dates as $date){
                $amount =  number_format(InvoiceTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date'], 'client_id' => $request->client_id, 'reference' => $request->reference, 'agent_id' =>\Auth::id() , 'product_id' => $request->product_id , 'service_id' => $request->service_id])->get()->sum($column), 2, '.', '');
                $arr['data'][] =  $amount;
                $arr['total'] = $arr['total']+ $amount;
            }
            

            $finalData['data'][] = $arr;
        }

        if($request->export){
            $fileName = 'EMPLOYEEINVOICINGEVOLUTIONREPORT-'.time().$request->company_id.'.xlsx';

            Excel::store(new InvoiceByAgentEvoluationExport($finalData, $request), 'public/xlsx/'.$fileName);
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }
        return response()->json([
            'status' => true,
            'data' => $finalData
        ]);
    }
    /* -------------------------------------------------------------------------- */
    /*                   Invoicing By Items sub report of of evolution            */
    /* -------------------------------------------------------------------------- */
    public function InvoicingByItem(Request $request){
        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables);

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
        $serviceTable = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($serviceTable);

        $productTable = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($productTable);
        $invoiceReceiptTable = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($invoiceReceiptTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);
        $purchaseReferenceTypes = Reference::where('type', 'Purchase Invoice')->pluck('prefix')->toArray();
        $client_ids = InvoiceTable::whereHas('client')->pluck('client_id')->toArray();
        $clients = Client::whereIn('id',$client_ids)->get();
        $referenceType = Reference::whereIn('type', ['Normal Invoice', 'Refund Invoice'])->pluck('prefix')->toArray();
        $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',['PRO'])->pluck('reference_id')->toArray();
        $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',['SER'])->pluck('reference_id')->toArray();
        $products = Product::whereIn('id',$itemProductIds)->get();
        $services = Service::whereIn('id',$itemServiceIds)->get();
        //set the year for the report
        if(!$request->year){
            $request['year'] =  date('Y');
        }
        if($request->after_tax){
            $column = 'amount';
            }else{
            $column = 'amount_with_out_vat';
        }
        $dates =  getDateToIterate($request);
        if($request->type == 'graph'){
            $data = [];
            foreach($dates as $date){
                $data['labels'][] =  $date['name'];
                $data['no_category'][] =  number_format(InvoiceTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date'], 'client_id' =>$request->client, 'reference' => $request->reference, 'agent_id' =>$request->agent_id , 'product_id' => $request->product_id])->whereDoesntHave('products')->get()->sum($column), 2, '.', '');
                foreach($products as $product){
                    $data['product_'.$product->id][] =  number_format(InvoiceTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date'], 'client_id' => $request->client_id, 'reference' => $request->reference, 'agent_id' =>$request->agent_id , 'product_id' => $product->id])->get()->sum($column), 2, '.', '');
                }
                foreach($services as $service){
                    $data['service_'.$service->id][] =  number_format(InvoiceTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date'], 'client_id' => $request->client_id, 'reference' => $request->reference, 'agent_id' =>$request->agent_id , 'service_id' => $service->id])->get()->sum($column), 2, '.', '');
                }
            }
            $finalData['labels'] = @$data['labels'];
            $finalData['data'][] = 
            [
                "label" => "Not found in catalog",
                "fill" => true,
                "lineTension" => 0.5,
                "backgroundColor" => generateRandomColor(),
                "borderColor" => "#3c4ccf",
                "borderCapStyle" => "butt",
                "borderDash" => [],
                "borderDashOffset" => 0.0,
                "borderJoinStyle" => "miter",
                "pointBorderColor" => "#3c4ccf",
                "pointBackgroundColor" => "#fff",
                "pointBorderWidth" => 1,
                "pointHoverRadius" => 5,
                "pointHoverBackgroundColor" => "#3c4ccf",
                "pointHoverBorderColor" => "#fff",
                "pointHoverBorderWidth" => 2,
                "pointRadius" => 1,
                "pointHitRadius" => 1000,
                "data" => @$data['no_category']
            ];
            foreach($products as $product){

                $finalData['data'][] = 
                [
                    "label" => $product->name,
                    "fill" => true,
                    "lineTension" => 0.5,
                    "backgroundColor" => generateRandomColor(),
                    "borderColor" => "#3c4ccf",
                    "borderCapStyle" => "butt",
                    "borderDash" => [],
                    "borderDashOffset" => 0.0,
                    "borderJoinStyle" => "miter",
                    "pointBorderColor" => "#3c4ccf",
                    "pointBackgroundColor" => "#fff",
                    "pointBorderWidth" => 1,
                    "pointHoverRadius" => 5,
                    "pointHoverBackgroundColor" => "#3c4ccf",
                    "pointHoverBorderColor" => "#fff",
                    "pointHoverBorderWidth" => 2,
                    "pointRadius" => 1,
                    "pointHitRadius" => 1000,
                    "data" => @$data['product_'.$product->id]
                ];
            }
            foreach($services as $service){

                $finalData['data'][] = 
                [
                    "label" => $service->name,
                    "fill" => true,
                    "lineTension" => 0.5,
                    "backgroundColor" => generateRandomColor(),
                    "borderColor" => "#3c4ccf",
                    "borderCapStyle" => "butt",
                    "borderDash" => [],
                    "borderDashOffset" => 0.0,
                    "borderJoinStyle" => "miter",
                    "pointBorderColor" => "#3c4ccf",
                    "pointBackgroundColor" => "#fff",
                    "pointBorderWidth" => 1,
                    "pointHoverRadius" => 5,
                    "pointHoverBackgroundColor" => "#3c4ccf",
                    "pointHoverBorderColor" => "#fff",
                    "pointHoverBorderWidth" => 2,
                    "pointRadius" => 1,
                    "pointHitRadius" => 1000,
                    "data" => @$data['service_'.$service->id]
                ];
            }
        }else{
            $finalData = [];
            foreach($dates as $date){
                $finalData['labels'][] =  $date['name'];
            }
            $arr = [];
            $arr['name'] = "Not found in catalog";
            $arr['reference'] = "-";
            $arr['category'] = "_";
            $arr['total'] = 0;
            foreach($dates as $date){
                $amount = number_format(InvoiceTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date'], 'client_id' =>$request->client, 'reference' => $request->reference, 'agent_id' =>$request->agent_id , 'product_id' => $request->product_id])->whereDoesntHave('products')->get()->sum($column), 2, '.', '');
                $arr['data'][] =  $amount;
                $arr['total'] = $arr['total']+ $amount;
            }
            

            $finalData['data'][] = $arr;
            foreach($products as $product){
                $arr = [];
                $arr['name'] = $product->name;
                $arr['category'] = $product->product_category_name;
                $arr['reference'] = $product->reference.''.$product->reference_number;
                $arr['total'] = 0;
                foreach($dates as $date){
                    $amount = number_format(InvoiceTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date'], 'client_id' => $request->client_id, 'reference' => $request->reference, 'agent_id' =>$request->agent_id , 'product_id' => $product->id])->get()->sum($column), 2, '.', '');
                    $arr['data'][] =  $amount;
                    $arr['total'] = $arr['total']+ $amount;
                }
                
    
                $finalData['data'][] = $arr;
            }
            foreach($services as $service){
                $arr = [];
                $arr['name'] = $service->name;
                $arr['category'] = $service->product_category_name;
                $arr['reference'] = $service->reference.''.$service->reference_number;
                $arr['total'] = 0;
                foreach($dates as $date){
                    $amount = number_format(InvoiceTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date'], 'client_id' => $request->client_id, 'reference' => $request->reference, 'agent_id' =>$request->agent_id , 'service_id' => $service->id])->get()->sum($column), 2, '.', '');
                    $arr['data'][] =  $amount;
                    $arr['total'] = $arr['total']+ $amount;
                }
                
    
                $finalData['data'][] = $arr;
            }
        }
        if($request->export){
            $fileName = 'CATALOGINVOICINGEVOLUTIONREPORT-'.time().$request->company_id.'.xlsx';

            Excel::store(new InvoiceByItemEvoluationExport($finalData, $request), 'public/xlsx/'.$fileName);
            return response()->json([
                'status' => true,
                'url' => url('/storage/xlsx/'.$fileName),
             ]);
        }
        return response()->json([
            'status' => true,
            'data' => $finalData
        ]);
    }
    /* -------------------------------------------------------------------------- */
    /*                   Purchase  By Provider sub report of of evolution         */
    /* -------------------------------------------------------------------------- */
    public function purchasesByProvider(Request $request){  
        $suppliersTables = 'company_'.$request->company_id.'_suppliers';
        Supplier::setGlobalTable($suppliersTables);

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
        //set the year for the report
        if(!$request->year){
            $request['year'] =  date('Y');
        }
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
        $supplier_ids = PurchaseTable::whereHas('supplier')->whereIn('reference', $referenceType)->pluck('supplier_id')->toArray();
        $suppliers = Supplier::whereIn('id',$supplier_ids)->get();
        $dates =  getDateToIterate($request);
        if($request->type == 'graph'){
            $data = [];
            foreach($dates as $date){
                $data['labels'][] =  $date['name'];
                foreach($suppliers as $supplier){
                    $data[$supplier->id][] =  number_format(PurchaseTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date'], 'supplier' => $supplier->id, 'agent_id' =>$request->agent_id , 'product_id' => $request->product_id , 'service_id' => $request->service_id])->whereIn('reference', $referenceType)->get()->sum($column), 2, '.', '');
                }
            }
            $finalData['labels'] = @$data['labels'];
            
            foreach($suppliers as $supplier){

                $finalData['data'][] = 
                [
                    "label" => $supplier->legal_name.' ('.$supplier->name.')',
                    "fill" => true,
                    "lineTension" => 0.5,
                    "backgroundColor" => generateRandomColor(),
                    "borderColor" => "#3c4ccf",
                    "borderCapStyle" => "butt",
                    "borderDash" => [],
                    "borderDashOffset" => 0.0,
                    "borderJoinStyle" => "miter",
                    "pointBorderColor" => "#3c4ccf",
                    "pointBackgroundColor" => "#fff",
                    "pointBorderWidth" => 1,
                    "pointHoverRadius" => 5,
                    "pointHoverBackgroundColor" => "#3c4ccf",
                    "pointHoverBorderColor" => "#fff",
                    "pointHoverBorderWidth" => 2,
                    "pointRadius" => 1,
                    "pointHitRadius" => 1000,
                    "data" => @$data[$supplier->id]
                ];
            }
        }else{
            $finalData = [];
            foreach($dates as $date){
                $finalData['labels'][] =  $date['name'];
            }
            foreach($suppliers as $supplier){
                $arr = [];
                $arr['name'] = $supplier->legal_name.' ('.$supplier->name.')';
                $arr['total'] = 0;
                foreach($dates as $date){
                    $amount = number_format(PurchaseTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date'], 'supplier' => $supplier->id, 'agent_id' =>$request->agent_id , 'product_id' => $request->product_id ,'service_id' => $request->service_id])->whereIn('reference', $referenceType)->get()->sum($column), 2, '.', '');
                    $arr['data'][] =  $amount;
                    $arr['total'] = $arr['total']+ $amount;
                }
                
    
                $finalData['data'][] = $arr;
            }
        }
        return response()->json([
            'status' => true,
            'data' => $finalData
        ]);
    }
     /* -------------------------------------------------------------------------- */
    /*                   Purchases By Items sub report of of evolution            */
    /* -------------------------------------------------------------------------- */
    public function purchasesByItem(Request $request){
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
        $serviceTable = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($serviceTable);

        $productTable = 'company_'.$request->company_id.'_products';
        Product::setGlobalTable($productTable);
        $expenseTable = 'company_'.$request->company_id.'_expense_and_investments';
        ExpenseAndInvestment::setGlobalTable($expenseTable);

        $invoiceReceiptTable = 'company_'.$request->company_id.'_invoice_receipts';
        InvoiceReceipt::setGlobalTable($invoiceReceiptTable);

        $item_meta_table = 'company_'.$request->company_id.'_item_metas';
        ItemMeta::setGlobalTable($item_meta_table);
        $supplier_ids = PurchaseTable::whereHas('supplier')->pluck('supplier_id')->toArray();
        $suppliers = Supplier::whereIn('id',$supplier_ids)->get();
        $referenceType = Reference::whereIn('type', ['Purchase Invoice'])->pluck('prefix')->toArray();
        $referenceExpenseType = Reference::where('type', 'Expense and investment')->pluck('prefix')->toArray();
        // return $referenceExpenseType;
        $referenceProductType = Reference::where('type', 'Product')->pluck('prefix')->toArray();
        $referenceServiceType = Reference::where('type', 'Service')->pluck('prefix')->toArray();
        $itemProductIds = Item::whereIn('type',$referenceType)->whereIn('reference',$referenceProductType)->pluck('reference_id')->toArray();
        $itemServiceIds = Item::whereIn('type',$referenceType)->whereIn('reference',$referenceServiceType)->pluck('reference_id')->toArray();
        $itemExpenseIds = Item::whereIn('type',$referenceType)->whereIn('reference',$referenceExpenseType)->pluck('reference_id')->toArray();
        $products = Product::whereIn('id',$itemProductIds)->get();
        $services = Service::whereIn('id',$itemServiceIds)->get();
        $expenses = ExpenseAndInvestment::whereIn('id',$itemExpenseIds)->get();
        //set the year for the report
        if(!$request->year){
            $request['year'] =  date('Y');
        }
        //set the year for the report
        if(!$request->year){
            $request['year'] =  date('Y');
        }
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
        $dates =  getDateToIterate($request);
        if($request->type == 'graph'){
            $data = [];
            foreach($dates as $date){
                $data['labels'][] =  $date['name'];
                $data['no_category'][] =  number_format(PurchaseTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date'], 'client_id' =>$request->client, 'agent_id' =>$request->agent_id , 'product_id' => $request->product_id])->whereDoesntHave('products')->get()->sum($column), 2, '.', '');
                foreach($products as $product){
                    $data['product_'.$product->id][] =  number_format(PurchaseTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date'], 'client_id' => $request->client_id, 'agent_id' =>$request->agent_id , 'product_id' => $product->id])->whereIn('reference', $referenceType)->get()->sum($column), 2, '.', '');
                }
                foreach($services as $service){
                    $data['service_'.$service->id][] =  number_format(PurchaseTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date'], 'client_id' => $request->client_id, 'agent_id' =>$request->agent_id , 'service_id' => $service->id])->whereIn('reference', $referenceType)->get()->sum($column), 2, '.', '');
                }
                foreach($expenses as $expense){
                    $data['expense_'.$expense->id][] =  number_format(PurchaseTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date'], 'client_id' => $request->client_id, 'agent_id' =>$request->agent_id , 'expense_id' => $expense->id])->whereIn('reference', $referenceType)->get()->sum($column), 2, '.', '');
                }
            }
            $finalData['labels'] = @$data['labels'];
            $finalData['data'][] = 
            [
                "label" => "Not found in catalog",
                "fill" => true,
                "lineTension" => 0.5,
                "backgroundColor" => generateRandomColor(),
                "borderColor" => "#3c4ccf",
                "borderCapStyle" => "butt",
                "borderDash" => [],
                "borderDashOffset" => 0.0,
                "borderJoinStyle" => "miter",
                "pointBorderColor" => "#3c4ccf",
                "pointBackgroundColor" => "#fff",
                "pointBorderWidth" => 1,
                "pointHoverRadius" => 5,
                "pointHoverBackgroundColor" => "#3c4ccf",
                "pointHoverBorderColor" => "#fff",
                "pointHoverBorderWidth" => 2,
                "pointRadius" => 1,
                "pointHitRadius" => 1000,
                "data" => @$data['no_category']
            ];
            foreach($products as $product){

                $finalData['data'][] = 
                [
                    "label" => $product->name,
                    "fill" => true,
                    "lineTension" => 0.5,
                    "backgroundColor" => generateRandomColor(),
                    "borderColor" => "#3c4ccf",
                    "borderCapStyle" => "butt",
                    "borderDash" => [],
                    "borderDashOffset" => 0.0,
                    "borderJoinStyle" => "miter",
                    "pointBorderColor" => "#3c4ccf",
                    "pointBackgroundColor" => "#fff",
                    "pointBorderWidth" => 1,
                    "pointHoverRadius" => 5,
                    "pointHoverBackgroundColor" => "#3c4ccf",
                    "pointHoverBorderColor" => "#fff",
                    "pointHoverBorderWidth" => 2,
                    "pointRadius" => 1,
                    "pointHitRadius" => 1000,
                    "data" => @$data['product_'.$product->id]
                ];
            }
            foreach($services as $service){

                $finalData['data'][] = 
                [
                    "label" => $service->name,
                    "fill" => true,
                    "lineTension" => 0.5,
                    "backgroundColor" => generateRandomColor(),
                    "borderColor" => "#3c4ccf",
                    "borderCapStyle" => "butt",
                    "borderDash" => [],
                    "borderDashOffset" => 0.0,
                    "borderJoinStyle" => "miter",
                    "pointBorderColor" => "#3c4ccf",
                    "pointBackgroundColor" => "#fff",
                    "pointBorderWidth" => 1,
                    "pointHoverRadius" => 5,
                    "pointHoverBackgroundColor" => "#3c4ccf",
                    "pointHoverBorderColor" => "#fff",
                    "pointHoverBorderWidth" => 2,
                    "pointRadius" => 1,
                    "pointHitRadius" => 1000,
                    "data" => @$data['service_'.$service->id]
                ];
            }
            foreach($expenses as $expense){

                $finalData['data'][] = 
                [
                    "label" => $expense->name,
                    "fill" => true,
                    "lineTension" => 0.5,
                    "backgroundColor" => generateRandomColor(),
                    "borderColor" => "#3c4ccf",
                    "borderCapStyle" => "butt",
                    "borderDash" => [],
                    "borderDashOffset" => 0.0,
                    "borderJoinStyle" => "miter",
                    "pointBorderColor" => "#3c4ccf",
                    "pointBackgroundColor" => "#fff",
                    "pointBorderWidth" => 1,
                    "pointHoverRadius" => 5,
                    "pointHoverBackgroundColor" => "#3c4ccf",
                    "pointHoverBorderColor" => "#fff",
                    "pointHoverBorderWidth" => 2,
                    "pointRadius" => 1,
                    "pointHitRadius" => 1000,
                    "data" => @$data['expense_'.$expense->id]
                ];
            }
        }else{
            $finalData = [];
            foreach($dates as $date){
                $finalData['labels'][] =  $date['name'];
            }
            $arr = [];
            $arr['name'] = "Not found in catalog";
            $arr['reference'] = "-";
            $arr['total'] = 0;
            foreach($dates as $date){
                $amount = number_format(PurchaseTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date'], 'client_id' =>$request->client, 'agent_id' =>$request->agent_id , 'product_id' => $request->product_id])->whereDoesntHave('products')->get()->sum($column), 2, '.', '');
                $arr['data'][] =  $amount;
                $arr['total'] = $arr['total']+ $amount;
            }
            

            $finalData['data'][] = $arr;
            foreach($products as $product){
                $arr = [];
                $arr['name'] = $product->name;
                $arr['reference'] = $product->reference.''.$product->reference_number;
                $arr['total'] = 0;
                foreach($dates as $date){
                    $amount =  number_format(PurchaseTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date'], 'client_id' => $request->client_id, 'agent_id' =>$request->agent_id , 'product_id' => $product->id])->whereIn('reference', $referenceType)->get()->sum($column), 2, '.', '');
                    $arr['data'][] =  $amount;
                    $arr['total'] = $arr['total']+ $amount;
                }
                
    
                $finalData['data'][] = $arr;
            }
            foreach($services as $service){
                $arr = [];
                $arr['name'] = $service->name;
                $arr['reference'] = $service->reference.''.$service->reference_number;
                $arr['total'] = 0;
                foreach($dates as $date){
                    $amount = number_format(PurchaseTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date'], 'client_id' => $request->client_id, 'agent_id' =>$request->agent_id , 'service_id' => $service->id])->whereIn('reference', $referenceType)->get()->sum($column), 2, '.', '');
                    $arr['data'][] =  $amount;
                    $arr['total'] = $arr['total']+ $amount;
                }
                
    
                $finalData['data'][] = $arr;
            }
            foreach($expenses as $expense){
                $arr = [];
                $arr['name'] = $expense->name;
                $arr['reference'] = $expense->reference.''.$expense->reference_number;
                $arr['total'] = 0;
                foreach($dates as $date){
                    $amount = number_format(PurchaseTable::filter(['dateStartDate' =>$date['start_date'] ,'dateEndDate' => $date['end_date'], 'client_id' => $request->client_id, 'agent_id' =>$request->agent_id , 'expense_id' => $expense->id])->whereIn('reference', $referenceType)->get()->sum($column), 2, '.', '');
                    $arr['data'][] =  $amount;
                    $arr['total'] = $arr['total']+ $amount;
                }
                
    
                $finalData['data'][] = $arr;
            }
        }
        return response()->json([
            'status' => true,
            'data' => $finalData
        ]);
    }
}
