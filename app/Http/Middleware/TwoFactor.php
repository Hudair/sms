<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactor
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if (config('app.two_factor')) {

            $user = auth()->user();

            if (auth()->check() && $user->two_factor && $user->two_factor_code) {
                if ($user->two_factor_expires_at->lt(now())) {
                    $user->resetTwoFactorCode();

                    auth()->logout();

                    return redirect()->route('login')->with([
                            'status'  => 'info',
                            'message' => __('locale.auth.two_factor_code_expired'),
                    ]);
                }

                if ( ! $request->is('verify*')) {
                    return redirect()->route('verify.index');
                }

            }
        }

        return $next($request);
    }
}
