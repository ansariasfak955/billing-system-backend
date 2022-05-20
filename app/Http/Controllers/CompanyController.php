<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\DataTables\CompanyDataTable;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

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
        // $companys_count = Company::whereHas("roles", function($q){ $q->whereIn("name", ["company"]); })->count();
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
            'email' => 'email|unique:companies'
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
        $company = Company::create($request->all());
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
        return view('backend.pages.companies.edit', compact('company', 'page_title'));
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
        
        if ($request->password != '') {
            $request['password'] = Hash::make($request->password);
        }

        /*if ($request->is_ban == 'on') {
            $request['is_ban'] = 1;
        } else {
            $request['is_ban'] = 0;
        }*/
        
        /*if ($request->password == '') {
            $company->update($request->except(['_method', '_token', 'role', 'password']));
        } else {
            $company->update($request->except(['_method', '_token', 'role']));
        } */

        $company->update($request->except(['_method', '_token']));   
        
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
}