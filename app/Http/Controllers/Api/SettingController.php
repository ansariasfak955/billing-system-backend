<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    /* Get Email settings*/
    public function getSettings(Request $request)
    {
        Setting::setGlobalTable('company_'.$request->company_id.'_settings');

        return response()->json([
            "status" => true,
            "settings" =>  Setting::pluck('option_value', 'option_name')
        ]);
    }

    /* Get Email settings*/
    public function updateSettings(Request $request)
    {
        Setting::setGlobalTable('company_'.$request->company_id.'_settings');
        foreach ($request->all() as $option_name => $option_value) {
            Setting::where('option_name', $option_name)->update([
                "option_value" => $option_value
            ]);
        }
       
        // foreach($request->update_data as $item){
        //     Setting::where('option_name', $item['option_name'])->update([
        //         "option_value" => $item['option_value']
        //     ]);
        // }

        return response()->json([
            "status" => true,
            "settings" =>  Setting::pluck('option_value', 'option_name'),
            "message" => "Settings updated successfully"
        ]);
    }
}
