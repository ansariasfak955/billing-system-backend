<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        User::setGlobalTable('company_'.$request->company_id.'_users');
        return response()->json([
            "status" => true,
            "custom_states" => User::get()
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
            'email' => 'required|email',         
            'username' => 'required|alpha_dash',         
            'role' => 'required',
            'password' => 'required|min:8|alpha_dash',       
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
            'role' => 'required'     
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
}
