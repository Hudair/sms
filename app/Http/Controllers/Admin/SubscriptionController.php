<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Subscription\StoreSubscription;
use App\Library\Tool;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Repositories\Contracts\SubscriptionRepository;
use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends AdminBaseController
{
    protected $subscriptions;

    /**
     * SubscriptionController constructor.
     *
     * @param  SubscriptionRepository  $subscriptions
     */
    public function __construct(SubscriptionRepository $subscriptions)
    {
        $this->subscriptions = $subscriptions;
    }


    /**
     * @return Application|Factory|View
     * @throws AuthorizationException
     */

    public function index()
    {

        $this->authorize('view subscription');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Customer')],
                ['name' => __('locale.menu.Subscriptions')],
        ];


        return view('admin.subscriptions.index', compact('breadcrumbs'));
    }


    /**
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function search(Request $request)
    {

        $this->authorize('view subscription');

        $columns = [
                0 => 'uid',
                1 => 'name',
                2 => 'subscribed_by',
                3 => 'start_at',
                4 => 'end_at',
                5 => 'status',
                6 => 'uid',
        ];

        $totalData = Subscription::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $subscriptions = Subscription::offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $subscriptions = Subscription::whereLike(['uid', 'user.first_name', 'user.last_name', 'plan.name'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Subscription::whereLike(['uid', 'user.first_name', 'user.last_name', 'plan.name'], $search)->count();
        }

        $data = [];
        if ( ! empty($subscriptions)) {
            foreach ($subscriptions as $subscription) {

                if ($subscription->status == 'new' || $subscription->status == 'active') {
                    $color        = 'success';
                    $status_label = __('locale.labels.active');
                } else {
                    $color        = 'danger';
                    $status_label = __('locale.labels.'.$subscription->status);
                }

                if ($subscription->status == 'pending') {
                    $color = 'warning';
                }

                if ($subscription->status == 'renew') {
                    $color = 'primary';
                }

                $customer_name    = $subscription->user->displayName();
                $customer_profile = route('admin.customers.show', $subscription->user->uid);

                if ($subscription->start_at) {
                    $subscribed_on = Tool::dateTime($subscription->start_at)->format('M d, Y');
                } else {
                    $subscribed_on = Tool::dateTime($subscription->created_at)->format('M d, Y');
                }

                if ($subscription->isEnded()) {
                    $end_at = Tool::dateTime($subscription->end_at)->diffForHumans();
                } elseif ($subscription->cancelled()) {
                    if ($subscription->current_period_ends_at) {
                        $end_at = Tool::dateTime($subscription->current_period_ends_at)->diffForHumans();
                    } else {
                        $end_at = '--';
                    }
                } elseif ($subscription->isRecurring()) {
                    if ($subscription->current_period_ends_at) {
                        $end_at = Tool::dateTime($subscription->current_period_ends_at)->diffForHumans();
                    } else {
                        $end_at = '--';
                    }
                } else {
                    $end_at = Tool::dateTime($subscription->end_at)->diffForHumans();
                }


                $return_url = "<a class='text-info mr-1' href=".route('admin.subscriptions.logs', $subscription->uid)." data-toggle='tooltip' data-placement='top' title=".__('locale.subscription.logs')."><i class='feather us-2x icon-bar-chart-2'></i></a>";

                if ($subscription->isActive() || $subscription->isNew()) {
                    $return_url .= "<span class='action-cancel text-warning' data-id='$subscription->uid' data-toggle='tooltip' data-placement='top' title=".__('locale.buttons.cancel')."><i class='feather us-2x icon-stop-circle'></i></span>";
                }

                if ($subscription->isEnded()) {
                    $return_url .= "<span class='action-delete text-danger' data-id='$subscription->uid' data-toggle='tooltip' data-placement='top' title=".__('locale.buttons.delete')."><i class='feather us-2x icon-trash'></i></span>";
                }

                $nestedData['uid']           = $subscription->uid;
                $nestedData['name']          = $subscription->plan->name;
                $nestedData['subscribed_by'] = "<a href='$customer_profile' class='text-primary mr-1'>$customer_name</a>";
                $nestedData['start_at']      = $subscribed_on;
                $nestedData['end_at']        = $end_at;
                $nestedData['status']        = "<div class='chip chip-$color'>
                                                    <div class='chip-body'>
                                                        <div class='chip-text text-uppercase'> $status_label </div>
                                                        </div>
                                                    </div>";
                $nestedData['action']        = $return_url;
                $data[]                      = $nestedData;

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
     * @return Application|Factory|View
     * @throws AuthorizationException
     */

    public function create()
    {
        $this->authorize('new subscription');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/subscriptions"), 'name' => __('locale.menu.Subscriptions')],
                ['name' => __('locale.buttons.new_subscription')],
        ];

        $customers = User::where('status', true)->where('is_customer', true)->cursor();
        $plans     = Plan::where('status', true)->orderBy('price')->cursor();

        return view('admin.subscriptions.create', compact('breadcrumbs', 'customers', 'plans'));
    }


    /**
     * assign new subscription to customer
     *
     * @param  StoreSubscription  $request
     *
     * @return RedirectResponse
     */
    public function store(StoreSubscription $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.subscriptions.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $response = $this->subscriptions->store($request->input());


        if (isset($response) && isset($response->getData()->status) && $response->getData()->status == 'error') {
            return redirect()->route('admin.subscriptions.create')->with([
                    'status'  => 'error',
                    'message' => $response->getData()->message,
            ]);
        }

        return redirect()->route('admin.subscriptions.index')->with([
                'status'  => 'success',
                'message' => __('locale.subscription.subscription_successfully_added'),
        ]);

    }

    /**
     * view specific subscription logs
     *
     * @param  Subscription  $subscription
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View
     * @throws AuthorizationException
     */
    public function logs(Subscription $subscription)
    {

        $this->authorize('manage subscription');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/subscriptions"), 'name' => __('locale.menu.Subscriptions')],
                ['name' => __('locale.subscription.logs')],
        ];

        return view('admin.subscriptions.logs', compact('breadcrumbs', 'subscription'));
    }

    /**
     * approve pending subscription
     *
     * @param  Subscription  $subscription
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function approvePending(Subscription $subscription): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('manage subscription');

        $this->subscriptions->approvePending($subscription);

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.subscription.subscription_successfully_active'),
        ]);
    }

    /**
     * reject pending subscription
     *
     * @param  Subscription  $subscription
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function rejectPending(Subscription $subscription, Request $request): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('manage subscription');

        if ( ! $request->reason) {
            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.subscription.reject_reason_required'),
            ]);
        }

        $this->subscriptions->rejectPending($subscription, $request->only('reason'));

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.subscription.subscription_successfully_active'),
        ]);
    }


    /**
     * cancel subscription
     *
     * @param  Subscription  $subscription
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function cancel(Subscription $subscription): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('manage subscription');

        $subscription->setEnded(Auth::user()->id);

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.subscription.subscription_successfully_cancelled'),
        ]);
    }


    /**
     * @param  Subscription  $subscription
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function destroy(Subscription $subscription): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('delete subscription');

        $this->subscriptions->destroy($subscription);

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.subscription.subscription_successfully_deleted'),
        ]);

    }

    /**
     * Bulk Action with Enable, Disable and Delete
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */

    public function batchAction(Request $request): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $action = $request->get('action');
        $ids    = $request->get('ids');

        switch ($action) {

            case 'cancel':

                $this->authorize('manage subscription');

                $this->subscriptions->batchCancel($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.subscription.subscriptions_cancelled'),
                ]);
        }

        return response()->json([
                'status'  => 'error',
                'message' => __('locale.exceptions.invalid_action'),
        ]);

    }

}
