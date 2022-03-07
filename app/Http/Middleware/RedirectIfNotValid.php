<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotValid
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
        if ( ! Auth::check()) {
            return redirect()->route('login')->with([
                    'status'  => 'error',
                    'message' => 'Invalid Access',
            ]);
        } elseif (Auth::check()) {
            if (Helper::app_config('license') == null) {
                return redirect()->route('verify.license');
            }
        }

        return $next($request);
    }

}
