<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Helpers\TableHelper;
use Illuminate\Http\Request;
use Auth;
use Validator;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            "status" => true,
            "companies" => Auth::user()->companies
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
            'phone' => 'digits:10'            
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
            'phone' => 'digits:10'            
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        
        $company->update($request->all());

        if($request->logo != NULL){
            $imageName = time().'.'.$request->logo->extension();  
            $request->logo->move(storage_path('app/public/company/logo'), $imageName);
            $company->logo = $imageName;
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
