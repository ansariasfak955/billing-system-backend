<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientAsset;
use Illuminate\Http\Request;
use Validator;
class ClientAssetController extends Controller
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
        $table = 'company_'.$request->company_id.'_client_assets';
        ClientAsset::setGlobalTable($table);

        if($request->client_id == NULL){
            if(ClientAsset::count() == 0){
                return response()->json([
                    "status" => false,
                    "message" =>  "No data found"
                ]);
            }
            return response()->json([
                "status" => true,
                "client_assets" =>  ClientAsset::get()
            ]);
        }

        if(ClientAsset::where('client_id', $request->client_id)->count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "client_assets" =>  ClientAsset::where('client_id', $request->client_id)->get()
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
        $validator = Validator::make($request->all(),[
            'client_id' => 'required',          
            'name' => 'required',
            'reference' => "required",          
        ], [
            'client_id.required' => 'Please select client ',
            'name.required' => 'Please select name ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_client_assets';
        ClientAsset::setGlobalTable($table);


        if ($request->reference_number == '') {
            $request['reference_number'] = get_client_asset_latest_ref_number($request->company_id, $request->reference, 1);
        }else{
            $client_asset = ClientAsset::where('reference', $request->reference)->where('reference_number', $request->reference_number)->first();

            if ($client_asset) {
                $request->reference_number = '';
            }
        }
        if( $request->reference_number  ){

            $client_asset = ClientAsset::create($request->except('company_id', 'main_image'));

            if($request->main_image != NULL){
                $imageName = time().'.'.$request->main_image->extension();  
                $request->main_image->move(storage_path('app/public/clients/assets'), $imageName);
                $client_asset->main_image = $imageName;
                $client_asset->save();
            }

            return response()->json([
                "status" => true,
                "client" => $client_asset,
                "message" => "Client asset created successfully"
            ]);
        }

        return response()->json([
            "status"  => false,
            "message" => "Please choose different reference number"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClientAsset  $clientAsset
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_client_assets';
        ClientAsset::setGlobalTable($table);
        $client_asset = ClientAsset::where('id', $request->client_asset)->first();

        if($client_asset ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "client_asset" => $client_asset
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClientAsset  $clientAsset
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $table = 'company_'.$request->company_id.'_client_assets';
        ClientAsset::setGlobalTable($table);

        $validator = Validator::make($request->all(),[
            'client_id' => 'required',          
            'name' => 'required'          
        ], [
            'client_id.required' => 'Please select client ',
            'name.required' => 'Please select name ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $client_asset = ClientAsset::where('id', $request->client_asset)->first();
        
        $client_asset->update($request->except('company_id', '_method', 'main_image'));

        if($request->main_image != NULL){
            $imageName = time().'.'.$request->main_image->extension();  
            $request->main_image->move(storage_path('app/public/clients/assets'), $imageName);
            $client_asset->main_image = $imageName;
            $client_asset->save();
        }

        return response()->json([
            "status" => true,
            "client_asset" => $client_asset
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClientAsset  $clientAsset
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $table = 'company_'.$request->company_id.'_client_assets';
        ClientAsset::setGlobalTable($table);
        $client_asset = ClientAsset::where('id', $request->client_asset)->first();
        if($client_asset->delete()){
            return response()->json([
                    'status' => true,
                    'message' => "Client asset deleted successfully!"
            ]);
        } else {
            return response()->json([
                    'status' => false,
                    'message' => "Retry deleting again! "
            ]);
        }
    }
}
