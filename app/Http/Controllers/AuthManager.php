<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthManager extends Controller
{
    function login()
    {
        return view('auth.login');
    }

    public function logout()
    {
        Auth::logout(); 
        return redirect()->route('login');
    }

    function LoginPost(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email','password');
        if(Auth::attempt($credentials)){
            return redirect()->intended(route("home"));
            //return redirect()->intended('/home'); if i dont have a name
        }
        return redirect(route("login"))->with("error","Invalid Email or Password");
    }

    function register()
    {
        return view('auth.register');
    }

    function registerPost(Request $request){
        $request->validate([
            'fullname'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6'
        ]);
        $user = new User();
        $user->name=$request->fullname;
        $user->email=$request->email;
        $user->password = Hash::make($request->password); 
        if($user->save()){
            return redirect(route("login"))->with("success","Registration Successful");
        }
        return redirect(route("register"))->with("error","Registration Failed");
    }
}
