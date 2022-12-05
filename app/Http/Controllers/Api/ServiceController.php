<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Rate;
use App\Models\ServiceRate;
use App\Models\Reference;
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
        // Service::setGlobalTable('company_'.$request->company_id.'_services');
        // if(Service::count() == 0){
        //     return response()->json([
        //         "status" => false,
        //         "message" =>  "No data found"
        //     ]);
        // }
        // return response()->json([
        //     "status" => true,
        //     "products" =>  Service::get()
        // ]);
        $table = 'company_'.$request->company_id.'_services';
        Service::setGlobalTable($table);

        $service = Service::query();
        
        if($request->search){
            $service = $service->where('name', 'like', '%'.$request->search.'%')->orWhere('reference_number', 'like', '%'.$request->search.'%')
            ->orWhere('price', 'like', '%'.$request->search.'%');
        }
        //set reference table
        $referenceTable = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($referenceTable);
        if($request->type){
            //get dynamic reference
            $refernce_ids = Reference::where('type',urldecode($request->type))->pluck('prefix')->toArray();
            $service = $service->whereIn('reference', $refernce_ids);
        }
        $service = $service->get();
            
            
        if ( $service->count() == 0 ) {
            return response()->json([
                "status" => false,
                "message" => "No product found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "products" =>  $service
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
        if ($request->reference_number == '') {
            $service->reference_number = get_service_latest_ref_number($request->company_id, $request->reference, 1);
        } else {
            $service = Service::where('reference', $request->reference)->where('reference_number', $request->reference_number)->first();
            if ($service == NULL) {
                $service->reference_number = get_service_latest_ref_number($request->company_id, $request->reference, 0);
            } else {
                return response()->json([
                    "status"  => false,
                    "client"  => $service,
                    "message" => "Please choose different reference number"
                ]);
            }
        }
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

        $rateTable = 'company_'.$request->company_id.'_rates';
        Rate::setGlobalTable($rateTable);
        $table = 'company_'.$request->company_id.'_service_rates';
        ServiceRate::setGlobalTable($table);

        $allRates = Rate::get();

        foreach($allRates as $rate){
            $product_rate = ServiceRate::create(['name' =>  $rate->name, 'description' => $rate->description, 'service_id' => $service->id]);
            $product_rate->purchase_price = $service->purchase_price;
            $product_rate->sales_price = $service->price;
            $product_rate->discount = $service->discount;
            $product_rate->purchase_margin = $service->purchase_margin;
            $product_rate->sales_margin = $service->sales_margin;
            $product_rate->save();
        }

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
    public function batchDelete(Request $request){
        $table = 'company_'.$request->company_id.'_services';
        $validator = Validator::make($request->all(), [
            'ids' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        Service::setGlobalTable($table);
        $ids = explode(",", $request->ids);
        Service::whereIn('id', $ids)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Deleted Successfully',
        ]);
    }
    public function duplicate(Request $request){
        $table = 'company_'.$request->company_id.'_services';
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        Service::setGlobalTable($table);
        $service = Service::find($request->id);
        if(!$service){
            return response()->json([
                'status' => false,
                'message' => 'Service not found'
            ]);
        }
        $duplicateService = $service->replicate();
        $duplicateService->created_at = now();
        $duplicateService->save();
        return response()->json([
            'status' => true,
            'message' => 'Duplicate Service Successfully',
            'data' => $service
        ]);
    }
}
