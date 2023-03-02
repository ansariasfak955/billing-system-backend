<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reference;
use Validator;
use Storage;

class ReferenceController extends Controller
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
        // $product_category =  new ProductCategory;
        $table = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($table);
        $query =  Reference::query();
       
        if($request->type){
            $type = explode(',',$request->type);
            $query->where('type' , $type[0])->orWhere('type', $type[1]);
        }
        
        $data =  $query->get();
        
        if($data->count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "reference" => $data
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
        $reference_table = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($reference_table);
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'type' => 'required',
            'prefix' => 'required|unique:'.$reference_table,       
        ]);
        if(in_array($request->type, getReferenceTypes('template'))){
            $validator = Validator::make($request->all(),[
                'name' => 'required',
                'type' => 'required',
                'prefix' => 'required',       
                'prefix' => 'required|unique:'.$reference_table,      
                'template_id' => 'required',       
            ]);
        }
        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }


        $reference = Reference::create($request->except('company_id'));
        if($request->by_default == '1'){
            Reference::where('type', $request->type)->where('id', '!=', $reference->id)->update([
                'by_default' => '0'
            ]);
        }
        return response()->json([
            "status" => true,
            "reference" => $reference,
            "message" => "Reference created successfully"
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
        $reference =  new Reference;
        $reference = $reference->setTable('company_'.$request->company_id.'_references')->where('id', $request->reference)->first();

        if($reference ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "reference" => $reference
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
        $table = 'company_'.$request->company_id.'_references';
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'type' => 'required',
            'prefix' => "required|unique:$table,prefix,$request->reference"     
        ]);
        if(in_array($request->type, getReferenceTypes('template'))){
            $validator = Validator::make($request->all(),[
                'name' => 'required',
                'type' => 'required',
                'prefix' => "required|unique:$table,prefix,$request->reference"  ,       
                'title' => 'required',       
                'template_id' => 'required',       
            ]);
        }
        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        Reference::setGlobalTable($table);
        $reference = Reference::where('id', $request->reference)->first();
        $reference->update($request->except('company_id', '_method'));
        $reference->save();
        if($request->by_default == '1'){
            Reference::where('type', $request->type)->where('id', '!=', $reference->id)->update([
                'by_default' => '0'
            ]);
        }
        return response()->json([
            "status" => true,
            "reference" => $reference,
            "message" => "Reference updated successfully"
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
        $table = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($table);
        $reference = Reference::where('id', $request->reference)->first();
        if(!$reference){

            return response()->json([
                'status' => false,
                'message' => "Retry deleting again!"
           ]);
        } 
        if($reference->by_default == '1'){
            return response()->json([
                'status' => false,
                'message' => "Cannot delete by default reference!"
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => "Reference deleted successfully!"
        ]);
    }
    public function batchDelete(Request $request){
        $table = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($table);
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
        $ids = explode(",", $request->ids);
        Reference::whereIn('id', $ids)->where('by_default', '!=', '1')->delete(); 
        return response()->json([
            'status' => true,
            'message' => 'References deleted successfully'
        ]);
    }
}
