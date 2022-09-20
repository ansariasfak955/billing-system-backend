<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Helpers\TableHelper;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Auth;
use Validator;
use DB;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $table = 'company_'.$request->company_id.'_companies';
        // Company::setGlobalTable($table);

        if(Company::where('id', $request->company_id)->count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "companies" => Company::where('id', $request->company_id)->get()
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
        $request['user_id'] = Auth()->id();

        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:companies',
            'commercial_name' => 'required|unique:companies',
            'phone' => 'required',
            'tin' => 'required|alpha_num',  
        ],[
            'tin.required' => 'Ced/Ruc is must be required',
            'tin.alpha_num' => 'Ced/Ruc special characters are not allowed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }  
        $company = Company::create($request->except('logo'));

        if($request->logo != NULL){
            $imageName = time().'.'.$request->logo->extension();  
            $request->logo->move(storage_path('app/public/company/logo'), $imageName);
            $company->logo = $imageName;
            $company->save();
        }


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
        $role = DB::table($role_table)
            ->where('name', 'Admin')
            ->select('id')
            ->first();

        if (isset($role->id)) {
            $role_model_table = 'company_'.$company->id.'_model_has_roles';
            DB::table($role_model_table)->insert([
                'role_id'    => $role->id,
                'model_type' => 'App\Models\User',
                'model_id'   => $user->id,
            ]);
        }
        
        return response()->json([
            "status" => true,
            "company" => $company,
            "message" => "Company created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        return response()->json([
            "status" => true,
            "company" => $company
        ]);
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
            'name' => 'required|unique:companies,name,'.$company->id,
            'phone' => 'required',
            'tin' => 'required|alpha_num',
        ],[
            'tin.required' => 'Ced/Ruc is must be required',
            'tin.alpha_num' => 'Ced/Ruc special characters are not allowed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        
        $company->update($request->except('logo'));

        if($request->logo != NULL){
            $imageName = time().'.'.$request->logo->extension();  
            $request->logo->move(storage_path('app/public/company/logo'), $imageName);
            $company->logo = $imageName;
            $company->save();
        }

        return response()->json([
            "status" => true,
            "company" => $company,
            "message" => "Company updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        if($company->delete()){
            return response()->json([
                'status' => true,
                'message' => "Company deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again! "
            ]);
        }
    }
}
