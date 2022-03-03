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
            "settings" =>  Setting::get()
        ]);
    }

    /* Get Email settings*/
    public function updateSettings(Request $request)
    {
        Setting::setGlobalTable('company_'.$request->company_id.'_settings');
       
        foreach($request->update_data as $item){
            Setting::where('option_name', $item['option_name'])->update([
                "option_value" => $item['option_value']
            ]);
        }

        return response()->json([
            "status" => true,
            "settings" =>  Setting::get(),
            "message" => "Settings updated successfully"
        ]);
    }
}
