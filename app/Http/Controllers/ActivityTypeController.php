<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityType;
use App\DataTables\ActivityTypeDataTable;
use Illuminate\Http\Request;
use Validator;

class ActivityTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ActivityTypeDataTable $dataTable, Request $request)
    {
        $page_title = "Activity Types";
        $activity_types = ActivityType::get();
        return $dataTable->render('backend.pages.activity-type.index', compact('page_title', 'activity_types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = "Create ActivityType";
        return view('backend.pages.activity-type.create', compact('page_title'));
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
            'activity_type' => "required"
        ], [
            'activity_type.required' => 'Please enter activity type.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first()
            ]);
        }

        $activity_types = ActivityType::create($request->all());
        $activity_types->save();

        return redirect()->route('activity-type.index')->withSuccess('New activity type is created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ActivityType  $activity_type
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
		//
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ActivityType  $activity_type
     * @return \Illuminate\Http\Response
     */
    public function edit(ActivityType $activity_type)
    {
        $page_title = "Edit ActivityType";
        return view('backend.pages.activity-type.edit', compact('activity_type', 'page_title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ActivityType  $activity_type
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ActivityType $activity_type)
    {
        $activity_type = ActivityType::where('id', $activity_type->id)->first();
        
        $activity_type->update($request->all());
        $activity_type->save();

        return redirect()->route('activity-type.index')->withSuccess('Activity Type Updated Successfully!'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ActivityType  $activity_type
     * @return \Illuminate\Http\Response
     */
    public function destroy(ActivityType $activity_type)
    {
        $activity_type = ActivityType::where('id', $activity_type->id)->first();

        if($activity_type->delete()){
            return response()->json([
                'status' => true,
                'message' => "ActivityType deleted successfully!"
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Retry deleting again!"
            ]);
        }
    }
}