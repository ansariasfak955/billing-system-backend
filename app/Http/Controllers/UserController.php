<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\DataTables\UserDataTable;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserDataTable $dataTable, Request $request)
    {
        $page_title = "Users";
        $users_count = User::whereHas("roles", function($q){ $q->whereIn("name", ["user"]); })->count();
        return $dataTable->render('backend.pages.users.index', compact('page_title', 'users_count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = "Create User";
        $roles = Role::all()->pluck('name','id')->toArray();
        $role = '';
        return view('backend.pages.users.create', compact('page_title', 'roles', 'role'));
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
            'name'  => 'required',
            'email' => 'email|unique:users'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withError($validator->errors()->first())->withInput();
        }

        $request['password'] = Hash::make($request->password);
        /*if ($request->is_ban == 'on') {
            $request['is_ban'] = 1;
        } else {
            $request['is_ban'] = 0;
        }*/
        $user = User::create($request->except('role'));
        $user->assignRole($request->role);
        return redirect()->route('users.index')->withSuccess('New user is created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $page_title = "Edit User";
        $roles = Role::all()->pluck('name','id')->toArray();
        $role = $user->roles->pluck('id')->first();
        return view('backend.pages.users.edit', compact('user', 'page_title', 'roles', 'role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required'             
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withError($validator->errors()->first())->withInput();
        }
        
        if ($request->password != '') {
            $request['password'] = Hash::make($request->password);
        }

        /*if ($request->is_ban == 'on') {
            $request['is_ban'] = 1;
        } else {
            $request['is_ban'] = 0;
        }*/
        
        $user->syncRoles($request->role);
        
        if ($request->password == '') {
            $user->update($request->except(['_method', '_token', 'role', 'password']));
        } else {
            $user->update($request->except(['_method', '_token', 'role']));
        }       
        
        return redirect()->route('users.index')->withSuccess('User Updated Successfully!'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if($user->delete()){
            return response()->json([
                'status' => true,
                'message' => "User deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again!"
            ]);
        }
    }

    /* Delete Multiple Entries */

    public function deleteAll(Request $request)
    {
        User::whereIn('id', $request->ids)->delete();
        return response()->json([
            'status' => true, 
            'message' => 'Selected items deleted successfully', 
        ]);
    }

    //  Get profile
    public function getProfile()
    {
        $page_title = 'Profile';
        return view('backend.pages.users.profile', compact('page_title'));
    }


    /* Update profile */
    public function updateProfile(Request $request, $id)
    {
        $user = User::where('id', $id)->first();
        $request->validate([
            'name' => 'required',
            'email' => 'required'
        ]);
    
        $user->update($request->all());
        if($request->image != NULL){
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(storage_path('app/public/users'), $imageName);
            $user->image = $imageName;
            $user->save();
        }

        return redirect()->back()->with('success','Profile updated successfully');
       
    }

    /* get and update password */

    public function changePassword(Request $request)
    {
        if($request->isMethod('post')){
            if(!isset($request->current_password)){
                return redirect()->back()->with('error','Please enter current password!'); 
            }

            if(!isset($request->new_password)){
                return redirect()->back()->with('error','Please enter new password!'); 
            }

            if(isset($request->new_password)){
                if(!\Hash::check($request->current_password, \Auth::user()->password)){
                    return redirect()->back()->with('error','Current password does not matched');
                } 
            }
            
            if(isset($request->new_password)){
                \Auth::user()->password = bcrypt($request->new_password);
                \Auth::user()->save();
            }
            return redirect()->to('/')->with('success','password updated successfully');
        }
        return view('backend.pages.users.change-password');
    }
}
