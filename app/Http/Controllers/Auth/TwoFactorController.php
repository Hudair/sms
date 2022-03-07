<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Notifications\TwoFactorCode;
use App\Repositories\Contracts\AccountRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;

class TwoFactorController extends Controller
{

    /**
     * @var
     */
    protected $account;

    /**
     * Create a new controller instance.
     *
     * @param  AccountRepository  $account
     */
    public function __construct(AccountRepository $account)
    {
        $this->middleware('guest');
        $this->account = $account;
    }


    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $pageConfigs = [
                'bodyClass' => "bg-full-screen-image",
                'blankPage' => true,
        ];

        return view('/auth/twoFactor', [
                'pageConfigs' => $pageConfigs,
        ]);
    }

    /**
     * @return Application|Factory|View
     */
    public function backUpCode()
    {
        $pageConfigs = [
                'bodyClass' => "bg-full-screen-image",
                'blankPage' => true,
        ];

        return view('/auth/twoFactorBackUp', [
                'pageConfigs' => $pageConfigs,
        ]);
    }

    /**
     * verify two factor code
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {

        if (config('app.env') == 'demo') {
            return redirect()->back()->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $request->validate([
                'two_factor_code' => 'integer|required|min:6',
        ]);

        $user = auth()->user();

        if ($request->input('two_factor_code') == $user->two_factor_code) {

            $user->resetTwoFactorCode();

            Session::put('two-factor-login-success', 'success');

            $this->account->redirectAfterLogin($user);

            Session::reflash();

            return redirect(Helper::home_route());
        }

        Session::reflash();

        return redirect()->back()->with([
                'status'  => 'error',
                'message' => __('locale.auth.two_factor_code_not_matched'),
        ]);
    }


    /**
     * verify with backup code
     *
     * @param  Request  $request
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function updateBackUpCode(Request $request)
    {

        if (config('app.env') == 'demo') {
            return redirect()->back()->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $request->validate([
                'two_factor_code' => 'integer|required|min:6',
        ]);

        $user = auth()->user();

        $backUpCode = json_decode($user->two_factor_backup_code, true);


        if (isset($backUpCode) && is_array($backUpCode) && in_array($request->input('two_factor_code'), $backUpCode)) {

            $user->resetTwoFactorCode();

            Session::flash('two-factor-login-success', 'success');

            $this->account->redirectAfterLogin($user);

            Session::reflash();

            return redirect(Helper::home_route());
        }

        return redirect()->back()->with([
                'status'  => 'error',
                'message' => __('locale.auth.two_factor_code_not_matched'),
        ]);
    }

    /**
     * resend two factor code
     *
     * @return RedirectResponse
     */

    public function resend(): RedirectResponse
    {

        if (config('app.env') == 'demo') {
            return redirect()->back()->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $user = auth()->user();
        $user->generateTwoFactorCode();
        $user->notify(new TwoFactorCode());

        return redirect()->back()->with([
                'status'  => 'success',
                'message' => __('locale.auth.two_factor_code_sent'),
        ]);
    }


}
