<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\InvoiceTable;
use App\Models\SalesEstimate;
use App\Models\TechnicalTable;
use Illuminate\Http\Request;

class RelatedController extends Controller
{
    public function related(Request $request){
        $clientsTables = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientsTables); 

        $invoiceTable = 'company_'.$request->company_id.'_invoice_tables';
        InvoiceTable::setGlobalTable($invoiceTable);

        $salesTable = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($salesTable);

        $technicalTable = 'company_'.$request->company_id.'_technical_tables';
        TechnicalTable::setGlobalTable($technicalTable);

        $data = [];
        $salesDatas = SalesEstimate::where('client_id', $request->client_id)->take(2)->get();
        $invoiceDatas = InvoiceTable::where('client_id', $request->client_id)->take(2)->get();
        $technicalDatas = TechnicalTable::where('client_id', $request->client_id)->take(2)->get();

        if($request->type == "incidents"){
            $technicalDatas = TechnicalTable::where('client_id', $request->client_id)->take(10)->get();
            foreach($technicalDatas as $technicalData){
                $arr['id'] = $technicalData->id;
                $arr['reference_number'] = $salesData->reference_number;
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
        }else{
            foreach($salesDatas as $salesData){
                $arr['id'] = $salesData->id;
                $arr['reference_number'] = $salesData->reference_number;
                $arr['reference'] = $salesData->reference;
                $arr['reference_type'] = $salesData->reference_type;
                $arr['title'] = $salesData->title;
                $arr['created_by'] = $salesData->created_by;
                $arr['status'] = $salesData->status;
                $arr['type'] = $salesData->reference;
                $arr['date'] = $salesData->date;
                $arr['amount'] = $salesData->amount;
                $data[] = $arr;
            }
    
            foreach($invoiceDatas as $invoiceData){
                $arr['id'] = $invoiceData->id;
                $arr['reference_number'] = $invoiceData->reference_number;
                $arr['reference'] = $invoiceData->reference;
                $arr['reference_type'] = $invoiceData->reference_type;
                $arr['title'] = $invoiceData->title;
                $arr['created_by'] = $invoiceData->created_by;
                $arr['status'] = $invoiceData->status;
                $arr['type'] = $invoiceData->reference;
                $arr['date'] = $invoiceData->date;
                $arr['amount'] = $invoiceData->amount;
                $data[] = $arr;
            }
    
            foreach($technicalDatas as $technicalData){
                $arr['id'] = $technicalData->id;
                $arr['reference_number'] = $technicalData->reference_number;
                $arr['reference'] = $technicalData->reference;
                $arr['reference_type'] = $technicalData->reference_type;
                $arr['title'] = $technicalData->title;
                $arr['created_by'] = $technicalData->created_by;
                $arr['status'] = $technicalData->status;
                $arr['type'] = $technicalData->reference;
                $arr['date'] = $technicalData->date;
                $arr['amount'] = $technicalData->amount;
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
