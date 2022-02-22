<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    //use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
   // protected $redirectTo = '/home';


    /**
     * Display the password reset view.
     *
     * @return \Illuminate\View\View
     */
    public function getResetPassword(Request $request)
    {
        return view('backend.pages.auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateResetpassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);
       
        $updatePassword = \DB::table('password_resets')->where([
                              'email' => $request->email,
                              'token' => $request->token
                            ])->first();
        if(!$updatePassword){
            return back()->withInput()->with('error', 'Invalid token!');
        }

        
       if($updatePassword->type == "admin"){
           User::where('email', $request->email)->update(['password' => bcrypt($request->password)]);
          \DB::table('password_resets')->where(['email'=> $request->email])->delete();
          PasswordChangeSuccess::dispatchNow(User::where('email', $request->email)->first());
        }

        if($updatePassword->type == "user"){
           User::where('email', $request->email)->update(['password' => bcrypt($request->password)]);
           \DB::table('password_resets')->where(['email'=> $request->email])->delete();
            PasswordChangeSuccess::dispatchNow(User::where('email', $request->email)->first());
          return redirect(env('WEBSITE_APP_URL').'/login')->with('message','Your password has been changed successfully!');
        }
  
        
        return redirect()->to('/login')->with('message','Your password has been changed successfully!');
    }
}
