<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\userRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct(protected userRepository $userRepository)
    {
    }
    public function showLoginPage()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        try{
            if($this->userRepository->attempt($credentials)){
                $request->session()->regenerate();
                return redirect()->intended('/dashboard');
            }
        }catch(AuthenticationException $e){
            return back()->withErrors(['login' => $e->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect('/');
    }
}
