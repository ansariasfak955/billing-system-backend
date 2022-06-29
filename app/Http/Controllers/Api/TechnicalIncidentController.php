<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TechnicalIncident;
use Illuminate\Http\Request;
use Validator;
use Auth;

class TechnicalIncidentController extends Controller
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

        $table = 'company_'.$request->company_id.'_technical_incidents';
        TechnicalIncident::setGlobalTable($table);
        $technical_incidents = TechnicalIncident::get();

        if($technical_incidents->count() == 0) {
            return response()->json([
                "status" => false,
                "message" => "No data found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "technical_incidents" =>  $technical_incidents
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
    	$notifications = json_encode(explode(',', $request->notifications), true);
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

        $table = 'company_'.$request->company_id.'_technical_incidents';
        TechnicalIncident::setGlobalTable($table);

        if ($request->reference_number == '') {
            $request['reference_number'] = get_technical_incident_latest_ref_number($request->company_id, $request->reference, 1);
        }else{
            $technical_incident = TechnicalIncident::where('reference', $request->reference)->where('reference_number', $request->reference_number)->first();

            if ($technical_incident) {
                $request->reference_number = '';
            }
        }

        if( $request->reference_number  ){

            $technical_incidents = TechnicalIncident::create($request->except('company_id', 'type'));
            $technical_incidents->created_by = Auth::id();
            $technical_incidents->notifications = $notifications;
            $technical_incidents->save();

            return response()->json([
                "status" => true,
                "technical_incidents" => $technical_incidents,
                "message" => "Incident created successfully"
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
     * @param  \App\Models\TechnicalIncident  $technicalIncident
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_technical_incidents';
        TechnicalIncident::setGlobalTable($table);
        $technical_incident = TechnicalIncident::where('id', $request->technical_incident)->first();

        if($technical_incidents ==  NULL){
            return response()->json([
                "status" => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "technical_incident" => $technical_incident
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TechnicalIncident  $technicalIncident
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $table = 'company_'.$request->company_id.'_technical_incidents';
        TechnicalIncident::setGlobalTable($table);
        $technical_incident = TechnicalIncident::where('id', $request->technical_incident)->first();
        
        $technical_incident->update($request->except('company_id', 'technical_incident', '_method'));
        $technical_incident->create_by = Auth::id();
        $technical_incident->save();

        return response()->json([
            "status" => true,
            "technical_incident" => $technical_incident,
            "message" => "Incident updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TechnicalIncident  $technicalIncident
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $table = 'company_'.$request->company_id.'_technical_incidents';
        TechnicalIncident::setGlobalTable($table);
        $technical_incident = TechnicalIncident::where('id', $request->technical_incident)->first();
        if ($technical_incident == NULL) {
            return response()->json([
                'status' => false,
                'message' => "Incident not exist!"
            ]);
        }

        if($technical_incident->delete()){
            return response()->json([
                'status' => true,
                'message' => "Incident deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "There is an error!"
            ]);
        }
    }
}