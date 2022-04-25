<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\DataTables\SubscriptionDataTable;
use Illuminate\Http\Request;
use Validator;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SubscriptionDataTable $dataTable, Request $request)
    {
        $page_title = "Subscriptions";
        $subscriptions = Subscription::get();
        return $dataTable->render('backend.pages.subscriptions.index', compact('page_title', 'subscriptions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = "Create Subscription";
        $types = array(
            'monthly' => 'Monthly',
            'annually' => 'Annually'
        );
        return view('backend.pages.subscriptions.create', compact('page_title', 'types'));
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
            'name' => "required"
        ], [
            'name.required' => 'Please enter name.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $subscription = Subscription::create($request->all());
        $subscription->save();

        return redirect()->route('subscriptions.index')->withSuccess('New subscription is created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
		//
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function edit(Subscription $subscription)
    {
        $page_title = "Edit Subscription";
        $types = array(
            'monthly' => 'Monthly',
            'annually' => 'Annually'
        );
        return view('backend.pages.subscriptions.edit', compact('subscription', 'page_title', 'types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subscription $subscription)
    {
        $subscription = Subscription::where('id', $subscription->id)->first();
        
        $subscription->update($request->all());
        $subscription->save();

        return redirect()->route('subscriptions.index')->withSuccess('Subscription Updated Successfully!'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subscription $subscription)
    {
        $subscription = Subscription::where('id', $subscription->id)->first();

        if($subscription->delete()){
            return response()->json([
                'status' => true,
                'message' => "Subscription deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again!"
            ]);
        }
    }
}