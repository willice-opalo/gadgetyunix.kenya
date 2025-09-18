<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\RolesEnum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    // /**
    //  * Handle an incoming authentication request.
    //  */
    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     $request->authenticate();

    //     $request->session()->regenerate();

    //     $user = Auth::user();

    //     if ($user->hasAnyRole([RolesEnum::Admin->value, RolesEnum::Vendor->value])) {
    //         return Inertia::location(route('home'));
    //     }else if($user->hasRole(RolesEnum::User)){
    //         $route = route('dasboard', absolute:false)
    //     }


    //     return redirect()->intended(route('dashboard', absolute: false));
    // }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->hasAnyRole([RolesEnum::Admin->value, RolesEnum::Vendor->value])) {
            // return Inertia::location(route('home'));
            // return Inertia::location(route('filament.admin.pages.dashboard'));
            return redirect()->route('filament.admin.pages.dashboard');
        } elseif ($user->hasRole(RolesEnum::User->value)) {
            return redirect()->route('dashboard');
        }

        return redirect()->intended(route('dashboard'));
    }





    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
