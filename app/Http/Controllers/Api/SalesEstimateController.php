<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesEstimate;
use Validator;
use Storage;

class SalesEstimateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(($request->company_id ==  NULL)||($request->company_id ==  0)){
            return response()->json([
                "status" => false,
                "message" =>  "Please select company"
            ]);
        }

        $table = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($table);
        $sales_estimate = SalesEstimate::get();

        if ($sales_estimate->count() == 0) {
            return response()->json([
                "status" => false,
                "message" => "No sales estimate found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "sales_estimate" => $sales_estimate
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'client_id' => 'required'
        ], [
            'client_id.required' => 'Please select client.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        //change format of date
        if($request->date){

            $request['date'] = get_formatted_datetime($request->date);
        }

        if($request->assigned_date){

            $request['assigned_date'] = get_formatted_datetime($request->assigned_date);
        }

        if($request->start_date){

            $request['start_date'] = get_formatted_datetime($request->start_date);
        }

        if($request->end_date){

            $request['end_date'] = get_formatted_datetime($request->end_date);
        }
        if($request->due_date){

            $request['due_date'] = get_formatted_datetime($request->due_date);
        }
        if($request->valid_until){

            $request['valid_until'] = get_formatted_datetime($request->valid_until);
        }

        $table = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($table);
        $sales_estimate = SalesEstimate::create($request->except('company_id'));
        if ($request->signature) {
            $signature_name = time().'.'.$request->signature->extension();  
            $request->signature->move(storage_path('app/public/sales/signature'), $signature_name);
            $sales_estimate->signature = $signature_name;    
        }
        $sales_estimate->save();

        return response()->json([
            "status" => true,
            "sales_estimate" => $sales_estimate,
            "message" => "Sales estimate created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SalesEstimate  $salesEstimate
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($table);
        $sales_estimate = SalesEstimate::where('id', $request->sales_estimate)->first();

        if($sales_estimate ==  NULL){
            return response()->json([
                "status" => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "sales_estimate" => $sales_estimate
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SalesEstimate  $salesEstimate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $table = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($table);
        $sales_estimate = SalesEstimate::where('id', $request->sales_estimate)->first();
        
        //change format of date
        if($request->date){

            $request['date'] = get_formatted_datetime($request->date);
        }

        if($request->assigned_date){

            $request['assigned_date'] = get_formatted_datetime($request->assigned_date);
        }

        if($request->start_date){

            $request['start_date'] = get_formatted_datetime($request->start_date);
        }

        if($request->end_date){

            $request['end_date'] = get_formatted_datetime($request->end_date);
        }
        if($request->due_date){

            $request['due_date'] = get_formatted_datetime($request->due_date);
        }
        if($request->valid_until){

            $request['valid_until'] = get_formatted_datetime($request->valid_until);
        }

        $sales_estimate->update($request->except('company_id'));
        if ($request->signature) {
            $signature_name = time().'.'.$request->signature->extension();  
            $request->signature->move(storage_path('app/public/sales/signature'), $signature_name);
            $sales_estimate->signature = $signature_name;    
        }
        $sales_estimate->save();

        return response()->json([
            "status" => true,
            "sales_estimate" => $sales_estimate,
            "message" => "Sales estimate updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SalesEstimate  $salesEstimate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $table = 'company_'.$request->company_id.'_sales_estimates';
        SalesEstimate::setGlobalTable($table);
        $sales_estimate = SalesEstimate::where('id', $request->sales_estimate)->first();

        if ($sales_estimate == NULL) {
            return response()->json([
                'status' => false,
                'message' => "Sales estimate not exist!"
            ]);
        } else {
            if($sales_estimate->delete()){
                Storage::delete('public/sales-estimate/signature/'.$sales_estimate->signature.'');
                return response()->json([
                    'status' => true,
                    'message' => "Sales estimate deleted successfully!"
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => "There is an error!"
                ]);
            }    
        }
    }
}