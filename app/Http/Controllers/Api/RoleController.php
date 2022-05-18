<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\UserController;
use Spatie\Permission\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Validator;
use Auth;

class RoleController extends Controller
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
        (new UserController())->setConfig($request->company_id);
       
        if(Role::count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "roles" =>  Role::get()
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
        (new UserController())->setConfig($request->company_id);

        $validator = Validator::make($request->all(),[
            'name' => 'required'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        if(Role::where('name', $request->name)->first() != NULL){
            return response()->json([
                "status"  => false,
                "message" => "Role with same name already exists"
            ]);
        }

       
        $role = Role::create($request->except('company_id', 'permissions'));

        if(isset($request->permissions)){
            foreach(Permission::get() as $permission){
                if(in_array($permission->id, $request->permissions)){ 
                    $role->givePermissionTo($permission);
                }
            }
        }

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
        (new UserController())->setConfig($request->company_id);
       
        $role = Role::where('id', $request->role)->first();

        $all_permissions = [];
        $permissions = Permission::where('parent_id', 0)->with('children')->get();

        foreach($permissions as $permission){
            if(count($permission->children) != 0){

                foreach($permission->children as $second_permission){
                    $exists = $role->permissions->contains($second_permission->id);
                    if($exists == true){
                        $second_permission->setAttribute('is_checked', 'yes');
                    } else {
                        $second_permission->setAttribute('is_checked', 'no');
                    } 
                    if(count($second_permission->children) != 0){
                    
                        foreach($second_permission->children as $third_permission){
                            $exists = $role->permissions->contains($third_permission->id);
                            if($exists == true){
                                $third_permission->setAttribute('is_checked', 'yes');
                            } else {
                                $third_permission->setAttribute('is_checked', 'no');
                            } 
                        }
                    }
                }

            }

            $exists = $role->permissions->contains($permission->id);
            if($exists == true){
                $permission->setAttribute('is_checked', 'yes');
            } else {
                $permission->setAttribute('is_checked', 'no');
            }
           
        }
 
        return response()->json([
            "status" => true,
            "role" => $role,
            "permissions" => $permissions,
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
        (new UserController())->setConfig($request->company_id);

        $validator = Validator::make($request->all(),[
            'name' => 'required'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        
        $role = Role::where('id', $request->role)->first();
        
        $role->update($request->except('company_id', '_method', 'permissions'));
        if(isset($request->permissions)){
            foreach(Permission::get() as $permission){
                if(in_array($permission->id, $request->permissions)){ 
                   if($role->hasPermissionTo($permission->name) != 1){
                        $role->givePermissionTo($permission);
                   }
                } else {
                    if($role->hasPermissionTo($permission->name) == 1){
                        $role->revokePermissionTo($permission);
                    }
                }
            }
        }

        return response()->json([
            "status" => true,
            "message" => "Role updated successfully!",
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
        (new UserController())->setConfig($request->company_id);

        $role = Role::where('id', $request->role)->first();
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

    /* 
    * Get All Permissions
    */
    public function getPermissions(Request $request)
    {
        $table = 'company_'.$request->company_id.'_permissions';
        Permission::setGlobalTable($table);
        
        return response()->json([
            'status' => true,
            'permissions' => Permission::where('parent_id', 0)->with('children')->get()
        ]);
    }
}