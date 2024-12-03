<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Trait\AuthTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    use AuthTrait;

    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $isActive=$this->checkService($request);

        if($isActive==1){

            $isLogin = $this->loginTrait($request);

            if ($isLogin) {
                $request->session()->regenerate();

                return redirect()->intended(RouteServiceProvider::HOME);
            }

            return back()->withErrors([
                'email' => __('messages.not_matched'),
            ])->onlyInput('email');

        }else{

            $message = __('messages.service_inactive');
            return back()->withErrors([
                'custom_message' => $message,
            ]);

        }
    }

    /**
     * Destroy an authenticated session.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
