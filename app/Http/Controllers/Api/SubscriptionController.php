<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Validator;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $subscription = Subscription::get();

        if ($subscription->count() == 0) {
            return response()->json([
                "status" => false,
                "message" => "No subscription found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "subscription" => $subscription
            ]);  
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {	
    	// $table = 'company_'.$request->company_id.'_subscriptions';
     //    Subscription::setGlobalTable($table);
     //    $validator = Validator::make($request->all(),[
     //        'name' => "required"
     //    ], [
     //        'name.required' => 'Please enter name.'
     //    ]);

     //    if ($validator->fails()) {
     //        return response()->json([
     //            "status" => false,
     //            "message" => $validator->errors()->first()
     //        ]);
     //    }
        
     //    $subscription = Subscription::create($request->except('company_id'));
     //    $subscription->save();

     //    return response()->json([
     //        "status"       => true,
     //        "subscription" => $subscription,
     //        "message"      => "Subscription created successfully"
     //    ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
		// $table = 'company_'.$request->company_id.'_subscriptions';
  //       Subscription::setGlobalTable($table);
  //       $subscription = Subscription::where('id', $request->subscription)->first();

  //       if($subscription ==  NULL){
  //           return response()->json([
  //               "status" => false,
  //               "message" => "This entry does not exists"
  //           ]);
  //       }
 
  //       return response()->json([
  //           "status"       => true,
  //           "subscription" => $subscription
  //       ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // $table = 'company_'.$request->company_id.'_subscriptions';
        // Subscription::setGlobalTable($table);
        // $subscription = Subscription::where('id', $request->subscription)->first();
        
        // $subscription->update($request->except('company_id', 'subscription'));
        // $subscription->save();

        // return response()->json([
        //     "status" => true,
        //     "subscription" => $subscription,
        //     "message" => "Subscription updated successfully"
        // ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // $table = 'company_'.$request->company_id.'_subscriptions';
        // Subscription::setGlobalTable($table);
        // $subscription = Subscription::where('id', $request->subscription)->first();

        // if($subscription ==  NULL){
        //     return response()->json([
        //         "status"  => false,
        //         "message" => "This entry does not exists"
        //     ]);
        // }

        // if($subscription->delete()){
        //     return response()->json([
        //         'status'  => true,
        //         'message' => "Subscription deleted successfully!"
        //     ]);
        // } else {
        //     return response()->json([
        //         'status'  => false,
        //         'message' => "There is an error!"
        //     ]);
        // }
    }
}