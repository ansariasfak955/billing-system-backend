<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
//use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

   // use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if(User::whereHas("roles", function($q){ $q->where("name", "super admin"); })->where('email', $request->email)->first() == NULL){
            return redirect()->back()->withError('Email address is incorrect');
        }
        $credentials = $request->only('email', 'password');
        if (\Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->intended('/')->withSuccess('Signed in');
        } else {
            if(User::where('email', $request->email)->where('password', $request->password)->first() == NULL){
                return redirect()->back()->withInput($request->input())->withError('Password Incorrect');
            }
        }
        return redirect()->back()->withError('Login details are not valid');
    }
}
