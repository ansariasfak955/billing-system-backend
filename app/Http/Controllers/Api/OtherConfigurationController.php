<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OtherConfiguration;
use Validator;

class OtherConfigurationController extends Controller
{
    public function index(Request $request){

        $table = 'company_'.$request->company_id.'_other_configurations';
        OtherConfiguration::setGlobalTable($table);

            return response()->json([
                'status' => true,
                'data' =>  OtherConfiguration::whereNull('parent_id')->with('children')->get()
            ]);
    }
}
