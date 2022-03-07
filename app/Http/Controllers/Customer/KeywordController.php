<?php

namespace App\Http\Controllers\Customer;

use App\Http\Requests\Keywords\CustomerUpdate;
use App\Http\Requests\SenderID\PayPaymentRequest;
use App\Library\Tool;
use App\Models\Keywords;
use App\Models\PaymentMethods;
use App\Models\PhoneNumbers;
use App\Models\Senderid;
use App\Repositories\Contracts\KeywordRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KeywordController extends CustomerBaseController
{

    protected $keywords;


    /**
     * KeywordController constructor.
     *
     * @param  KeywordRepository  $keywords
     */

    public function __construct(KeywordRepository $keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @return Application|Factory|View
     * @throws AuthorizationException
     */

    public function index()
    {

        $this->authorize('view_keywords');

        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('dashboard'), 'name' => __('locale.menu.Sending')],
                ['name' => __('locale.menu.Keywords')],
        ];


        return view('customer.keywords.index', compact('breadcrumbs'));
    }


    /**
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function search(Request $request)
    {

        $this->authorize('view_keywords');

        $columns = [
                0 => 'uid',
                1 => 'title',
                2 => 'keyword_name',
                4 => 'price',
                5 => 'status',
                6 => 'uid',
        ];

        $totalData = Keywords::where('user_id', Auth::user()->id)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $keywords = Keywords::where('user_id', Auth::user()->id)->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $keywords = Keywords::where('user_id', Auth::user()->id)->whereLike(['uid', 'title', 'keyword_name', 'price'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Keywords::where('user_id', Auth::user()->id)->whereLike(['uid', 'title', 'keyword_name', 'price'], $search)->count();
        }

        $data = [];
        if ( ! empty($keywords)) {
            foreach ($keywords as $keyword) {
                $show     = route('customer.keywords.show', $keyword->uid);
                $checkout = route('customer.keywords.pay', $keyword->uid);

                $edit       = __('locale.buttons.edit');
                $release    = __('locale.labels.release');
                $renew      = __('locale.labels.renew');
                $remove_mms = __('locale.buttons.remove_mms');

                $action_url = '';

                $action_url .= "<span class='action-release text-danger mr-1' data-id='$keyword->uid'  data-toggle='tooltip' data-placement='top' title='$release'><i class='feather us-2x icon-minus-square'></i></span>";

                if ($keyword->status == 'assigned') {
                    $status     = '<div class="chip chip-success"> <div class="chip-body"><div class="chip-text text-uppercase">'.__('locale.labels.assigned').'</div></div></div>';
                    $action_url .= "<a href='$show' class='text-primary mr-1' data-toggle='tooltip' data-placement='top' title='$edit'><i class='feather us-2x icon-edit' ></i></a>";
                    if ($keyword->reply_mms) {
                        $action_url .= "<span class='action-remove-sms text-warning' data-id='$keyword->uid' data-toggle='tooltip' data-placement='top' title='$remove_mms'><i class='feather us-2x icon-delete'></i></span>";
                    }
                } else {
                    $status     = '<div class="chip chip-danger"> <div class="chip-body"><div class="chip-text text-uppercase">'.__('locale.labels.expired').'</div></div></div>';
                    $action_url .= "<a href='$checkout' class='text-primary mr-1' data-toggle='tooltip' data-placement='top' title='$renew' ><i class='feather us-2x icon-refresh-cw' ></i></a>";
                }


                $nestedData['uid']          = $keyword->uid;
                $nestedData['title']        = $keyword->title;
                $nestedData['keyword_name'] = $keyword->keyword_name;
                $nestedData['price']        = "<div>
                                                        <p class='text-bold-600'>".Tool::format_price($keyword->price, $keyword->currency->format)." </p>
                                                        <p class='text-muted'>".$keyword->displayFrequencyTime()."</p>
                                                   </div>";
                $nestedData['status']       = $status;
                $nestedData['action']       = $action_url;
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
     * show available keywords
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View
     * @throws AuthorizationException
     */
    public function buy()
    {
        $this->authorize('buy_keywords');

        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('keywords'), 'name' => __('locale.menu.Keywords')],
                ['name' => __('locale.keywords.buy_keyword')],
        ];

        return view('customer.keywords.buy', compact('breadcrumbs'));
    }


    /**
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function available(Request $request)
    {

        $this->authorize('buy_keywords');

        $columns = [
                0 => 'uid',
                1 => 'title',
                2 => 'keyword_name',
                4 => 'price',
                5 => 'uid',
        ];

        $totalData = Keywords::where('status', 'available')->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $keywords = Keywords::where('status', 'available')->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $keywords = Keywords::where('status', 'available')->whereLike(['uid', 'title', 'keyword_name', 'price'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Keywords::where('status', 'available')->whereLike(['uid', 'title', 'keyword_name', 'price'], $search)->count();
        }

        $data = [];
        if ( ! empty($keywords)) {
            foreach ($keywords as $keyword) {
                $checkout = route('customer.keywords.pay', $keyword->uid);

                $nestedData['uid']          = null;
                $nestedData['title']        = $keyword->title;
                $nestedData['keyword_name'] = $keyword->keyword_name;
                $nestedData['price']        = "<div>
                                                        <p class='text-bold-600'>".Tool::format_price($keyword->price, $keyword->currency->format)." </p>
                                                        <p class='text-muted'>".$keyword->displayFrequencyTime()."</p>
                                                   </div>";
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
     * View currency for edit
     *
     * @param  Keywords  $keyword
     *
     * @return Application|Factory|View
     *
     * @throws AuthorizationException
     */

    public function show(Keywords $keyword)
    {
        $this->authorize('update_keywords');

        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('keywords'), 'name' => __('locale.menu.Keywords')],
                ['name' => __('locale.keywords.update_keyword')],
        ];

        if (Auth::user()->customer->getOption('sender_id_verification') == 'yes') {
            $sender_ids    = Senderid::where('user_id', auth()->user()->id)->cursor();
            $phone_numbers = PhoneNumbers::where('user_id', auth()->user()->id)->cursor();
        } else {
            $sender_ids    = null;
            $phone_numbers = null;
        }

        return view('customer.keywords.show', compact('breadcrumbs', 'keyword', 'sender_ids', 'phone_numbers'));
    }


    /**
     * @param  Keywords  $keyword
     * @param  CustomerUpdate  $request
     *
     * @return RedirectResponse
     */

    public function update(Keywords $keyword, CustomerUpdate $request): RedirectResponse
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('customer.keywords.show', $keyword->uid)->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->keywords->updateByCustomer($keyword, $request->except('_method', '_token'));

        return redirect()->route('customer.keywords.show', $keyword->uid)->with([
                'status'  => 'success',
                'message' => __('locale.keywords.keyword_successfully_updated'),
        ]);
    }

    /**
     * remove mms file
     *
     * @param  Keywords  $keyword
     *
     * @return JsonResponse
     */

    public function removeMMS(Keywords $keyword): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        if ( ! $keyword->where('user_id', Auth::user()->id)->update(['reply_mms' => null])) {
            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.exceptions.something_went_wrong'),
            ]);
        }

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.keywords.keyword_mms_file_removed'),
        ]);
    }


    /**
     * @param  Keywords  $keyword
     * @param $id
     *
     * @return JsonResponse Controller|JsonResponse
     *
     * @throws AuthorizationException
     */
    public function release(Keywords $keyword, $id): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->authorize('release_keywords');

        $this->keywords->release($keyword, $id);

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.keywords.keyword_successfully_released'),
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


        $ids      = $request->get('ids');
        $keywords = Keywords::where('user_id', Auth::user()->id)->whereIn('uid', $ids)->cursor();

        foreach ($keywords as $keyword) {
            $keyword->user_id       = 1;
            $keyword->status        = 'available';
            $keyword->validity_date = null;

            $keyword->save();
        }

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.keywords.keyword_successfully_released'),
        ]);

    }


    /**
     * checkout
     *
     * @param  Keywords  $keyword
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View
     * @throws AuthorizationException
     */
    public function pay(Keywords $keyword)
    {

        $this->authorize('buy_keywords');

        $pageConfigs = [
                'bodyClass' => 'ecommerce-application',
        ];

        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('dashboard'), 'name' => __('locale.menu.Sending')],
                ['link' => url('keywords'), 'name' => __('locale.menu.Keywords')],
                ['name' => __('locale.labels.checkout')],
        ];

        $payment_methods = PaymentMethods::where('status', true)->cursor();

        return view('customer.Keywords.checkout', compact('breadcrumbs', 'pageConfigs', 'keyword', 'payment_methods'));
    }


    /**
     * pay sender id payment
     *
     * @param  Keywords  $keyword
     * @param  PayPaymentRequest  $request
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function payment(Keywords $keyword, PayPaymentRequest $request)
    {

        $data = $this->keywords->payPayment($keyword, $request->except('_token'));

        if (isset($data)) {

            if ($data->getData()->status == 'success') {

                if ($request->payment_methods == 'braintree') {
                    return view('customer.Payments.braintree', [
                            'token'    => $data->getData()->token,
                            'keyword'  => $keyword,
                            'post_url' => route('customer.keywords.braintree', $keyword->uid),
                    ]);
                }

                if ($request->payment_methods == 'stripe') {
                    return view('customer.Payments.stripe', [
                            'session_id'      => $data->getData()->session_id,
                            'publishable_key' => $data->getData()->publishable_key,
                            'keyword'         => $keyword,
                    ]);
                }

                if ($request->payment_methods == 'authorize_net') {

                    $months = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'];

                    return view('customer.Payments.authorize_net', [
                            'months'   => $months,
                            'keyword'  => $keyword,
                            'post_url' => route('customer.keywords.authorize_net', $keyword->uid),
                    ]);
                }

                if ($request->payment_methods == 'offline_payment') {
                    return view('customer.Payments.offline', [
                            'data' => $data->getData()->data,
                    ]);
                }

                return redirect()->to($data->getData()->redirect_url);
            }

            return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                    'status'  => 'error',
                    'message' => $data->getData()->message,
            ]);
        }

        return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                'status'  => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
        ]);

    }

}
