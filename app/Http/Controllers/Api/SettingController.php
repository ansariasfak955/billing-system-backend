<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Company;
use App\Models\User;
use App\Jobs\SendTestMailJob;
use App\Mail\SendTestMail;
use Mail;
use Auth;

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

        $users_table = 'company_'.$request->company_id.'_users';
        User::setGlobalTable($users_table);

        $user = User::where('id', Auth::id())->first();
        if ($user != NULL) {
            $user->smtp_email_address = $request->smtp_email_address;
            $user->smtp_server = $request->smtp_server;
            $user->smtp_security_protocol = $request->smtp_security_protocol;
            $user->smtp_password = $request->smtp_password;
            $user->smtp_port = $request->smtp_port;
            $user->smtp_sender_name = $request->email_configuration_sender_name;
            $user->save();
        }

        $token = $request->bearerToken();
        Auth::user()->setAttribute("token", $token);

        $user_data = User::where('id', Auth::id())->first();
       
        // foreach($request->update_data as $item){
        //     Setting::where('option_name', $item['option_name'])->update([
        //         "option_value" => $item['option_value']
        //     ]);
        // }

        return response()->json([
            "status" => true,
            "settings" =>  Setting::pluck('option_value', 'option_name'),
            "user" =>  Auth::user(),
            "message" => "Settings updated successfully"
        ]);
    }

    public function sendTestEmail(Request $request)
    {
        $configuration = [
            'smtp_host'       => getSettingValue('smtp_server'),
            'smtp_port'       => getSettingValue('smtp_port'),
            'smtp_username'   => getSettingValue('smtp_email_address'),
            'smtp_password'   => getSettingValue('smtp_password'),
            'smtp_encryption' => getSettingValue('smtp_security_protocol')
        ];
        $company = Company::where('id',$request->company_id)->first();
        $settings_table = 'company_'.$request->company_id.'_settings';
        Setting::setGlobalTable($settings_table);

        $settings = Setting::where('option_name', 'email_configuration_signature')->first();

        $users_table = 'company_'.$request->company_id.'_users';
        User::setGlobalTable($users_table);
        $user = User::where('id', Auth::id())->first();

        if($user == NULL){
            return response()->json([
                "status" => false,
                "message" => "User not exist!"
            ]);
        }

        // if($user->smtp_server == NULL || $user->smtp_port == NULL || $user->smtp_email_address == NULL || $user->smtp_password == NULL || $user->smtp_security_protocol == NULL || $user->smtp_sender_name == NULL) {
        //     return response()->json([
        //         "status" => false,
        //         "user" => $user,
        //         "message" => "Please enter SMTP details first!"
        //     ]);
        // }

        $configuration['from_email']      = getCompanySetting($request->company_id , 'email_configuration_send_as') ? getCompanySetting($request->company_id , 'email_configuration_send_as') : 'admin@billing.site';

        $configuration[ 'from_name']       = getCompanySetting($request->company_id,'email_configuration_sender_name') ? getCompanySetting($request->company_id,'email_configuration_sender_name') : 'Billing';

        $configuration['reply_to']       =  getCompanySetting($request->company_id, 'email_configuration_reply_to') ? getCompanySetting($request->company_id, 'email_configuration_reply_to') : '';
        $configuration['send_copy_to']       = getCompanySetting($request->company_id, 'email_configuration_send_copy_to') ?getCompanySetting($request->company_id, 'email_configuration_send_copy_to') : '';

        // $configuration = [
        //     'smtp_host'       => $user->smtp_server,
        //     'smtp_port'       => $user->smtp_port,
        //     'smtp_username'   => $user->smtp_email_address,
        //     'smtp_password'   => $user->smtp_password,
        //     'smtp_encryption' => $user->smtp_security_protocol,
        //     'from_email'      => $user->smtp_email_address,
        //     'from_name'       => $user->smtp_sender_name ? $user->smtp_sender_name : 'test',
        // ];

        SendTestMailJob::dispatch($configuration, $company->email, new SendTestMail($configuration, $user->email, $company, $settings));

        return response()->json([
            "status" => true,
            "message" => "Test mail sent successfully!"
        ]);
    }
}