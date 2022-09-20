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

        if($request->type ){
            $query->where('type' , $request->type);
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
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'type' => 'required',
            'prefix' => 'required',       
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $reference_table = 'company_'.$request->company_id.'_references';
        Reference::setGlobalTable($reference_table);

        $reference = Reference::create($request->except('company_id'));

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
        Reference::setGlobalTable($table);
        $reference = Reference::where('id', $request->reference)->first();
        $reference->update($request->except('company_id', '_method'));
        $reference->save();

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
        if($reference->delete()){

            return response()->json([
                    'status' => true,
                    'message' => "Deleted successfully!"
            ]);
        } else {
            return response()->json([
                    'status' => false,
                    'message' => "Retry deleting again!"
            ]);
        }
    }
}
