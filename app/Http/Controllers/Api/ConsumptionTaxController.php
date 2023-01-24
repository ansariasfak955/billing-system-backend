<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConsumptionTax;
use Validator;
class ConsumptionTaxController extends Controller
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
        ConsumptionTax::setGlobalTable('company_'.$request->company_id.'_consumption_taxes');
        $query = ConsumptionTax::query();

        $taxes = $query->get();
        if( !count($taxes)){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "taxes" => $taxes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $table = 'company_'.$request->company_id.'_consumption_taxes';
        ConsumptionTax::setGlobalTable($table);
        $validator = Validator::make($request->all(),[
            'primary_name' => 'required',
            'by_default_in_sales' => "in:1,0",
            'by_default_in_purchases' => "in:1,0",
            'activate_secondary_tax' => "in:1,0",
            'secondary_by_default_in_sales' => "in:1,0",
            'secondary_by_default_in_purchases' => "in:1,0",
            'subtractive' => "in:1,0",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $tax =ConsumptionTax::create($request->except(['company_id', 'taxes']));
        
        if($request->taxes){
            $tax->taxes = json_encode($request->taxes);
            $tax->save();
        }

        return response()->json([
            "status" => true,
            "tax" => $tax,
            "message" => "Tax created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_consumption_taxes';
        ConsumptionTax::setGlobalTable($table);
        $tax = ConsumptionTax::find($request->cosumption_tax);

        if(!$tax){
            return response()->json([
                "status" => false,
                "message" => "Not Found"
            ]);
        }

        return response()->json([
            "status" => true,
            "tax" => $tax,
            "message" => "Tax found!"
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $table = 'company_'.$request->company_id.'_consumption_taxes';
        ConsumptionTax::setGlobalTable($table);
        $validator = Validator::make($request->all(),[
            'primary_name' => 'required',
            'by_default_in_sales' => "in:1,0",
            'by_default_in_purchases' => "in:1,0",
            'activate_secondary_tax' => "in:1,0",
            'secondary_by_default_in_sales' => "in:1,0",
            'secondary_by_default_in_purchases' => "in:1,0",
            'subtractive' => "in:1,0",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $tax = ConsumptionTax::find($request->cosumption_tax);
        $tax->update($request->except(['company_id', 'taxes']));
        
        if($request->taxes){
            $tax->taxes = json_encode($request->taxes);
            $tax->save();
        }

        return response()->json([
            "status" => true,
            "tax" => $tax,
            "message" => "Tax updated successfully"
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

        $table = 'company_'.$request->company_id.'_consumption_taxes';
        ConsumptionTax::setGlobalTable($table);

        $tax = ConsumptionTax::find($request->cosumption_tax);

        if(!$tax){
            return response()->json([
                "status" => false,
                "message" => "Not Found"
            ]);
        }

        $tax->delete();

        return response()->json([
            "status" => true,
            "message" => "Tax deleted!"
        ]);
    }
}
