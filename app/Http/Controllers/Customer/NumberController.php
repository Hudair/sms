<?php

namespace App\Http\Controllers\Customer;

use App\Http\Requests\SenderID\PayPaymentRequest;
use App\Library\Tool;
use App\Models\PaymentMethods;
use App\Models\PhoneNumbers;
use App\Repositories\Contracts\PhoneNumberRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NumberController extends CustomerBaseController
{

    protected $numbers;


    /**
     * PhoneNumberController constructor.
     *
     * @param  PhoneNumberRepository  $numbers
     */

    public function __construct(PhoneNumberRepository $numbers)
    {
        $this->numbers = $numbers;
    }

    /**
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function index()
    {

        $this->authorize('view_numbers');

        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('dashboard'), 'name' => __('locale.menu.Sending')],
                ['name' => __('locale.menu.Numbers')],
        ];

        return view('customer.Numbers.index', compact('breadcrumbs'));
    }


    /**
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function search(Request $request)
    {

        $this->authorize('view_numbers');

        $columns = [
                0 => 'uid',
                1 => 'number',
                2 => 'price',
                3 => 'status',
                4 => 'capabilities',
                5 => 'uid',
        ];

        $totalData = PhoneNumbers::where('user_id', Auth::user()->id)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $numbers = PhoneNumbers::where('user_id', Auth::user()->id)->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $numbers = PhoneNumbers::where('user_id', Auth::user()->id)->whereLike(['uid', 'number', 'price', 'status'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = PhoneNumbers::where('user_id', Auth::user()->id)->whereLike(['uid', 'number', 'price', 'status'], $search)->count();

        }

        $data = [];
        if ( ! empty($numbers)) {
            foreach ($numbers as $number) {

                $release  = __('locale.labels.release');
                $renew    = __('locale.labels.renew');
                $checkout = route('customer.numbers.pay', $number->uid);

                $action = "<span class='action-release text-warning mr-1' data-toggle='tooltip' data-placement='top' title='$release'  data-id='$number->uid'><i class='feather us-2x icon-minus-square'></i></span>";

                if ($number->status == 'assigned') {
                    $status = '<div class="chip chip-success"> <div class="chip-body"><div class="chip-text text-uppercase">'.__('locale.labels.assigned').'</div></div></div>';
                } else {
                    $status = '<div class="chip chip-danger"> <div class="chip-body"><div class="chip-text text-uppercase">'.__('locale.labels.expired').'</div></div></div>';
                    $action .= "<a href='$checkout' class='text-primary mr-1' data-toggle='tooltip' data-placement='top' title='$renew' ><i class='feather us-2x icon-refresh-cw' ></i></a>";
                }

                $nestedData['uid']          = $number->uid;
                $nestedData['number']       = $number->number;
                $nestedData['price']        = "<div>
                                                        <p class='text-bold-600'>".Tool::format_price($number->price, $number->currency->format)." </p>
                                                        <p class='text-muted'>".$number->displayFrequencyTime()."</p>
                                                   </div>";
                $nestedData['status']       = $status;
                $nestedData['capabilities'] = $number->getCapabilities();
                $nestedData['action']       = $action;
                $data[]                     = $nestedData;

            }
        }

        $json_data = [
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $data,
        ];

        echo json_encode($json_data);
        exit();

    }

    /**
     * show available numbers
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View
     * @throws AuthorizationException
     */
    public function buy()
    {
        $this->authorize('buy_numbers');

        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('numbers'), 'name' => __('locale.menu.Numbers')],
                ['name' => __('locale.phone_numbers.buy_number')],
        ];

        return view('customer.Numbers.buy', compact('breadcrumbs'));
    }

    /**
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function availableNumbers(Request $request)
    {

        $this->authorize('buy_numbers');

        $columns = [
                0 => 'uid',
                1 => 'number',
                2 => 'price',
                3 => 'capabilities',
                4 => 'uid',
        ];

        $totalData = PhoneNumbers::where('status', 'available')->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $numbers = PhoneNumbers::where('status', 'available')->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $numbers = PhoneNumbers::where('status', 'available')->whereLike(['uid', 'number', 'price'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = PhoneNumbers::where('status', 'available')->whereLike(['uid', 'number', 'price'], $search)->count();

        }

        $data = [];
        if ( ! empty($numbers)) {
            foreach ($numbers as $number) {

                $checkout = route('customer.numbers.pay', $number->uid);

                $nestedData['uid']          = null;
                $nestedData['number']       = $number->number;
                $nestedData['price']        = "<div>
                                                        <p class='text-bold-600'>".Tool::format_price($number->price, $number->currency->format)." </p>
                                                        <p class='text-muted'>".$number->displayFrequencyTime()."</p>
                                                   </div>";
                $nestedData['capabilities'] = $number->getCapabilities();
                $nestedData['action']       = "<a href='$checkout' class='text-primary mr-1'><i class='feather us-2x icon-shopping-bag' ></i>".__('locale.labels.buy')."</a>";
                $data[]                     = $nestedData;

            }
        }

        $json_data = [
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $data,
        ];

        echo json_encode($json_data);
        exit();

    }


    /**
     * @param  PhoneNumbers  $phone_number
     * @param $id
     *
     * @return JsonResponse Controller|JsonResponse
     *
     * @throws AuthorizationException
     */
    public function release(PhoneNumbers $phone_number, $id): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('release_numbers');

        $this->numbers->release($phone_number, $id);

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.phone_numbers.number_successfully_released'),
        ]);

    }

    /**
     * batch release
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function batchAction(Request $request): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $ids     = $request->get('ids');
        $numbers = PhoneNumbers::where('user_id', Auth::user()->id)->whereIn('uid', $ids)->cursor();

        foreach ($numbers as $number) {
            $number->user_id       = 1;
            $number->status        = 'available';
            $number->validity_date = null;

            $number->save();
        }

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.phone_numbers.number_successfully_released'),
        ]);

    }


    /**
     * checkout
     *
     * @param  PhoneNumbers  $number
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View
     * @throws AuthorizationException
     */
    public function pay(PhoneNumbers $number)
    {

        $this->authorize('buy_numbers');

        $pageConfigs = [
                'bodyClass' => 'ecommerce-application',
        ];

        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('dashboard'), 'name' => __('locale.menu.Sending')],
                ['link' => url('numbers'), 'name' => __('locale.menu.Numbers')],
                ['name' => __('locale.labels.checkout')],
        ];

        $payment_methods = PaymentMethods::where('status', true)->cursor();

        return view('customer.Numbers.checkout', compact('breadcrumbs', 'pageConfigs', 'number', 'payment_methods'));
    }


    /**
     * pay sender id payment
     *
     * @param  PhoneNumbers  $number
     * @param  PayPaymentRequest  $request
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function payment(PhoneNumbers $number, PayPaymentRequest $request)
    {

        $data = $this->numbers->payPayment($number, $request->except('_token'));

        if (isset($data)) {

            if ($data->getData()->status == 'success') {

                if ($request->payment_methods == 'braintree') {
                    return view('customer.Payments.braintree', [
                            'token'    => $data->getData()->token,
                            'number'   => $number,
                            'post_url' => route('customer.numbers.braintree', $number->uid),
                    ]);
                }

                if ($request->payment_methods == 'stripe') {
                    return view('customer.Payments.stripe', [
                            'session_id'      => $data->getData()->session_id,
                            'publishable_key' => $data->getData()->publishable_key,
                            'number'          => $number,
                    ]);
                }

                if ($request->payment_methods == 'authorize_net') {

                    $months = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'];

                    return view('customer.Payments.authorize_net', [
                            'months'   => $months,
                            'number'   => $number,
                            'post_url' => route('customer.numbers.authorize_net', $number->uid),
                    ]);
                }

                if ($request->payment_methods == 'offline_payment') {
                    return view('customer.Payments.offline', [
                            'data' => $data->getData()->data,
                    ]);
                }

                return redirect()->to($data->getData()->redirect_url);
            }

            return redirect()->route('customer.numbers.pay', $number->uid)->with([
                    'status'  => 'error',
                    'message' => $data->getData()->message,
            ]);
        }

        return redirect()->route('customer.numbers.pay', $number->uid)->with([
                'status'  => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
        ]);

    }

}
