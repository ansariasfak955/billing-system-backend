<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Validator;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        ExpenseCategory::setGlobalTable('company_'.$request->company_id.'_expense_categories');
        if(ExpenseCategory::count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "expense_categories" =>  ExpenseCategory::get()
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
        ExpenseCategory::setGlobalTable('company_'.$request->company_id.'_expense_categories');
        $validator = Validator::make($request->all(),[
            'name' => 'required'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        
        $expense_category = ExpenseCategory::create($request->except('company_id'));
        $expense_category->type = $request->type??'expense';
        $expense_category->save();

        return response()->json([
            "status" => true,
            "expense_category" => $expense_category,
            "message" => "Expense category created successfully"
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
        ExpenseCategory::setGlobalTable('company_'.$request->company_id.'_expense_categories');
        $expense_category = ExpenseCategory::where('id', $request->expense_category)->first();

        if($expense_category ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "expense_category" => $expense_category
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }
        ExpenseCategory::setGlobalTable('company_'.$request->company_id.'_expense_categories');
        $expense_category =ExpenseCategory::where('id', $request->expense_category)->first();
        
        $expense_category->update($request->except('company_id', '_method'));
        $expense_category->type = $request->type??$expense_category->type;
        $expense_category->save();

        return response()->json([
            "status" => true,
            "expense_category" => $expense_category
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
        ExpenseCategory::setGlobalTable('company_'.$request->company_id.'_expense_categories');
        $expense_category = ExpenseCategory::where('id', $request->expense_category)->first();
        if($expense_category->delete()){
            return response()->json([
                    'status' => true,
                    'message' => "Expense category deleted successfully!"
            ]);
        } else {
            return response()->json([
                    'status' => false,
                    'message' => "Retry deleting again! "
            ]);
        }
    }
}
