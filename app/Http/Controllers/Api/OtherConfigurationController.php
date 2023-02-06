<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OtherConfiguration;
use Validator;

class OtherConfigurationController extends Controller
{
    public function index(Request $request){

        $table = 'company_'.$request->company_id.'_other_configurations';
        OtherConfiguration::setGlobalTable($table);

            return response()->json([
                'status' => true,
                'data' =>  OtherConfiguration::whereNull('parent_id')->with('children')->get()
            ]);
    }
    public function update(Request $request){
        // $validator = Validator::make($request->all(), [
        //     'ids' => 'required',
        // ]);
        // if($validator->fails()){
        //     return response()->json([
        //         'status' => false,
        //         'message' => $validator->errors()->first()
        //     ]);
        // }
        $table = 'company_'.$request->company_id.'_other_configurations';
        OtherConfiguration::setGlobalTable($table);

        $configurationUpdate = OtherConfiguration::where('id', $request->other_configuration)->first();
        $configurationUpdate->update($request->except('company_id', '_method'));
        $configurationUpdate->save();

        return response()->json([
            'status' => true,
            'message' => 'Deposit update successfully',
            'data' => $configurationUpdate
        ]);
    }
}
