<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\UserController;
// use Spatie\Permission\Models\Role;
use App\Models\Role;
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

        $roles_table = 'company_'.$request->company_id.'_roles';
        Role::setGlobalTable($roles_table);
       
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

        $roles_table = 'company_'.$request->company_id.'_roles';
        Role::setGlobalTable($roles_table);

        $permissions_table = 'company_'.$request->company_id.'_permissions';
        Permission::setGlobalTable($permissions_table);

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
                    // $role->givePermissionTo($permission);
                    $role_has_permissions = "company_".$request->company_id."_role_has_permissions";
                    \DB::table($role_has_permissions)->insert([
                        'permission_id' => $permission->id,
                        'role_id' => $role->id,
                    ]);
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
        $roles_table = 'company_'.$request->company_id.'_roles';
        Role::setGlobalTable($roles_table);
        $role = Role::where('id', $request->role)->first();

        $role_has_permissions = "company_".$request->company_id."_role_has_permissions";

        $permissions_table = 'company_'.$request->company_id.'_permissions';
        Permission::setGlobalTable($permissions_table);

        $all_permissions = [];
        $permissions = Permission::where('parent_id', 0)->with('children')->get();

        foreach($permissions as $permission){
            if(count($permission->children) != 0){

                foreach($permission->children as $second_permission){
                    $exists = \DB::table($role_has_permissions)->where('role_id' , $role->id)->where('permission_id' , $second_permission->id)->first();
                    if( $exists ){
                        $second_permission->setAttribute('is_checked', 'yes');
                    } else {
                        $second_permission->setAttribute('is_checked', 'no');
                    } 
                    if(count($second_permission->children) != 0){
                    
                        foreach($second_permission->children as $third_permission){
                            // $exists = $role->permissions->contains($third_permission->id);
                            $exists = \DB::table($role_has_permissions)->where('role_id' , $role->id)->where('permission_id' , $third_permission->id)->first();
                            if($exists){
                                $third_permission->setAttribute('is_checked', 'yes');
                            } else {
                                $third_permission->setAttribute('is_checked', 'no');
                            } 
                        }
                    }
                }

            }

            // $exists = $role->permissions->contains($permission->id);
            $exists = \DB::table($role_has_permissions)->where('role_id' , $role->id)->where('permission_id' , $permission->id)->first();
            if($exists ){
                $permission->setAttribute('is_checked', 'yes');
            } else {
                $permission->setAttribute('is_checked', 'no');
            }
           
        }

        $role_has_permissions = "company_".$request->company_id."_role_has_permissions";
        $selected_permissions = \DB::table($role_has_permissions)->where('role_id', $request->role)->pluck('permission_id');

        $selected_permission_ids = [];
        foreach ($selected_permissions as $permission_id) {
            $selected_permission_ids[] =  "$permission_id";
        }
 
        return response()->json([
            "status" => true,
            "role" => $role,
            "permissions" => $permissions,
            "selected_permissions" => $selected_permission_ids,
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

        $roles_table = 'company_'.$request->company_id.'_roles';
        Role::setGlobalTable($roles_table);
        $role = Role::where('id', $request->role)->first();

        $permissions_table = 'company_'.$request->company_id.'_permissions';
        Permission::setGlobalTable($permissions_table);
        
        $role->update($request->except('company_id', '_method', 'permissions'));
        if(isset($request->permissions)){
            /*foreach(Permission::get() as $permission) {
                if(in_array($permission->id, $request->permissions)){
                    if($role->hasPermissionTo($permission->name) != 1){
                        // echo 'notexist----';
                        $role_has_permissions = "company_".$request->company_id."_role_has_permissions";
                        \DB::table($role_has_permissions)->insert([
                            'permission_id' => $permission->id,
                            'role_id' => $request->role,
                        ]);
                    }
                } else {
                    if($role->hasPermissionTo($permission->name) == 1){
                        // echo 'exist---';
                        $role->revokePermissionTo($permission);
                        $role_has_permissions = "company_".$request->company_id."_role_has_permissions";
                        \DB::table($role_has_permissions)->where('permission_id', $permission->id)->where('role_id', $request->role)->delete();
                    }
                }
            }*/

            $role_has_permissions = "company_".$request->company_id."_role_has_permissions";
            \DB::table($role_has_permissions)->where('role_id', $request->role)->delete();

            foreach($request->permissions as $permission_new) {
                \DB::table($role_has_permissions)->insert([
                    'permission_id' => $permission_new,
                    'role_id' => $request->role,
                ]);
            }
        }

        $selected_permissions = \DB::table($role_has_permissions)->where('role_id', $request->role)->pluck('permission_id');

        return response()->json([
            "status" => true,
            "message" => "Role updated successfully!",
            "role" => $role,
            "selected_permissions" => $selected_permissions,
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

    public function getRolePermissions(Request $request)
    {
        $permission_arr = get_roles_permissions($request->company_id);
        return response()->json([
            'status' => true,
            'permissions' => $permission_arr ? $permission_arr->original : []
        ]);
    }

    public function duplicateRole(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'role_id' => 'required'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $role_table = 'company_'.$request->company_id.'_roles';
        Role::setGlobalTable($role_table);

        $permission_table = 'company_'.$request->company_id.'_permissions';
        Permission::setGlobalTable($permission_table);

        $role = Role::where('id', $request->role_id)->first();
        if ($role == NULL){
            return response()->json([
                'status' => false,
                'message' => 'Role does not exist'
            ]);
        }

        $new_role = Role::create([
            'name' => $role->name.'-copy',
            'guard_name' => 'api',
        ]);

        $role_has_permissions = "company_".$request->company_id."_role_has_permissions";
        $permissions = \DB::table($role_has_permissions)->where('role_id', $request->role_id)->get();
        foreach ($permissions as $permission) {
            \DB::table($role_has_permissions)->insert([
                'permission_id' => $permission->permission_id,
                'role_id' => $new_role->id,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Role and permissions copied successfully'
        ]);
    }
    public function getMasterRolePermissions(Request $request){
        $validator = Validator::make($request->all(),[
            'names' => 'required'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        $names = explode(',',$request->names);
        $roleIds = \DB::table('roles')->whereIn('name', $names)->pluck('id')->toArray();
        if(empty($roleIds) ){
            return response()->json([
                "status" => false,
                'message' => 'Role Not Found!'
            ]);
        }
        $all_permissions = [];
        $role_has_permissions = "role_has_permissions";
        $permissions = Permission::where('parent_id', 0)->with('children')->get();

        foreach($permissions as $permission){
            if(count($permission->children) != 0){

                foreach($permission->children as $second_permission){
                    $exists = \DB::table($role_has_permissions)->whereIn('role_id' , $roleIds)->where('permission_id' , $second_permission->id)->count();
                    if( $exists > 0  ){
                        $second_permission->setAttribute('is_checked', 'yes');
                    } else {
                        $second_permission->setAttribute('is_checked', 'no');
                    } 
                    if(count($second_permission->children) != 0){
                    
                        foreach($second_permission->children as $third_permission){
                            // $exists = $role->permissions->contains($third_permission->id);
                            $exists = \DB::table($role_has_permissions)->whereIn('role_id' , $roleIds)->where('permission_id' , $third_permission->id)->count();
                            if($exists > 0){
                                $third_permission->setAttribute('is_checked', 'yes');
                            } else {
                                $third_permission->setAttribute('is_checked', 'no');
                            } 
                        }
                    }
                }

            }

            // $exists = $role->permissions->contains($permission->id);
            $exists = \DB::table($role_has_permissions)->whereIn('role_id' , $roleIds)->where('permission_id' , $permission->id)->count();
            if($exists > 0 ){
                $permission->setAttribute('is_checked', 'yes');
            } else {
                $permission->setAttribute('is_checked', 'no');
            }
           
        }

        $role_has_permissions = "role_has_permissions";
        $selected_permissions = \DB::table($role_has_permissions)->whereIn('role_id', $roleIds)->pluck('permission_id');

        $selected_permission_ids = [];
        foreach ($selected_permissions as $permission_id) {
            $selected_permission_ids[] =  "$permission_id";
        }
 
        return response()->json([
            "status" => true,
            "permissions" => $permissions,
            "selected_permissions" => $selected_permission_ids,
        ]);
    }
}