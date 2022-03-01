<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role =  new Role;
        return response()->json([
            "status" => true,
            "roles" =>  $role->setTable('company_'.$request->company_id.'_roles')->get()
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
            'name' => 'required'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $role =  new Role;
        $role = $role->setTable('company_'.$request->company_id.'_roles')->create($request->except('company_id'));

        return response()->json([
            "status" => true,
            "role" => $role,
            "message" => "Role created successfully"
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
        $role =  new Role;
        $role = $role->setTable('company_'.$request->company_id.'_roles')->where('id', $request->role)->first();
 
        return response()->json([
            "status" => true,
            "role" => $role
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
            'name' => 'required'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $role =  new Role;
        $role = $role->setTable('company_'.$request->company_id.'_roles')->where('id', $request->role)->first();
        
        $role->update($request->except('company_id', '_method'));

        return response()->json([
            "role" => $role
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
        $role =  new Role;
        $role = $role->setTable('company_'.$request->company_id.'_roles')->where('id', $request->role)->first();
        if($role->delete()){
            return response()->json([
                    'status' => true,
                    'message' => "Role deleted successfully!"
            ]);
        } else {
            return response()->json([
                    'status' => false,
                    'message' => "Retry deleting again! "
            ]);
        }
    }
}
