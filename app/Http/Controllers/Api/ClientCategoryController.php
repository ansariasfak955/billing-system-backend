<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClientCategory;
use Validator;
class ClientCategoryController extends Controller
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

        $table = 'company_'.$request->company_id.'_client_categories';
        ClientCategory::setGlobalTable($table);

        $client_categories = ClientCategory::query();
        
        if($request->client_id){
            $client_categories = $client_categories->where('client_id', $request->client_id);
        }

        $client_categories = $client_categories->filter($request->all())->get();
        if ($client_categories->count() == 0) {
            return response()->json([
                "status" => false,
                "message" => "No category found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "client_categories" =>  $client_categories
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
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $table = 'company_'.$request->company_id.'_client_categories';
        ClientCategory::setGlobalTable($table);

        $client_category = ClientCategory::create($request->except('company_id'));

        return response()->json([
            "status" => true,
            "client_category" => $client_category,
            "message" => "Client category created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClientCategory  $ClientCategory
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $table = 'company_'.$request->company_id.'_client_categories';
        ClientCategory::setGlobalTable($table);
        $client_category = ClientCategory::where('id', $request->client_category)->first();

        if($client_category ==  NULL){
            return response()->json([
                "status" => false,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "client_category" => $client_category
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClientCategory  $ClientCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $table = 'company_'.$request->company_id.'_client_categories';
        ClientCategory::setGlobalTable($table);
        $client_category = ClientCategory::where('id', $request->client_category)->first();
        
        $client_category->update($request->except('company_id'));
        $client_category->save();

        return response()->json([
            "status" => true,
            "client" => $client_category,
            "message" => "Client category updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClientCategory  $ClientCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $table = 'company_'.$request->company_id.'_client_categories';
        ClientCategory::setGlobalTable($table);
        $client_category = ClientCategory::where('id', $request->client_category)->first();

        if($client_category->delete()){
            return response()->json([
                'status' => true,
                'message' => "Client category deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "There is an error!"
            ]);
        }
    }
    public function batchDelete(Request $request){
        $table = 'company_'.$request->company_id.'_client_categories';
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
        ClientCategory::setGlobalTable($table);
        $ids = explode(",", $request->ids);
        ClientCategory::whereIn('id', $ids)->delete();
        return response()->json([
            'status' => true,
            'message' => 'ClientCategory deleted successfully'
        ]);

    }
}
