<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseTicket;
use Validator;
use Storage;

class PurchaseTicketController extends Controller
{
    public function index(Request $request)
    {
        if(($request->company_id ==  NULL)||($request->company_id ==  0)){
            return response()->json([
                "status" => false,
                "message" =>  "Please select company"
            ]);
        }

        $table = 'company_'.$request->company_id.'_purchase_tickets';
        PurchaseTicket::setGlobalTable($table);

        // if($request->supplier_id){
        //     $purchase_ticket = PurchaseTicket::where('supplier_id' , $request->supplier_id)->get();
        // }else{
        //     $purchase_ticket = PurchaseTicket::get();
        // }
        $query = PurchaseTicket::query();

        if($request->search){
            $query = $query->where('reference', 'like', '%'.$request->search.'%')->orWhere('reference_number', 'like', '%'.$request->search.'%');
        }
        if($request->supplier_id){
            $query = $query->where('supplier_id', $request->supplier_id);
        }
        $purchase_ticket = $query->get();

        if($purchase_ticket->count() == 0) {
            return response()->json([
                "status" => false,
                "message" => "No data found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "data" =>  $purchase_ticket
            ]);  
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'supplier_id' => 'required',
            'reference' => 'required',
        ], [
            'supplier_id.required' => 'Please select supplier.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        //change format of date
        if($request->payment_date){

            $request['payment_date'] = get_formatted_datetime($request->payment_date);
        }
        if($request->date){

            $request['date'] = get_formatted_datetime($request->date);
        }

        $table = 'company_'.$request->company_id.'_purchase_tickets';
        // return $table;
        PurchaseTicket::setGlobalTable($table);

        if ($request->reference_number == '') {

            $request['reference_number'] = get_purchase_ticket_table_latest_ref_number($request->company_id, $request->reference, 1 );
        }else{

            $purchase_ticket = PurchaseTicket::where('reference', $request->reference)->where('reference_number', $request->reference_number)->first();

            if ($purchase_ticket) {
                $request->reference_number = '';
            }
        }

        if( $request->reference_number  ){
            $purchase_ticket = PurchaseTicket::create($request->except('company_id'));
            $purchase_ticket->save();

            return response()->json([
                "status" => true,
                "data" => PurchaseTicket::find($purchase_ticket->id),
                "message" => "Saved successfully"
            ]);
        }else{
            return response()->json([
                "status" => false,
                "message" => "Please choose different reference number"
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_purchase_tickets';
        PurchaseTicket::setGlobalTable($table);

        $purchase_ticket = PurchaseTicket::where('id', $request->purchase_ticket)->first();

        if($purchase_ticket ==  NULL){
            return response()->json([
                "status" => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "data" => $purchase_ticket
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $table = 'company_'.$request->company_id.'_purchase_tickets';
        PurchaseTicket::setGlobalTable($table);
        $purchase_ticket = PurchaseTicket::where('id', $request->purchase_ticket)->first();
        
         //change format of date
        if($request->date){

            $request['date'] = get_formatted_datetime($request->date);
        }
        if($request->payment_date){

            $request['payment_date'] = get_formatted_datetime($request->payment_date);
        }

        $purchase_ticket->update($request->except('company_id', 'purchase_ticket', '_method'));
        $purchase_ticket->save();

        return response()->json([
            "status" => true,
            "data" => $purchase_ticket,
            "message" => "Updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $table = 'company_'.$request->company_id.'_purchase_tickets';
        PurchaseTicket::setGlobalTable($table);
        $purchase_ticket = PurchaseTicket::where('id', $request->purchase_ticket)->first();

        if ($purchase_ticket == NULL) {
            return response()->json([
                'status' => false,
                'message' => "Entry not exist!"
            ]);
        }    

        if($purchase_ticket->delete()){
            return response()->json([
                'status' => true,
                'message' => "Entry deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "There is an error!"
            ]);
        }
    }
    public function batchDelete(Request $request){
        $table = 'company_'.$request->company_id.'_purchase_tickets';
        $validator = Validator::make($request->all(), [
            'ids' => 'required'
        ],[
            'ids.required' => 'Please select entry to delete'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        PurchaseTicket::setGlobalTable($table);
        $ids = explode(",", $request->ids);
        PurchaseTicket::whereIn('id', $ids)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Clients deleted successfully'
        ]);
    }
    public function duplicate(Request $request){
        $table = 'company_'.$request->company_id.'_purchase_tickets';

        $validator = Validator::make($request->all(),[
            'id'=>'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        
        PurchaseTicket::setGlobalTable($table);

        $purchaseTicket = PurchaseTicket::find($request->id);
        if(!$purchaseTicket){
            return response()->json([
                'status' => false,
                'message' => 'Client Not found!'
            ]);
        }
        // dd($client->client_attachments);
        $duplicatedTicket = $purchaseTicket->replicate();
        $duplicatedTicket->created_at = now();
        $duplicatedTicket->reference_number = get_purchase_ticket_table_latest_ref_number($request->company_id, $purchaseTicket->reference, 1 );
        $duplicatedTicket->save();

        return response()->json([
            'status' => true,
            'message' => 'Duplicate Ticket successfully',
            'data' =>$duplicatedTicket
        ]);
    }
}
