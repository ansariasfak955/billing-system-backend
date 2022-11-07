<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceRate;
use Validator;
class ServiceRateController extends Controller
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
        ServiceRate::setGlobalTable('company_'.$request->company_id.'_service_rates');
        $query = ServiceRate::query();

        if($request->service_id){
            $query = $query->where('service_id', ($request->service_id));
        }

        $services = $query->get();
        if( !count($services)){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "rates" => $services
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
        $table = 'company_'.$request->company_id.'_service_rates';
       ServiceRate::setGlobalTable($table);
        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:'.$table.'',
            'service_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        
        $rate =ServiceRate::create($request->except('company_id'));

        return response()->json([
            "status" => true,
            "rate" => $rate,
            "message" => "Rate created successfully"
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
       ServiceRate::setGlobalTable('company_'.$request->company_id.'_service_rates');
        $rate =ServiceRate::where('id', $request->service_rate)->first();

        if($rate ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "rate" => $rate
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
       ServiceRate::setGlobalTable('company_'.$request->company_id.'_service_rates');
        $validator = Validator::make($request->all(),[
            'name' => 'required' ,
            'service_id' => 'required',        
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $rate = ServiceRate::where('id', $request->service_rate)->first();
        
        $updateRate = $rate->update($request->except('company_id', '_method'));
        // dd($updateRate);

        return response()->json([
            "status" => true,
            "rate" => $rate
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
        ServiceRate::setGlobalTable('company_'.$request->company_id.'_service_rates');
        $rate =ServiceRate::where('id', $request->service_rate)->first();
        if($rate->delete()){
            return response()->json([
                'status' => true,
                'message' => "Rate deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again! "
            ]);
        }
    }
}
