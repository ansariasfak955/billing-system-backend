<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\InvoiceTable;
use App\Models\PurchaseTable;
use App\Models\SalesEstimate;
use App\Models\TechnicalTable;
use Illuminate\Http\Request;

class RelatedController extends Controller
{
    public function related(Request $request){

        $table = 'company_'.$request->company_id.'_purchase_tables';
        PurchaseTable::setGlobalTable($table);

        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables); 

        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTable);

        $salesTable = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($salesTable);

        $technicalTable = 'company_'.$request->company_id.'_technical_tables';
        TechnicalTable::setGlobalTable($technicalTable);

        $data = [];
        if($request->client_id){
            $salesDatas = SalesEstimate::where('client_id',$request->client_id)->latest('created_at')->get();
            $invoiceDatas = InvoiceTable::where('client_id', $request->client_id)->latest('created_at')->get();
            $technicalDatas = TechnicalTable::where('client_id',$request->client_id)->latest('created_at')->get();
            foreach($salesDatas as $salesData){
                $arr['id'] = $salesData->id;
                $arr['reference_number'] = $salesData->reference_number;
                $arr['reference'] = $salesData->reference;
                $arr['reference_type'] = $salesData->reference_type;
                $arr['description'] = $salesData->description;
                $arr['status'] = $salesData->status;
                $arr['assigned_to'] = $salesData->assigned_to;
                $arr['created_by'] = $salesData->created_by;
                $arr['date'] = $salesData->date;
                $arr['activity'] = $salesData->activity;
                $data[] = $arr;
            }
        
            foreach($invoiceDatas as $invoiceData){
                $arr['id'] = $invoiceData->id;
                $arr['reference_number'] = $invoiceData->reference_number;
                $arr['reference'] = $invoiceData->reference;
                $arr['reference_type'] = $invoiceData->reference_type;
                $arr['description'] = $invoiceData->description;
                $arr['status'] = $invoiceData->status;
                $arr['assigned_to'] = $invoiceData->assigned_to;
                $arr['created_by'] = $invoiceData->created_by;
                $arr['date'] = $invoiceData->date;
                $arr['activity'] = $invoiceData->activity;
                $data[] = $arr;
            }
        
            foreach($technicalDatas as $technicalData){
                $arr['id'] = $technicalData->id;
                $arr['reference_number'] = $technicalData->reference_number;
                $arr['reference'] = $technicalData->reference;
                $arr['reference_type'] = $technicalData->reference_type;
                $arr['description'] = $technicalData->description;
                $arr['status'] = $technicalData->status;
                $arr['assigned_to'] = $technicalData->assigned_to;
                $arr['created_by'] = $technicalData->created_by;
                $arr['date'] = $technicalData->date;
                $arr['activity'] = $technicalData->activity;
                $data[] = $arr;
            }
        
        }else if($request->supplier_id){
            $purchaseDatas = PurchaseTable::where('supplier_id',$request->supplier_id)->latest('created_at')->get();
            foreach($purchaseDatas  as $purchaseData){
                $arr['id'] = $purchaseData->id;
                $arr['reference_number'] = $purchaseData->reference_number;
                $arr['reference'] = $purchaseData->reference;
                $arr['reference_type'] = $purchaseData->reference_type;
                $arr['description'] = $purchaseData->description;
                $arr['status'] = $purchaseData->status;
                $arr['assigned_to'] = $purchaseData->assigned_to;
                $arr['created_by'] = $purchaseData->created_by;
                $arr['date'] = $purchaseData->date;
                $arr['activity'] = $purchaseData->activity;
                $data[] = $arr;
            }
        }

        if(count($data)) {
            return response()->json([
                "status" => true,
                "data" =>  $data
            ]);
        } else{
            return response()->json([
                "status" => false,
                "message" => "No data found!"
            ]);
        }
    }
}
