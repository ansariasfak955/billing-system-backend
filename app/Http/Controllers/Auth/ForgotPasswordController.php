<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\SendEmailForgetPassword;
//use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

   // use SendsPasswordResetEmails;

    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function getForgetPassword()
    {
        return view('backend.pages.auth.forget-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateForgetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        /*if($user->is_ban == 1){
             return back()->withInput()->with('error', 'Your account has been suspended.Please contact admin');
        }*/
        
        $token = \Str::random(64);

        $user = \DB::table('password_resets')->insert(
              ['email' => $request->email, 'token' => $token, 'created_at' => date("Y-m-d H:i:s"), 'type' => "admin"]
        );

        /* send emails on forgot password */
        SendEmailForgetPassword::dispatchNow($token, $request->email);

        return redirect()->to('/login')->withInput()->with('success', 'Reset password link sent on your email address.');

       
    }
}
