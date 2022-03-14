<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use Validator;

class ServiceController extends Controller
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
        Service::setGlobalTable('company_'.$request->company_id.'_services');
        if(Service::count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "products" =>  Service::get()
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
        Service::setGlobalTable('company_'.$request->company_id.'_services');
        $validator = Validator::make($request->all(),[
            'name' => 'required',          
            'price' => 'required|numeric'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

       
        $service = Service::create($request->except('image', 'company_id', 'images'));

        if($request->image != NULL){
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(storage_path('app/public/services/images'), $imageName);
            $service->image = $imageName;
            $service->save();
        }


        $service->is_active = $request->is_active??'1';
        $service->active_margin = $request->active_margin??'0';
        $service->is_promotional = $request->is_promotional??'0';
        $service->manage_stock = $request->manage_stock??'0';
        $service->save();

        return response()->json([
            "status" => true,
            "service" => $service,
            "message" => "Service created successfully"
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
        Service::setGlobalTable('company_'.$request->company_id.'_services');
        $service = Service::where('id', $request->service)->first();

        if($service ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "service" => $service
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Service::setGlobalTable('company_'.$request->company_id.'_services');
        $validator = Validator::make($request->all(),[
            'name' => 'required',          
            'price' => 'required|numeric'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        
        $service = Service::where('id', $request->service)->first();
        $service->where('id', $request->service)->update($request->except('image', 'company_id', 'images', '_method'));

        if($request->image != NULL){
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(storage_path('app/public/products/images'), $imageName);
            $service->image = $imageName;
            $service->save();
        }

        $service->is_active = $request->is_active??$service->is_active;
        $service->active_margin = $request->active_margin??$service->active_margin;
        $service->is_promotional = $request->is_promotional??$service->is_promotional;
        $service->manage_stock = $request->manage_stock??$service->manage_stock;
        $service->save();

        return response()->json([
            "status" => true,
            "service" => $service,
            "message" => "Service updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        Service::setGlobalTable('company_'.$request->company_id.'_services');
        $service = Service::where('id', $request->service)->first();
        if($service->delete()){
            return response()->json([
                    'status' => true,
                    'message' => "Service deleted successfully!"
            ]);
        } else {
            return response()->json([
                    'status' => false,
                    'message' => "Retry deleting again! "
            ]);
        }
    }
}
