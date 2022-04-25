<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityType;
use Illuminate\Http\Request;
use Validator;
use Auth;

class ActivityTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $activity_types = ActivityType::get();

        if($activity_types->count() == 0) {
            return response()->json([
                "status" => false,
                "message" => "No data found!"
            ]);
        } else {
            return response()->json([
                "status" => true,
                "activity_types" =>  $activity_types
            ]);  
        }
    }
}