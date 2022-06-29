<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TechnicalTable;
use Validator;
use Storage;

class TechnicalTableController extends Controller
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

        if(!$request->type){
            return response()->json([
                "status" => false,
                "message" =>  "Please select type"
            ]);
        }

        $table = 'company_'.$request->company_id.'_technical_tables';
        TechnicalTable::setGlobalTable($table);

        $technical_incidents = TechnicalTable::where('type', $request->type)->get();

        if($technical_incidents->count() == 0) {
            return response()->json([
                "status" => false,
                "message" => "No data found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "data" =>  $technical_incidents
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
            'client_id' => 'required',
            'type' => 'required'
        ], [
            'client_id.required' => 'Please select client.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_technical_tables';
        TechnicalTable::setGlobalTable($table);

        if ($request->reference_number == '') {
            $request['reference_number'] = get_technical_table_latest_ref_number($request->company_id, $request->reference, 1 , $request->type);
        }else{
            $technical_incident = TechnicalTable::where('reference', $request->reference)->where('reference_number', $request->reference_number)->first();

            if ($technical_incident) {
                $request->reference_number = '';
            }
        }

        if( $request->reference_number  ){

            $technical_incidents = TechnicalTable::create($request->except('company_id'));
            $technical_incidents->created_by = Auth::id();
            $technical_incidents->save();

            return response()->json([
                "status" => true,
                "data" => $technical_incidents,
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
        $table = 'company_'.$request->company_id.'_technical_tables';
        TechnicalTable::setGlobalTable($table);
        $technical_incident = TechnicalTable::where('id', $request->technical_table)->first();

        if($technical_incidents ==  NULL){
            return response()->json([
                "status" => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "data" => $technical_incident
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
        $table = 'company_'.$request->company_id.'_technical_tables';
        TechnicalTable::setGlobalTable($table);
        $technical_incident = TechnicalTable::where('id', $request->technical_table)->first();
        
        $technical_incident->update($request->except('company_id', 'technical_table', '_method'));
        $technical_incident->create_by = Auth::id();
        $technical_incident->save();

        return response()->json([
            "status" => true,
            "data" => $technical_incident,
            "message" => "Incident updated successfully"
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
        $table = 'company_'.$request->company_id.'_technical_table';
        TechnicalTable::setGlobalTable($table);
        $technical_incident = TechnicalTable::where('id', $request->technical_table)->first();
        if ($technical_incident == NULL) {
            return response()->json([
                'status' => false,
                'message' => "Entry not exist!"
            ]);
        }

        if($technical_incident->delete()){
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
}
