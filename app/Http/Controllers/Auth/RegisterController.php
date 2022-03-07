<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\AccountRepository;
use Arcanedev\NoCaptcha\Rules\CaptchaRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * @var AccountRepository
     */
    protected $account;


    /**
     * RegisterController constructor.
     *
     * @param  AccountRepository  $account
     */
    public function __construct(AccountRepository $account)
    {
        $this->middleware('guest');
        $this->account = $account;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
                'first_name' => ['required', 'string', 'max:255'],
                'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password'   => ['required', 'string', 'min:8', 'confirmed'],
        ];

        if (config('no-captcha.registration')) {
            $rules['g-recaptcha-response'] = ['required', new CaptchaRule()];
        }

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     *
     * @return RedirectResponse|mixed
     */

    protected function create(array $data)
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('login')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        return $this->account->register($data);
    }

    // Register
    public function showRegistrationForm()
    {
        $pageConfigs = [
                'bodyClass' => "bg-full-screen-image",
                'blankPage' => true,
        ];

        return view('/auth/register', [
                'pageConfigs' => $pageConfigs,
        ]);
    }

}
