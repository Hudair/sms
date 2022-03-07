<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\GeneralException;
use App\Http\Requests\Plan\AddSendingServerRequest;
use App\Http\Requests\Plan\CopyPlanRequest;
use App\Http\Requests\Plan\CuttingSystemRequest;
use App\Http\Requests\Plan\PlanPricingRequest;
use App\Http\Requests\Plan\SetPrimarySendingServerRequest;
use App\Http\Requests\Plan\SpeedLimitRequest;
use App\Http\Requests\Plan\StorePlanRequest;
use App\Library\Tool;
use App\Models\Currency;
use App\Models\Plan;
use App\Models\PlansSendingServer;
use App\Models\SendingServer;
use App\Repositories\Contracts\PlanRepository;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Generator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PlanController extends AdminBaseController
{
    protected $plans;

    /**
     * Plan contractor
     *
     * PlanController constructor.
     *
     * @param  PlanRepository  $plans
     */

    public function __construct(PlanRepository $plans)
    {
        $this->plans = $plans;
    }


    /**
     * @return Application|Factory|View
     * @throws AuthorizationException
     */

    public function index()
    {

        $this->authorize('manage plans');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Plan')],
                ['name' => __('locale.menu.Plans')],
        ];


        return view('admin.plans.index', compact('breadcrumbs'));
    }


    /**
     * view all plan
     *
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function search(Request $request)
    {

        $this->authorize('manage plans');

        $columns = [
                0 => 'uid',
                1 => 'name',
                2 => 'price',
                3 => 'frequency_unit',
                4 => 'frequency_amount',
                5 => 'status',
                6 => 'uid',
        ];

        $totalData = Plan::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $plans = Plan::offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $plans = Plan::whereLike(['uid', 'name', 'price', 'frequency_unit', 'frequency_amount'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Plan::whereLike(['uid', 'name', 'price', 'frequency_unit', 'frequency_amount'], $search)->count();
        }

        $data = [];
        if ( ! empty($plans)) {
            foreach ($plans as $plan) {
                $show = route('admin.plans.show', $plan->uid);

                if ($plan->status == true) {
                    $status = 'checked';
                } else {
                    $status = '';
                }

                $nestedData['uid']   = $plan->uid;
                $nestedData['name']  = "<div>
                                                        <p class='text-bold-600'> $plan->name </p>
                                                        <p class='text-muted'>".__('locale.plans.subscriber_count', ['count' => $plan->customersCount()])."</p>
                                                   </div>";
                $nestedData['price'] = "<div>
                                                        <p class='text-bold-600'>".Tool::format_price($plan->price, $plan->currency->format)." </p>
                                                        <p class='text-muted'>".$plan->displayFrequencyTime()."</p>
                                                   </div>";

                $nestedData['sending_credit'] = "<div>
                                                        <p class='text-bold-600'>".$plan->displayTotalQuota()." </p>
                                                        <p class='text-muted'>".__('locale.sending_servers.sending_credit')."</p>
                                                   </div>";

                $copy   = __('locale.buttons.copy');
                $edit   = __('locale.buttons.edit');
                $delete = __('locale.buttons.delete');

                $nestedData['status'] = "<div class='custom-control custom-switch switch-lg custom-switch-success'>
                <input type='checkbox' class='custom-control-input get_status' id='status_$plan->uid' data-id='$plan->uid' name='status' $status>
                <label class='custom-control-label' for='status_$plan->uid'>
                  <span class='switch-text-left'>".__('locale.labels.active')."</span>
                  <span class='switch-text-right'>".__('locale.labels.inactive')."</span>
                </label>
              </div>";
                $nestedData['action'] = "<a href='$show' class='text-primary mr-1' data-toggle='tooltip' data-placement='top' title='$edit'><i class='feather us-2x icon-edit' ></i></a>
                                         <span class='action-copy text-success mr-1' data-id='$plan->uid' data-value='$plan->name' data-toggle='tooltip' data-placement='top' title='$copy'><i class='feather us-2x icon-copy'></i></span>
                                         <span class='action-delete text-danger' data-id='$plan->uid'  data-toggle='tooltip' data-placement='top' title='$delete'><i class='feather us-2x icon-trash'></i></span>";
                $data[]               = $nestedData;

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
     * create new plan
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function create()
    {
        $this->authorize('create plans');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/plans"), 'name' => __('locale.menu.Plans')],
                ['name' => __('locale.plans.add_new_plan')],
        ];

        $currencies = Currency::where('status', 1)->get();

        return view('admin.plans.create', compact('breadcrumbs', 'currencies'));
    }

    /**
     * store new plan
     *
     * @param  StorePlanRequest  $request
     * @param  Plan  $plan
     *
     * @return RedirectResponse
     */
    public function store(StorePlanRequest $request, Plan $plan): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.plans.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        if (isset($request->is_popular)) {
            $popular = Plan::where('status', 1)->where('is_popular', 1)->first();
            if ($popular) {
                return redirect()->route('admin.plans.create')->with([
                        'status'  => 'error',
                        'message' => __('locale.plans.popular_plan_already_available'),
                ]);
            }
        }

        $plan = $this->plans->store($request->input(), $plan::defaultOptions(), $plan::billingCycleValues());

        return redirect()->route('admin.plans.show', $plan->uid)->with([
                'status'  => 'success',
                'message' => __('locale.plans.plan_successfully_added'),
        ]);

    }


    /**
     * View plan for edit
     *
     * @param  Plan  $plan
     *
     * @return Application|Factory|View
     *
     * @throws AuthorizationException
     */
    public function show(Plan $plan)
    {
        $this->authorize('edit plans');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/plans"), 'name' => __('locale.menu.Plans')],
                ['name' => $plan->name],
        ];

        $currencies            = Currency::where('status', 1)->get();
        $options               = json_decode($plan->options, true);
        $existing_plan_servers = PlansSendingServer::where('plan_id', $plan->id)->pluck('sending_server_id')->toArray();
        $sending_servers       = $plan->availableSendingServer($existing_plan_servers);

        return view('admin.plans.edit', compact('breadcrumbs', 'plan', 'currencies', 'options', 'sending_servers'));
    }


    /**
     * @param  Plan  $plan
     * @param  StorePlanRequest  $request
     *
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Plan $plan, StorePlanRequest $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.plans.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('edit plans');

        if (isset($request->is_popular) && $plan->is_popular == 0) {
            $popular = Plan::where('status', 1)->where('is_popular', 1)->first();
            if ($popular) {
                return redirect()->route('admin.plans.show', $plan->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.plans.popular_plan_already_available'),
                ]);
            }
        }

        $this->plans->update($plan, $request->input(), $plan::billingCycleValues());

        return redirect()->route('admin.plans.show', $plan->uid)->with([
                'status'  => 'success',
                'message' => __('locale.plans.general_settings_was_updated'),
        ]);
    }

    /**
     * update plan features
     *
     * @param  Request  $request
     * @param  Plan  $plan
     *
     * @return RedirectResponse
     * @throws AuthorizationException
     * @throws GeneralException
     */
    public function settingFeatures(Request $request, Plan $plan): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.plans.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('edit plans');

        if ( ! $request->has('sms_max') || $request->sms_max == null) {

            return redirect()->route('admin.plans.show', $plan->uid)->with([
                    'status'  => 'error',
                    'message' => __('locale.plans.sms_sending_credits_required'),
            ]);
        }

        $post_data = $request->except('_token');

        if ( ! $request->has('list_export')) {
            $post_data['list_export'] = 'no';
        }

        if ( ! $request->has('list_import')) {
            $post_data['list_import'] = 'no';
        }

        if ( ! $request->has('api_access')) {
            $post_data['api_access'] = 'no';
        }

        if ( ! $request->has('create_sending_server')) {
            $post_data['create_sending_server'] = 'no';
        }

        if ( ! $request->has('create_sub_account')) {
            $post_data['create_sub_account'] = 'no';
        }

        if ( ! $request->has('delete_sms_history')) {
            $post_data['delete_sms_history'] = 'no';
        }

        if ( ! $request->has('add_previous_balance')) {
            $post_data['add_previous_balance'] = 'no';
        }

        if ( ! $request->has('sender_id_verification')) {
            $post_data['sender_id_verification'] = 'no';
        }
        if ( ! $request->has('send_spam_message')) {
            $post_data['send_spam_message'] = 'no';
        }

        if ( ! $request->has('cutting_system')) {
            $post_data['cutting_system'] = 'no';
        }

        if ( ! $request->has('unsubscribe_url_required')) {
            $post_data['unsubscribe_url_required'] = 'no';
        }

        $get_options = json_decode($plan->options, true);
        $output      = array_replace($get_options, $post_data);

        if ( ! $plan->update(['options' => json_encode($output)])) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return redirect()->route('admin.plans.show', $plan->uid)->with([
                'status'  => 'success',
                'message' => __('locale.plans.features_was_updated'),
        ]);

    }

    /**
     *
     * update sms speed limit
     *
     * @param  SpeedLimitRequest  $request
     * @param  Plan  $plan
     *
     * @return RedirectResponse
     */

    public function updateSpeedLimit(SpeedLimitRequest $request, Plan $plan): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.plans.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $sendingLimit = $request->input('sending_limit');

        if (isset($sendingLimit) && $sendingLimit != 'custom' && $sendingLimit != 'other') {
            $input                  = $plan->sendingLimitValues()[$sendingLimit];
            $input['sending_limit'] = $request->input('sending_limit');
            $input['max_process']   = $request->input('max_process');
        } else {
            $input = $request->except('_token');
        }

        $this->plans->updateSpeedLimits($plan, $input);


        return redirect()->route('admin.plans.show', $plan->uid)->with([
                'status'  => 'success',
                'message' => __('locale.plans.speed_limit_was_updated'),
        ]);


    }

    /**
     *
     * update cutting system
     *
     * @param  CuttingSystemRequest  $request
     * @param  Plan  $plan
     *
     * @return RedirectResponse
     */

    public function updateCuttingSystem(CuttingSystemRequest $request, Plan $plan): RedirectResponse
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('admin.plans.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->plans->updateCuttingSystem($plan, $request->except('_token'));

        return redirect()->route('admin.plans.show', $plan->uid)->with([
                'status'  => 'success',
                'message' => __('locale.plans.cutting_value_was_updated'),
        ]);
    }


    /**
     * Add sending server on plan
     *
     * @param  AddSendingServerRequest  $request
     * @param  Plan  $plan
     *
     * @return RedirectResponse
     */

    public function addSendingServers(AddSendingServerRequest $request, Plan $plan): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.plans.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $server = SendingServer::findByUid($request->sending_server_id);
        if ($server) {

            $existing_plan_servers = PlansSendingServer::where('plan_id', $plan->id);
            $count                 = $existing_plan_servers->count();

            $fitness = 100;
            if ($count > 0) {
                $fitness = round(100 / ($count + 1));
                $existing_plan_servers->update([
                        'fitness' => $fitness,
                ]);
            }


            $sendingServer                    = new PlansSendingServer();
            $sendingServer->sending_server_id = $server->id;
            $sendingServer->plan_id           = $plan->id;
            $sendingServer->fitness           = $fitness;

            //by default set first one as primary
            if ( ! $plan->plansSendingServers()->where('is_primary', '=', true)->count()) {
                $sendingServer->is_primary = true;
            }

            $sendingServer->save();
            $plan->status = true;
            $plan->save();

            return redirect()->route('admin.plans.show', $plan->uid)->with([
                    'status'  => 'success',
                    'message' => __('locale.sending_servers.add_sending_server'),
            ]);

        }

        return redirect()->route('admin.plans.show', $plan->uid)->with([
                'status'  => 'error',
                'message' => __('locale.sending_servers.sending_server_not_found'),
        ]);

    }

    /**
     *
     * Update sending server fitness
     *
     * @param  Request  $request
     * @param  Plan  $plan
     *
     * @return RedirectResponse
     * @throws AuthorizationException
     */

    public function updateFitness(Request $request, Plan $plan): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.plans.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('edit plans');

        $sending_servers = $request->sending_servers;

        if (isset($sending_servers) && is_array($sending_servers) && count($sending_servers)) {

            if (array_sum($sending_servers) > 100) {
                return redirect()->route('admin.plans.show', $plan->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.plans.fitness_number_must_100'),
                ]);
            }

            $this->plans->updateFitnesses($sending_servers, $plan);

            return redirect()->route('admin.plans.show', $plan->uid)->with([
                    'status'  => 'success',
                    'message' => __('locale.plans.fitness_was_updated'),
            ]);

        }

        return redirect()->route('admin.plans.show', $plan->uid)->with([
                'status'  => 'error',
                'message' => __('locale.sending_servers.sending_server_not_found'),
        ]);


    }

    /**
     * Set primary sending server
     *
     * @param  SetPrimarySendingServerRequest  $request
     * @param  Plan  $plan
     *
     * @return JsonResponse
     */

    public function setPrimary(SetPrimarySendingServerRequest $request, Plan $plan): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->plans->setPrimarySendingServer($plan, $request->input());

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.plans.primary_sending_server_updated'),
        ]);

    }

    /**
     * delete plan sending server
     *
     * @param  SetPrimarySendingServerRequest  $request
     * @param  Plan  $plan
     *
     * @return JsonResponse
     */

    public function deletePlanSendingServer(SetPrimarySendingServerRequest $request, Plan $plan): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->plans->removeSendingServerByUid($plan, $request->input());

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.sending_servers.sending_server_successfully_deleted'),
        ]);

    }


    /**
     *
     * update sms pricing
     *
     * @param  PlanPricingRequest  $request
     * @param  Plan  $plan
     *
     * @return RedirectResponse
     */

    public function updatePricing(PlanPricingRequest $request, Plan $plan): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.plans.show', $plan->uid)->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->plans->updatePricing($plan, $request->except('_token'));

        return redirect()->route('admin.plans.show', $plan->uid)->with([
                'status'  => 'success',
                'message' => __('locale.plans.pricing_was_updated'),
        ]);
    }


    /**
     * copy existing as new
     *
     * @param  CopyPlanRequest  $request
     * @param  Plan  $plan
     *
     * @return JsonResponse
     */
    public function copy(CopyPlanRequest $request, Plan $plan): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->plans->copy($plan, $request->only('plan_name'));

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.plans.plan_was_successfully_copied'),
        ]);

    }

    /**
     * change plan status
     *
     * @param  Plan  $plan
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws GeneralException
     */
    public function activeToggle(Plan $plan): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }
        try {
            $this->authorize('manage plans');

            if ($plan->update(['status' => ! $plan->status])) {
                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.plans.plan_status_was_successfully_changed'),
                ]);
            }

            throw new GeneralException(__('locale.exceptions.something_went_wrong'));

        } catch (ModelNotFoundException $exception) {
            return response()->json([
                    'status'  => 'error',
                    'message' => $exception->getMessage(),
            ]);
        }
    }


    /**
     * Delete plan
     *
     * @param  Plan  $plan
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Plan $plan): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }
        $this->authorize('delete plans');

        $this->plans->destroy($plan);

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.plans.plan_successfully_deleted'),
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
            case 'destroy':

                $this->authorize('delete plans');

                $this->plans->batchDestroy($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.plans.plans_deleted'),
                ]);

            case 'enable':
                $this->authorize('manage plans');

                $this->plans->batchActive($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.plans.plans_enabled'),
                ]);

            case 'disable':

                $this->authorize('manage plans');

                $this->plans->batchDisable($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.plans.plans_disabled'),
                ]);
        }

        return response()->json([
                'status'  => 'error',
                'message' => __('locale.exceptions.invalid_action'),
        ]);

    }


    /**
     * @return Generator
     */

    public function planGenerator(): Generator
    {
        foreach (Plan::cursor() as $currency) {
            yield $currency;
        }
    }

    /**
     * @return RedirectResponse|BinaryFileResponse
     * @throws AuthorizationException
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function export()
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.plans.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('manage plans');

        $file_name = (new FastExcel($this->planGenerator()))->export(storage_path('Plan_'.time().'.xlsx'));

        return response()->download($file_name);
    }


}
