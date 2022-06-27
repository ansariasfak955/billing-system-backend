<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\Role;
use App\DataTables\CompanyDataTable;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Hash;
use App\Helpers\TableHelper;
use DB;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CompanyDataTable $dataTable, Request $request)
    {
        $page_title = "Companies";
        return $dataTable->render('backend.pages.companies.index', compact('page_title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = "Create Company";
        return view('backend.pages.companies.create', compact('page_title'));
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
            'email' => 'email|unique:companies',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withError($validator->errors()->first())->withInput();
        }

        $company = Company::create($request->all());
        TableHelper::createTables($company->id);

        // Create Super Admin
        $user_table = 'company_'.$company->id.'_users';
        User::setGlobalTable($user_table);
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Get and Assign Role
        $role_table = 'company_'.$company->id.'_roles';
        $role_id = DB::table($role_table)
            ->where('name', 'Admin')
            ->pluck('id')
            ->first();

        if (isset($role_id)) {
            $role_model_table = 'company_'.$company->id.'_model_has_roles';
            DB::table($role_model_table)->insert([
                'role_id'    => $role_id,
                'model_type' => 'App\Models\User',
                'model_id'   => $user->id,
            ]);
        }

        $company->update([
            'user_id' => $user->id
        ]);

        return redirect()->route('companies.index')->withSuccess('New company has been created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        $page_title = "Edit Company";
        $users_table = "company_".$company->id."_users";

        User::setGlobalTable($users_table);
        $user = User::where('id', $company->user_id)->first();
        $users = User::get();
        return view('backend.pages.companies.edit', compact('company', 'page_title', 'user', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withError($validator->errors()->first())->withInput();
        }

        $users_table = "company_".$company->id."_users";

        User::setGlobalTable($users_table);
        $user = User::where('id', $company->user_id)->first();
        
        if ($request->password != '') {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }
        
        $company->update($request->except(['_method', '_token', 'password']));  
        
        return redirect()->route('companies.index')->withSuccess('Company Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        // Delete all tables of company
        foreach(\DB::select('SHOW TABLES') as $table) {
            $all_table_names = get_object_vars($table);
            foreach ($all_table_names as $key => $table_name) {
                if (strpos($table_name, "company_".$company->id."") !== false) {
                    \Schema::drop($table_name);
                }
            }
        }
        if($company->delete()){
            return response()->json([
                'status' => true,
                'message' => "Company deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again!"
            ]);
        }
    }

    public function editUser(Request $request)
    {
        $company_id = $request->id;
        $user_id = $request->user_id;
        $user_table = 'company_'.$company_id.'_users';
        User::setGlobalTable($user_table);
        $user = User::where('id', $user_id)->first();
        $page_title = '';
        return view('backend.pages.companies.users.edit', compact('page_title', 'user', 'company_id'));
    }

    public function updateUser(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'tin' => 'required|alpha_num'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withError($validator->errors()->first())->withInput();
        }

        $company_id = $request->id;
        $user_id = $request->user_id;
        
        $user_table = 'company_'.$company_id.'_users';
        User::setGlobalTable($user_table);
        $user = User::where('id', $user_id)->first();
        
        if ($request->password) {
            $request['password'] = bcrypt($request->password);
            $user->update($request->all());
        } else {
            $user->update($request->except('password'));
        }
        return redirect()->route('companies.index')->withSuccess('Company User Updated Successfully!');
    }

    public function deleteUser(Request $request)
    {
        $company_id = $request->id;
        $user_id = $request->user_id;
        $user_table = 'company_'.$company_id.'_users';
        User::setGlobalTable($user_table);
        $user = User::where('id', $user_id)->first();
        if($user->delete()){
            return response()->json([
                'status' => true,
                'message' => "Company User deleted successfully!"
            ]);
        }
    }
    public function batchDelete(Request $request){
         // Delete all tables of company
        foreach($request->ids as $company_id){

            foreach(\DB::select('SHOW TABLES') as $table) {
                $all_table_names = get_object_vars($table);
                foreach ($all_table_names as $key => $table_name) {
                    if (strpos($table_name, "company_".$company_id."") !== false) {
                        \Schema::drop($table_name);
                    }
                }
            }

            $company = Company::find($company_id);

            if($company){

                $company->delete();
            }
        }
        return response()->json([
            'status' => true,
            'message' => "Companies deleted successfully!"
        ]);
    }
}