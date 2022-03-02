<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomState;
use App\Models\CustomStateType;
use Validator;

class CustomStateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $custom_state =  new CustomState;
        CustomState::setGlobalTable('company_'.$request->company_id.'_custom_states') ;
        return response()->json([
            "status" => true,
            "custom_states" =>  $custom_state->setTable('company_'.$request->company_id.'_custom_states')->get()
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
            'type_id' => 'required|numeric'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $custom_state =  new CustomState;
        CustomState::setGlobalTable('company_'.$request->company_id.'_custom_states') ;
        $custom_state = $custom_state->setTable('company_'.$request->company_id.'_custom_states')->create($request->except('company_id'));

        return response()->json([
            "status" => true,
            "custom_state" => $custom_state,
            "message" => "Custom state created successfully"
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
        $custom_state =  new CustomState;
        CustomState::setGlobalTable('company_'.$request->company_id.'_custom_states') ;
        $custom_state = $custom_state->setTable('company_'.$request->company_id.'_custom_states')->where('id', $request->custom_state)->first();

        return response()->json([
            "status" => true,
            "custom_state" => $custom_state
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

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'type_id' => 'required|numeric'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $custom_state =  new CustomState;
        CustomState::setGlobalTable('company_'.$request->company_id.'_custom_states') ;
        $custom_state = $custom_state->setTable('company_'.$request->company_id.'_custom_states')->where('id', $request->custom_state)->first();
        
        $custom_state->update($request->except('company_id', '_method'));
        $custom_state->save();

        return response()->json([
            "status" => true,
            "custom_state" => $custom_state,
            "message" => "Custom state updated successfully"
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
        $custom_state =  new CustomState;
        CustomState::setGlobalTable('company_'.$request->company_id.'_custom_states') ;
        $custom_state = $custom_state->setTable('company_'.$request->company_id.'_custom_states')->where('id', $request->custom_state)->first();

        if($custom_state->delete()){
            return response()->json([
                    'status' => true,
                    'message' => "Custom state deleted successfully!"
            ]);
        } else {
            return response()->json([
                    'status' => false,
                    'message' => "Retry deleting again! "
            ]);
        }
    }


    /* Get custom state types */
    public function getCustomStateTypes(Request $request)
    {
        $custom_state_type =  new CustomStateType;
        CustomStateType::setGlobalTable('company_'.$request->company_id.'_custom_state_types') ;
        CustomState::setGlobalTable('company_'.$request->company_id.'_custom_states') ;

        if(isset($request->custom_state_type_id)){
            return response()->json([
                'status' => true,
                'types' => $custom_state_type->setTable('company_'.$request->company_id.'_custom_state_types')->where('id', $request->custom_state_type_id)->with('states')->first()
            ]);
        }
        return response()->json([
                'status' => true,
                'types' => $custom_state_type->setTable('company_'.$request->company_id.'_custom_state_types')->with('states')->get()
        ]);
    }
}
