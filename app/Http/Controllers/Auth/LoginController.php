<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Constructor to inject the UserRepository dependency.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(protected UserRepository $userRepository)
    {
    }

    // ==================== LOGIN PAGE METHODS ====================

    /**
     * Display the login page.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginPage()
    {
        return view('auth.login');
    }

    // ==================== AUTHENTICATION METHODS ====================

    /**
     * Handle user authentication.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticate(Request $request)
    {
        // Validate the request credentials
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            // Attempt to authenticate the user
            if ($this->userRepository->attempt($credentials)) {
                $request->session()->regenerate();

                // Redirect based on user type
                if (Auth::user()->data['type'] === 'Admin') {
                    return redirect()->route('dashboard.admin');
                } elseif (Auth::user()->data['type'] === 'Teacher') {
                    return redirect()->route('dashboard.teacher');
                } else {
                    return redirect('/');
                }
            }
        } catch (AuthenticationException $e) {
            // Handle authentication errors
            return back()->withErrors(['login' => $e->getMessage()]);
        }

        // Fallback for failed authentication
        return back()->withErrors(['login' => 'Invalid credentials.']);
    }

    // ==================== LOGOUT METHODS ====================

    /**
     * Handle user logout.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout(); // Log the user out
        $request->session()->invalidate(); // Invalidate the session

        return redirect('/'); // Redirect to the homepage
    }
}
