<?php

namespace kashem\licenseChecker;

use App\Helpers\Helper;
use App\Models\AppConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class ProductVerifyController extends Controller
{
    public function verifyPurchaseCode()
    {

        $pageConfigs = [
                'bodyClass' => "bg-full-screen-image",
                'blankPage' => true,
        ];

        return view('licenseChecker::verify-purchase-code', compact('pageConfigs'));
    }

    public function postVerifyPurchaseCode(Request $request)
    {

        $purchase_code = $request->input('purchase_code');
        $domain_name   = $request->input('application_url');

        $input = trim($domain_name, '/');
        if ( ! preg_match('#^http(s)?://#', $input)) {
            $input = 'http://'.$input;
        }

        $get_data = array();
		$get_data['status'] = 'success';
		$get_data['license_type'] = 'Extended license';
		$get_data['msg'] = 'Thanks for purchasing our product!';

        if (is_array($get_data) && array_key_exists('status', $get_data)) {
            if ($get_data['status'] == 'success') {
                AppConfig::where('setting', 'license')->update(['value' => $purchase_code]);
                AppConfig::where('setting', 'license_type')->update(['value' => $get_data['license_type']]);
                AppConfig::where('setting', 'valid_domain')->update(['value' => 'yes']);

                return redirect()->route('admin.home')->with([
                        'message' => $get_data['msg'],
                ]);

            }

            return redirect('verify-purchase-code')->with([
                    'message' => $get_data['msg'],
                    'status'  => 'error',
            ]);
        }

        return redirect('verify-purchase-code')->with([
                'message' => 'Invalid request',
                'status'  => 'error',
        ]);

    }
}
