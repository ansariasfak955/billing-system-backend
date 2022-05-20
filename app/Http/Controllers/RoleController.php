<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\DataTables\RoleDataTable;
use Illuminate\Http\Request;
use Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RoleDataTable $dataTable, Request $request)
    {
        $page_title = "Roles";
        $roles = Role::get();
        return $dataTable->render('backend.pages.roles.index', compact('page_title', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = "Create Role";
        $permissions = Permission::where('parent_id', 0)->with('children')->get();
        return view('backend.pages.roles.create', compact('page_title', 'permissions'));
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
            'name' => "required"
        ], [
            'name.required' => 'Please enter role.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        if(Role::where('name' , $request->name )->first()){
            return redirect()->route('roles.index')->withError('Role already exists!');
        }
        
        $role = Role::create($request->except('permissions'));
        $role->guard_name = 'api';
        $role->save();

        foreach($request->permissions as $permission_index => $permission_id){
            $permission = Permission::where('id', $permission_id)->first();
            $_role = Role::where('id', $role->id)->first();
            $_role->givePermissionTo($permission);
        }

        return redirect()->route('roles.index')->withSuccess('New role is created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
		
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role, Request $request)
    {
        $page_title = "Edit Role";
        $permissions = Permission::where('parent_id', 0)->with('children')->get();

        $selected_permissions = Role::findByName($role->name)->permissions->pluck('id')->toArray();

        return view('backend.pages.roles.edit', compact('role', 'page_title', 'permissions', 'selected_permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $role = Role::where('id', $role->id)->first();
        $selected_permissions = Role::findByName($role->name)->permissions->pluck('id')->toArray();
        $revoke_permissions = array_diff($selected_permissions, $request->permissions);
        $revoke_arr = array_values($revoke_permissions);
        // Revoking permissions
        foreach ($revoke_arr as $key => $rev_permission_id) {
            $rev_permission = Permission::where('id', $rev_permission_id)->first();
            $role->revokePermissionTo($rev_permission);
        }
        
        $role->update($request->all());
        $role->save();

        // Give permissions
        foreach($request->permissions as $permission_index => $permission_id){
            $permission = Permission::where('id', $permission_id)->first();
            $_role = Role::where('id', $role->id)->first();
            $_role->givePermissionTo($permission);
        }

        return redirect()->route('roles.index')->withSuccess('Role Updated Successfully!'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role = Role::where('id', $role->id)->first();

        if($role->delete()){
            return response()->json([
                'status' => true,
                'message' => "Role deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again!"
            ]);
        }
    }
}