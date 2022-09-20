<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Config;
use Validator;
use Auth;
use DB;

class UserController extends Controller
{

    /*
    * Set config files according to company 
    *
    */
    public function setConfig($company_id)
    {
        
        config([
            //'permission.table_names.roles' => 'company_'.$company_id.'_roles',
            'permission.table_names.permissions' => 'company_'.$company_id.'_permissions',
            'permission.table_names.model_has_permissions' => 'company_'.$company_id.'_model_has_permissions',
            'permission.table_names.model_has_roles' => 'company_'.$company_id.'_model_has_roles',
            //'permission.table_names.role_has_permissions' => 'company_'.$company_id.'_role_has_permissions',
        ]);
    }


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
        User::setGlobalTable('company_'.$request->company_id.'_users');
        if(User::count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "users" => User::get()
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
        $tableName = 'company_'.$request->company_id.'_users';
        $validator = Validator::make($request->all(),[
            'name' => 'required',          
            'email' => 'required|email|unique:'.$tableName.'',         
            'username' => 'required|alpha_dash|unique:'.$tableName.'',         
            'role' => 'required',
            'password' => 'required|min:8|alpha_dash',
            'tin' => 'required|alpha_num',   
        ],[
            'tin.required' => 'Ced/Ruc number is required',
            'tin.alpha_num' => 'Ced/Ruc special characters are not allowed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        User::setGlobalTable('company_'.$request->company_id.'_users');
        $request['password'] = bcrypt($request->password);
        $user = User::create($request->except('image', 'company_id'));

        if($request->image != NULL){
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(storage_path('app/public/users'), $imageName);
            $user->image = $imageName;
            $user->save();
        }

        $user->has_access = $request->is_default??'1';
        $user->use_email_configuartion = $request->use_email_configuartion??"gmail";
        $model_has_roles_table = 'company_'.$request->company_id.'_model_has_roles';
        // Delete old role
        DB::table($model_has_roles_table)->where('model_id', $user->id)->delete();

        // Assign new role
        DB::table($model_has_roles_table)->insert([
            'role_id'    => $request->role,
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
        ]);

        $user->save();

        return response()->json([
            "status" => true,
            "user" => $user,
            "message" => "User created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        User::setGlobalTable('company_'.$request->company_id.'_users');
        $user = User::where('id', $request->user)->first();
        return response()->json([
            "status" => true,
            "user" => $user
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
            'email' => 'required|email',         
            'username' => 'required|alpha_dash',         
            'role' => 'required',
            'tin' => 'required|alpha_num',
        ],[
            'tin.required' => 'Ced/Ruc number is required',
            'tin.alpha_num' => 'Ced/Ruc special characters are not allowed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        User::setGlobalTable('company_'.$request->company_id.'_users');
        $user = User::where('id', $request->user)->first();
        $user->update($request->except('image', 'company_id', '_method', 'password'));

        if($request->image != NULL){
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(storage_path('app/public/users'), $imageName);
            $user->image = $imageName;
            $user->save();
        }

        if($request->password != NULL){
            $user->password = bcrypt($request->password);
        }
        $user->has_access = $request->has_access??$user->has_access;
        $user->use_email_configuartion = $request->use_email_configuartion??$user->use_email_configuartion;
        $roles_table = 'company_'.$request->company_id.'_roles';
        Role::setGlobalTable($roles_table);
        
        $model_has_roles_table = 'company_'.$request->company_id.'_model_has_roles';
        // Delete old role
        DB::table($model_has_roles_table)->where('model_id', $user->id)->delete();

        // Assign new role
        DB::table($model_has_roles_table)->insert([
            'role_id'    => $request->role,
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
        ]);
        $user->save();

        return response()->json([
            "status" => true,
            "user" => $user,
            "message" => "User updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        User::setGlobalTable('company_'.$request->company_id.'_users');
        $user = User::where('id', $request->user)->first();
        if($user->delete()){
            return response()->json([
                'status' => true,
                'message' => "User deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => true,
                'message' => "User deleted successfully!"
            ]);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = User::where('id', Auth::id())->first();

        if($request->password != NULL) {
            $user->password = bcrypt($request->password);  
        }

        $user->update($request->except('password','image'));

        if($request->image != NULL){
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(storage_path('app/public/users'), $imageName);
            $user->image = $imageName;
            $user->save();
        }

        $user = User::where('id', Auth::id())->first();

        $token = Auth::user()->createToken('api')->accessToken;
        Auth::user()->setAttribute("token", $token);
        return response()->json([
            'status'  => true,
            'message' => "Profile updated successfully!",
            'user'    => Auth::user(),
        ]);
    }
}
