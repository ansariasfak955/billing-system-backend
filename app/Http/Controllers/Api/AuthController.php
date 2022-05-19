<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Permission;
use App\Models\Company;
use Illuminate\Http\Request;
use Mail; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use App\Jobs\SendEmailsOnUserGeneration;
use App\Jobs\SendEmailForgetPassword;
use App\Jobs\PasswordChangeSuccess;
use App\Helpers\TableHelper;
use DB;

class AuthController extends Controller
{
    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function login(Request $request){
        if($request->company_name != NULL){
            $company_id = Company::where('name', $request->company_name)->pluck('id')->first();
            if ($company_id == '') {
                return response()->json([
                    'status' => false,
                    'message' => 'Company not exist',
                ]);
            }
            (new UserController())->setConfig($company_id);
            User::setGlobalTable('company_'.$company_id.'_users');
        }
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|required|email',
            'password' => 'required',
            'company_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
        
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            if(Auth::user()->is_ban == 1){
                 return response()->json([
                    'status' => false,
                    'message' => "Your account has been suspended. Please contact admin",
                ]);
            }

            $token = Auth::user()->createToken('api')->accessToken;
            Auth::user()->setAttribute("token", $token);

            // $table = 'company_'.$company_id.'_permissions';
            // Permission::setGlobalTable($table);
            // $permissions = Permission::where('parent_id', 0)->with('children')->get();

            // Permissions
            $permission_arr = get_roles_permissions($company_id);
            
            return response()->json([
                'status'      => true,
                'user'        => Auth::user(),
                'permissions' => $permission_arr ? $permission_arr->original : []
            ]);
        } else {
            return response()->json(['status' => false, 'message' => 'Please check your login credentials!']);
        }
    }
    
    /** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'email'   => 'sometimes|required|email',
            'name'    => 'required|regex:/^[\pL\s\-]+$/u',
            'country' => 'required',
            'company_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        if ($request->company_name == '') {
            return response()->json([
                'status' => false,
                'message' => 'Company name is required',
            ]);
        }

        $companyExist = Company::where('name', $request->company_name)->first();
        if ($companyExist != NULL) {
            return response()->json([
                'success' => false,
                'message' => "Company already exist with this name!",
            ]);
        }

        /*if(User::where('email', $request->email)->first() != NULL){
            return response()->json([
                'success' => false,
                'message' => "You are already registered"
            ]);
        }*/

        /*if($request->company_id == NULL){
            $user->assignRole('user');
        }*/
        
        $company = Company::create([
            'fiscal_start_date' => 1,
            'fiscal_start_month' => 1,
            'number_of_decimal' => 2,
            'decimal_separator' => '.',
            'pdf_file_download_date_format' => 'yyyy-mm-dd',
            'name' => $request->company_name,
        ]);

        TableHelper::createTables($company->id);

        // Create user in company table
        $table = 'company_'.$company->id.'_users';
        User::setGlobalTable($table);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile_number' => $request->mobile_number,
            'password' => bcrypt($request->password),
            'country' => $request->country
        ]);
        $company->where('id', $company->id)->update([
            'user_id' => $user->id,
            'email'   => $user->email,
        ]);

        /* send emails on registration */
        SendEmailsOnUserGeneration::dispatch($user, $request->password)->delay(now()->addSeconds(1));

        if (Auth::attempt(['email' => $user->email, 'password' => $request['password']])) {
            $token = Auth::user()->createToken('api')->accessToken;
            Auth::user()->setAttribute("token", $token);
        }

        // Permissions
        $permission_arr = get_roles_permissions($company->id);
        
        return response()->json([
            'success' => true,
            'message' => "You are registered successfully.",
            'user'    =>  Auth::user(),
            'permissions' => $permission_arr ? $permission_arr->original : []
        ]);
    }

    // -------------- forget password -------------------//
    public function forgetPassword(Request $request) {
        $user = User::where('email', $request->email)->first();
        if ($user == NULL) {
            return response()->json([
                'status' => false,
                'message' => "This email doesn't exist in our system"
            ]);
        }

        if($user->is_ban == 1){
            return response()->json([
                'status' => false,
                'message' => "Your account has been suspended.Please contact admin",
            ]);
        }
        
        $token = \Str::random(64);

        $user = \DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => date("Y-m-d H:i:s"),
            'type' => "user"
        ]);

        /* send emails on forgot password */
        SendEmailForgetPassword::dispatchNow($token, $request->email);
        
        return response()->json([
            'status' => true,
            'message' => 'Reset password link sent on your email address.',
        ]);
    }

    /* Reset password */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
       
        $updatePassword = \DB::table('password_resets')->where([
                              'email' => $request->email,
                              'token' => $request->token
                            ])->first();
        if(!$updatePassword){
            return response()->json([
                'status' => false,
                'message' => 'Invalid token!',
            ]);
        }

        if($updatePassword->type == "user"){
           User::where('email', $request->email)->update(['password' => bcrypt($request->password)]);
           \DB::table('password_resets')->where(['email'=> $request->email])->delete();
            PasswordChangeSuccess::dispatchNow(User::where('email', $request->email)->first());
          return response()->json([
                'status' => true,
                'message' => 'Your password has been changed successfully!',
            ]);
        }
    }
}