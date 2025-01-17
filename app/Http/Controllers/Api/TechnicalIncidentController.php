<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TechnicalIncident;
use App\Models\Client;
use App\Models\Reference;
use App\Models\Company;
use App\Exports\TechnicalIncidentExport;
use Maatwebsite\Excel\Facades\Excel;
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

        $clientTable = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($clientTable);

        $query = TechnicalIncident::query();    
        
        //set reference table
        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);
        if($request->type){
            //get dynamic reference
            $refernce_ids = Reference::where('type', urldecode($request->type))->pluck('prefix')->toArray();
            $query = $query->whereIn('reference', $refernce_ids);
        }
        $query = $query->filter($request->all())->orderBy('created_at', 'desc')->get();
        if (!count($query)) {
            return response()->json([
                "status" => false,
                "message" => "No clients found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "clients" =>  $query
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
        $company = Company::where('id', $request->company_id)->get()->first();
        if(!$company->address){
            return response()->json([
                "status" => false,
                "message" => 'Mandatory data in the company should be filled for creating a document.'
            ]);
        }elseif(!$company->city){
            return response()->json([
                "status" => false,
                "message" => 'Mandatory data in the company should be filled for creating a document.'
            ]);
        }


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
        if($request->closing_date){

            $request['closing_date'] = get_formatted_datetime($request->closing_date);
        }
        if($request->valid_until){

            $request['valid_until'] = get_formatted_datetime($request->valid_until);
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
            // $technical_incidents->created_by = Auth::id();
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

        if($technical_incident ==  NULL){
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

        $technical_incident = TechnicalIncident::where('id', $request->technical_incident)->first();
        
        $technical_incident->update($request->except('company_id', 'technical_incident', '_method'));
        $technical_incident->created_by = Auth::id();
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

        if($technical_incident->status == 'closed' || $technical_incident->status == 'Closed'){
            return response()->json([
                'status' => false,
                'message' => 'Closed Incident cannot be deleted!'
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
    public function batchDelete(Request $request){
        $table = 'company_'.$request->company_id.'_technical_incidents';
        $validator = Validator::make($request->all(),[
            'ids'=>'required',
        ],[
            'ids.required' => 'Please select entry to delete'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        TechnicalIncident::setGlobalTable($table);
        $ids = explode(",", $request->ids);
        TechnicalIncident::whereIn('id', $ids)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Clients deleted successfully'
        ]);
    }
    public function duplicate(Request $request){
        $table = 'company_'.$request->company_id.'_technical_incidents';
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        TechnicalIncident::setGlobalTable($table);
        $duplicateIncident = TechnicalIncident::find($request->id);
        if(!$duplicateIncident){
            return response()->json([
                'status' =>false,
                'message' => 'Technical Incident not found',
            ]);
        }
        $duplicateIncidents = $duplicateIncident->replicate();
        $duplicateIncidents->created_at = now();
        $duplicateIncidents->reference_number = get_technical_incident_latest_ref_number($request->company_id, $duplicateIncident->reference, 1);
        $duplicateIncidents->save();

        return response()->json([
            'status' => true,
            'message' => 'Duplicate Technical Incident Successfullu',
            'data' => $duplicateIncidents
        ]);
    }
    public function technicalIncidentExport(Request $request, $company_id){
        $validator = Validator::make($request->all(), [
            'ids' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        $table = 'company_'.$request->company_id.'_technical_incidents';
        TechnicalIncident::setGlobalTable($table);
        $client = 'company_'.$request->company_id.'_clients';
        Client::setGlobalTable($client);

        $fileName = 'Incidents-'.time().$company_id.'.xlsx';
        $ids = explode(',', $request->ids);
        $incidents = TechnicalIncident::with('client')->whereIn('id', $ids)->get();
        Excel::store(new TechnicalIncidentExport($incidents), 'public/xlsx/'.$fileName);

        return response()->json([
            'status' => true,
            'url' => url('/storage/xlsx/'.$fileName),
        ]);

    }
}