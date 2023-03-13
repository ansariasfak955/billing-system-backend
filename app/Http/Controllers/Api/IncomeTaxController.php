<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IncomeTax;
use Validator;
class IncomeTaxController extends Controller
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
        IncomeTax::setGlobalTable('company_'.$request->company_id.'_income_taxes');
        $query = IncomeTax::query();

        $taxes = $query->filter($request->all())->get();
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
        $table = 'company_'.$request->company_id.'_income_taxes';
        IncomeTax::setGlobalTable($table);
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'subtractive' => "in:1,0",
            'by_default' => "in:1,0",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        if($request->by_default == '1'){
            IncomeTax::where('by_default','1')->update([
                'by_default' => '0'
            ]);
        }
        $tax =IncomeTax::create($request->except(['company_id', 'taxes']));
        
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
        $table = 'company_'.$request->company_id.'_income_taxes';
        IncomeTax::setGlobalTable($table);
        $tax = IncomeTax::find($request->income_tax);

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
        $table = 'company_'.$request->company_id.'_income_taxes';
        IncomeTax::setGlobalTable($table);
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'subtractive' => "in:1,0",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $tax = IncomeTax::find($request->income_tax);
        if($request->by_default == '1'){
            IncomeTax::where('by_default','1')->update([
                'by_default' => '0'
            ]);
        }
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

        $table = 'company_'.$request->company_id.'_income_taxes';
        IncomeTax::setGlobalTable($table);

        $tax = IncomeTax::find($request->income_tax);

        if(!$tax){
            return response()->json([
                "status" => false,
                "message" => "Not Found"
            ]);
        }
        if($tax->by_default == '1'){
            return response()->json([
                "status" => false,
                "message" => "Cannot delete by default tax"
            ]);
        }

        $tax->delete();

        return response()->json([
            "status" => true,
            "message" => "Tax deleted!"
        ]);
    }
}