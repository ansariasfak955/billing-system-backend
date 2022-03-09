<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpenseAndInvestment;
use Validator;


class ExpenseAndInvestmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        ExpenseAndInvestment::setGlobalTable('company_'.$request->company_id.'_expense_and_investments');
        if(ExpenseAndInvestment::count() == 0){
            return response()->json([
                "status" => false,
                "message" =>  "No data found"
            ]);
        }
        return response()->json([
            "status" => true,
            "products" =>  ExpenseAndInvestment::get()
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
        ExpenseAndInvestment::setGlobalTable('company_'.$request->company_id.'_expense_and_investments');
        $validator = Validator::make($request->all(),[
            'name' => 'required',          
            'price' => 'required|numeric'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

       
        $expense_and_investment = ExpenseAndInvestment::create($request->except('image', 'company_id', 'images'));

        if($request->image != NULL){
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(storage_path('app/public/expense_n_investments/images'), $imageName);
            $expense_and_investment->image = $imageName;
            $expense_and_investment->save();
        }


        $expense_and_investment->type = $request->type??'expense';
        $expense_and_investment->category_id = $request->category_id??'0';
        $expense_and_investment->is_active = $request->is_active??'1';
        $expense_and_investment->active_margin = $request->active_margin??'0';
        $expense_and_investment->is_promotional = $request->is_promotional??'0';
        $expense_and_investment->manage_stock = $request->manage_stock??'0';
        $expense_and_investment->save();

        return response()->json([
            "status" => true,
            "expense_and_investment" => $expense_and_investment,
            "message" => "Item created successfully"
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
        ExpenseAndInvestment::setGlobalTable('company_'.$request->company_id.'_expense_and_investments');
        $expense_and_investment = ExpenseAndInvestment::where('id', $request->expense_investment)->first();

        if($expense_and_investment ==  NULL){
            return response()->json([
                "status" => true,
                "message" => "This entry does not exists"
            ]);
        }
 
        return response()->json([
            "status" => true,
            "expense_and_investment" => $expense_and_investment
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
        ExpenseAndInvestment::setGlobalTable('company_'.$request->company_id.'_expense_and_investments');
        $validator = Validator::make($request->all(),[
            'name' => 'required',          
            'price' => 'required|numeric'          
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        
        $expense_and_investment = ExpenseAndInvestment::where('id', $request->expense_investment)->first();
        $expense_and_investment->update($request->except('image', 'company_id', 'images', '_method'));

        if($request->image != NULL){
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(storage_path('app/public/expense_n_investments/images'), $imageName);
            $expense_and_investment->image = $imageName;
            $expense_and_investment->save();
        }

        $expense_and_investment->type = $request->type??$expense_and_investment->type;
        $expense_and_investment->category_id = $request->category_id??$expense_and_investment->category_id;
        $expense_and_investment->is_active = $request->is_active??$expense_and_investment->is_active;
        $expense_and_investment->active_margin = $request->active_margin??$expense_and_investment->active_margin;
        $expense_and_investment->is_promotional = $request->is_promotional??$expense_and_investment->is_promotional;
        $expense_and_investment->manage_stock = $request->manage_stock??$expense_and_investment->manage_stock;
        $expense_and_investment->save();

        return response()->json([
            "status" => true,
            "expense_and_investment" => $expense_and_investment,
            "message" => "Item updated successfully"
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
        ExpenseAndInvestment::setGlobalTable('company_'.$request->company_id.'_expense_and_investments');
        $expense_and_investment = ExpenseAndInvestment::where('id', $request->expense_investment)->first();
        if($expense_and_investment->delete()){
            return response()->json([
                    'status' => true,
                    'message' => "Item deleted successfully!"
            ]);
        } else {
            return response()->json([
                    'status' => false,
                    'message' => "Retry deleting again! "
            ]);
        }
    }
}
