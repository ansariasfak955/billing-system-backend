<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Mail; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use App\Jobs\SendEmailsOnUserGeneration;
use App\Jobs\SendEmailForgetPassword;

class AuthController extends Controller
{
    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function login(Request $request){ 
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|required|email',
            'password' => 'required',
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
            
            return response()->json(['status' => true, 'user' =>  Auth::user()]);
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
            'email' => 'sometimes|required|email',
            'name' => 'required|regex:/^[\pL\s\-]+$/u',
            'country' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

       

        if(User::where('email', $request->email)->orWhere("mobile_number", $request->mobile_number)->first() != NULL){
            return response()->json(['success' => false,
            'message' => "You are already registered"]);
        }
        /* Update user */
        //$request['password'] = "123456";
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile_number' => $request->mobile_number,
            'password' => bcrypt($request->password)
        ]);
        $user->assignRole('user'); 

        /* send emails on registration */
        SendEmailsOnUserGeneration::dispatch($user, $request->password)->delay(now()->addSeconds(1));

        if (Auth::attempt(['email' => $user->email, 'password' => $request['password']])) {
            
            $token = Auth::user()->createToken('api')->accessToken;
            Auth::user()->setAttribute("token", $token);
        }
        
 
        return response()->json([
            'success' => true,
            'user' =>  Auth::user(),
            'message' => "You are registered successfully."
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

        $user = \DB::table('password_resets')->insert(
              ['email' => $request->email, 'token' => $token, 'created_at' => date("Y-m-d H:i:s"), 'type' => "user"]
        );

        /* send emails on forgot password */
        SendEmailForgetPassword::dispatchNow($token, $request->email);
        
        return response()->json([
            'status' => true,
            'message' => 'Reset password link sent on your email address.',
        ]);
    }
}
