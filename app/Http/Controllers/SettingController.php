<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function getSmtpDetails(){
        $page_title = "SMTP Details";
        return view('backend.pages.smtp.create', compact('page_title'));
    }

    public function storeSmtpDetails(Request $request){

        Setting::updateOrCreate([
            'option_name' => 'smtp_email_address' 
        ], [
            'option_value' => $request->smtp_email_address
        ]);

        Setting::updateOrCreate([
            'option_name' => 'smtp_server' 
        ], [
            'option_value' => $request->smtp_server
        ]);

        Setting::updateOrCreate([
            'option_name' => 'smtp_security_protocol' 
        ], [
            'option_value' => $request->smtp_security_protocol
        ]);

        Setting::updateOrCreate([
            'option_name' => 'smtp_password' 
        ], [
            'option_value' => $request->smtp_password
        ]);

        Setting::updateOrCreate([
            'option_name' => 'smtp_port' 
        ], [
            'option_value' => $request->smtp_port
        ]);
        return back()->withSuccess('Settings updated successfully!');
    }
}
