<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
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
        $this->middleware('guest');
    }

    public function showResetForm(Request $request, $token = null)
    {
        $pageConfigs = [
                'bodyClass' => "bg-full-screen-image",
                'blankPage' => true,
        ];

        return view('auth.passwords.reset')->with(
                ['token' => $token, 'email' => $request->email, 'pageConfigs' => $pageConfigs]
        );
    }

    public function reset(Request $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('login')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $request->validate([
                'token'    => 'required',
                'email'    => 'required|email',
                'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) use ($request) {
                    $user->forceFill([
                            'password' => Hash::make($password),
                    ])->save();

                    $user->setRememberToken(Str::random(60));

                    event(new PasswordReset($user));
                }
        );

        return $status == Password::PASSWORD_RESET
                ? redirect()->route('login')->with([
                        'status'  => 'success',
                        'message' => __('locale.auth.password_reset_successfully'),
                ])
                : back()->with([
                        'status'  => 'error',
                        'message' => __($status),
                ]);
    }

}
