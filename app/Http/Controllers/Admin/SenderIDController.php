<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\GeneralException;
use App\Http\Requests\SenderID\StoreSenderidPlan;
use App\Http\Requests\SenderID\StoreSenderidRequest;
use App\Http\Requests\SenderID\UpdateSenderidRequest;
use App\Library\Tool;
use App\Models\Currency;
use App\Models\Senderid;
use App\Models\SenderidPlan;
use App\Models\User;
use App\Repositories\Contracts\SenderIDRepository;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Exception;
use Generator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SenderIDController extends AdminBaseController
{

    protected $sender_ids;


    /**
     * SenderIDController constructor.
     *
     * @param  SenderIDRepository  $sender_ids
     */

    public function __construct(SenderIDRepository $sender_ids)
    {
        $this->sender_ids = $sender_ids;
    }

    /**
     * @return Application|Factory|View
     * @throws AuthorizationException
     */

    public function index()
    {

        $this->authorize('view sender_id');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Sending')],
                ['name' => __('locale.menu.Sender ID')],
        ];

        return view('admin.SenderID.index', compact('breadcrumbs'));
    }


    /**
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function search(Request $request)
    {

        $this->authorize('view sender_id');

        $columns = [
                0 => 'uid',
                1 => 'sender_id',
                2 => 'user_id',
                3 => 'price',
                4 => 'status',
                5 => 'uid',
        ];

        $totalData = Senderid::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $sender_ids = Senderid::offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $sender_ids = Senderid::whereLike(['uid', 'sender_id', 'price', 'status', 'user.first_name', 'user.last_name'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Senderid::whereLike(['uid', 'sender_id', 'price', 'status', 'user.first_name', 'user.last_name'], $search)->count();

        }

        $data = [];
        if ( ! empty($sender_ids)) {
            foreach ($sender_ids as $senderid) {
                $show = route('admin.senderid.show', $senderid->uid);

                if ($senderid->user->is_admin) {
                    $assign_to = $senderid->user->displayName();
                } else {

                    $customer_profile = route('admin.customers.show', $senderid->user->uid);
                    $customer_name    = $senderid->user->displayName();

                    $assign_to = "<a href='$customer_profile' class='text-primary mr-1'>$customer_name</a>";
                }

                if ($senderid->status == 'active') {
                    $status = '<div class="chip chip-success"> <div class="chip-body"><div class="chip-text text-uppercase">'.__('locale.labels.active').'</div></div></div>';
                } elseif ($senderid->status == 'pending') {
                    $status = '<div class="chip chip-primary"> <div class="chip-body"><div class="chip-text text-uppercase">'.__('locale.labels.pending').'</div></div></div>';
                } elseif ($senderid->status == 'payment_required') {
                    $status = '<div class="chip chip-info"> <div class="chip-body"><div class="chip-text text-uppercase">'.__('locale.labels.payment_required').'</div></div></div>';
                } elseif ($senderid->status == 'expired') {
                    $status = '<div class="chip chip-warning"> <div class="chip-body"><div class="chip-text text-uppercase">'.__('locale.labels.expired').'</div></div></div>';
                } else {
                    $status = '<div class="chip chip-danger"> <div class="chip-body"><div class="chip-text text-uppercase">'.__('locale.labels.block').'</div></div></div>';
                }

                $nestedData['uid']       = $senderid->uid;
                $nestedData['sender_id'] = $senderid->sender_id;
                $nestedData['user_id']   = $assign_to;
                $nestedData['price']     = "<div>
                                                        <p class='text-bold-600'>".Tool::format_price($senderid->price, $senderid->currency->format)." </p>
                                                        <p class='text-muted'>".$senderid->displayFrequencyTime()."</p>
                                                   </div>";
                $nestedData['status']    = $status;
                $nestedData['action']    = "<a href='$show' class='text-primary mr-1'><i class='feather us-2x icon-edit'></i></a>
                                         <span class='action-delete text-danger' data-id='$senderid->uid'><i class='feather us-2x icon-trash'></i></span>";
                $data[]                  = $nestedData;

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
        $this->authorize('create sender_id');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/senderid"), 'name' => __('locale.menu.Sender ID')],
                ['name' => __('locale.sender_id.add_new_sender_id')],
        ];

        $customers  = User::where('status', true)->get();
        $currencies = Currency::where('status', true)->get();

        return view('admin.SenderID.create', compact('breadcrumbs', 'currencies', 'customers'));
    }


    /**
     * View sender id for edit
     *
     * @param  Senderid  $senderid
     *
     * @return Application|Factory|View
     *
     * @throws AuthorizationException
     */

    public function show(Senderid $senderid)
    {
        $this->authorize('edit sender_id');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/senderid"), 'name' => __('locale.menu.Sender ID')],
                ['name' => __('locale.sender_id.update_sender_id')],
        ];


        $customers  = User::where('status', true)->get();
        $currencies = Currency::where('status', true)->get();


        return view('admin.SenderID.show', compact('breadcrumbs', 'senderid', 'customers', 'currencies'));
    }


    /**
     * @param  StoreSenderidRequest  $request
     * @param  Senderid  $senderid
     *
     * @return RedirectResponse
     */

    public function store(StoreSenderidRequest $request, Senderid $senderid): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.senderid.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->sender_ids->store($request->input(), $senderid::billingCycleValues());

        return redirect()->route('admin.senderid.index')->with([
                'status'  => 'success',
                'message' => __('locale.sender_id.sender_id_successfully_added'),
        ]);

    }


    /**
     * @param  Senderid  $senderid
     * @param  UpdateSenderidRequest  $request
     *
     * @return RedirectResponse
     */

    public function update(Senderid $senderid, UpdateSenderidRequest $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.senderid.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->sender_ids->update($senderid, $request->input(), $senderid::billingCycleValues());

        return redirect()->route('admin.senderid.index')->with([
                'status'  => 'success',
                'message' => __('locale.sender_id.sender_id_successfully_updated'),
        ]);
    }

    /**
     * @param  Senderid  $senderid
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function destroy(Senderid $senderid): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->authorize('delete sender_id');

        $this->sender_ids->destroy($senderid);

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.sender_id.sender_id_successfully_deleted'),
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
                $this->authorize('delete sender_id');

                $this->sender_ids->batchDestroy($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.sender_id.senderids_deleted'),
                ]);

            case 'active':
                $this->authorize('edit sender_id');

                $this->sender_ids->batchActive($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.sender_id.senderids_active'),
                ]);

            case 'block':

                $this->authorize('edit sender_id');

                $this->sender_ids->batchBlock($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.sender_id.senderids_block'),
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

    public function senderidGenerator(): Generator
    {
        foreach (Senderid::cursor() as $senderid) {
            yield $senderid;
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
            return redirect()->route('admin.senderid.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('view sender_id');

        $file_name = (new FastExcel($this->senderidGenerator()))->export(storage_path('Senderid_'.time().'.xlsx'));

        return response()->download($file_name);
    }


    /**
     * @return Application|Factory|View
     * @throws AuthorizationException
     */

    public function plan()
    {

        $this->authorize('view sender_id');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/senderid"), 'name' => __('locale.menu.Sender ID')],
                ['name' => __('locale.menu.Plan')],
        ];

        return view('admin.SenderID.plan', compact('breadcrumbs'));
    }


    /**
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function searchPlan(Request $request)
    {

        $this->authorize('view sender_id');

        $columns = [
                0 => 'uid',
                1 => 'price',
                2 => 'renew',
                3 => 'uid',
        ];

        $totalData = SenderidPlan::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $sender_ids_plan = SenderidPlan::offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $sender_ids_plan = SenderidPlan::whereLike(['uid', 'price'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = SenderidPlan::whereLike(['uid', 'price'], $search)->count();

        }

        $data = [];
        if ( ! empty($sender_ids_plan)) {
            foreach ($sender_ids_plan as $plan) {
                $nestedData['uid']   = $plan->uid;
                $nestedData['price'] = Tool::format_price($plan->price, $plan->currency->format);
                $nestedData['renew'] = __('locale.labels.every').' '.$plan->displayFrequencyTime();

                $nestedData['action'] = "<span class='action-delete text-danger' data-id='$plan->uid'><i class='feather us-2x icon-trash'></i></span>";
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
     * @return Application|Factory|View
     * @throws AuthorizationException
     */

    public function createPlan()
    {
        $this->authorize('create sender_id');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/senderid"), 'name' => __('locale.menu.Sender ID')],
                ['link' => url(config('app.admin_path')."/senderid/plan"), 'name' => __('locale.menu.Plan')],
                ['name' => __('locale.labels.create_plan')],
        ];

        $currencies = Currency::where('status', true)->get();

        return view('admin.SenderID.create-plan', compact('breadcrumbs', 'currencies'));
    }


    /**
     * @param  StoreSenderidPlan  $request
     * @param  Senderid  $senderid
     *
     * @return RedirectResponse
     */

    public function storePlan(StoreSenderidPlan $request, Senderid $senderid): RedirectResponse
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('admin.senderid.plan')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->sender_ids->storePlan($request->except('_token'), $senderid::billingCycleValues());

        return redirect()->route('admin.senderid.plan')->with([
                'status'  => 'success',
                'message' => __('locale.plans.plan_successfully_added'),
        ]);

    }

    /**
     * @param  SenderidPlan  $plan
     *
     * @return JsonResponse
     * @throws GeneralException
     * @throws Exception
     */
    public function deletePlan(SenderidPlan $plan): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        if ( ! $plan->delete()) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.plans.plan_successfully_deleted'),
        ]);
    }

    /**
     * delete batch sender id plans
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     * @throws GeneralException
     */
    public function deleteBatchPlan(Request $request): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $ids    = $request->get('ids');
        $status = SenderidPlan::whereIn('uid', $ids)->delete();

        if ( ! $status) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.plans.plan_successfully_deleted'),
        ]);

    }

}
