<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\GeneralException;
use App\Http\Requests\Customer\AddUnitRequest;
use App\Http\Requests\Customer\PermissionRequest;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateAvatarRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Requests\Customer\UpdateInformationRequest;
use App\Library\Tool;
use App\Models\Blacklists;
use App\Models\Campaigns;
use App\Models\ChatBox;
use App\Models\ContactGroups;
use App\Models\Customer;
use App\Models\Invoices;
use App\Models\Keywords;
use App\Models\Language;
use App\Models\Notifications;
use App\Models\PhoneNumbers;
use App\Models\Plan;
use App\Models\Reports;
use App\Models\Senderid;
use App\Models\SendingServer;
use App\Models\Subscription;
use App\Models\SubscriptionTransaction;
use App\Models\Templates;
use App\Models\User;
use App\Repositories\Contracts\CustomerRepository;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Exception;
use Generator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Facades\Image;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CustomerController extends AdminBaseController
{
    /**
     * @var CustomerRepository
     */
    protected $customers;

    /**
     * Create a new controller instance.
     *
     * @param  CustomerRepository  $customers
     */
    public function __construct(CustomerRepository $customers)
    {
        $this->customers = $customers;
    }


    /**
     * @return Application|Factory|View
     * @throws AuthorizationException
     */

    public function index()
    {

        $this->authorize('view customer');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Customer')],
                ['name' => __('locale.menu.Customers')],
        ];


        return view('admin.customer.index', compact('breadcrumbs'));
    }


    /**
     * view all customers
     *
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function search(Request $request)
    {

        $this->authorize('view customer');

        $columns = [
                0 => 'responsive_id',
                1 => 'uid',
                2 => 'uid',
                3 => 'name',
                4 => 'subscription',
                5 => 'status',
                6 => 'actions',
        ];

        $totalData = User::where('is_customer', 1)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $users = User::where('is_customer', 1)->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $users = User::where('is_customer', 1)->whereLike(['uid', 'first_name', 'last_name', 'status', 'email'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = User::where('is_customer', 1)->whereLike(['uid', 'first_name', 'last_name', 'status', 'email'], $search)->count();
        }

        $data = [];
        if ( ! empty($users)) {
            foreach ($users as $user) {
                $show        = route('admin.customers.show', $user->uid);
                $assign_plan = route('admin.subscriptions.create', ['customer_id' => $user->id]);

                $assign_plan_label = __('locale.customer.assign_plan');
                $edit              = __('locale.buttons.edit');
                $delete            = __('locale.buttons.delete');

                if ($user->status == true) {
                    $status = 'checked';
                } else {
                    $status = '';
                }

                if ($user->customer->currentPlanName()) {
                    $subscription = $user->customer->currentPlanName();
                } else {
                    $subscription = __('locale.subscription.no_active_subscription');
                }


                $super_user = true;
                if ($user->id != 1) {
                    $super_user = false;
                }


                $nestedData['responsive_id'] = '';
                $nestedData['uid']           = $user->uid;
                $nestedData['avatar']        = route('admin.customers.avatar', $user->uid);
                $nestedData['email']         = $user->email;
                $nestedData['name']          = $user->first_name.' '.$user->last_name;
                $nestedData['created_at']    = __('locale.labels.created_at').': '.Tool::formatDate($user->created_at);

                $nestedData['subscription'] = "<div>
                                                        <p class='text-bold-600'>$subscription </p>
                                                   </div>";

                $nestedData['status'] = "<div class='form-check form-switch form-check-primary'>
                <input type='checkbox' class='form-check-input get_status' id='status_$user->uid' data-id='$user->uid' name='status' $status>
                <label class='form-check-label' for='status_$user->uid'>
                  <span class='switch-icon-left'><i data-feather='check'></i> </span>
                  <span class='switch-icon-right'><i data-feather='x'></i> </span>
                </label>
              </div>";

                $nestedData['assign_plan']       = $assign_plan;
                $nestedData['assign_plan_label'] = $assign_plan_label;
                $nestedData['show']              = $show;
                $nestedData['show_label']        = $edit;
                $nestedData['delete']            = $user->uid;
                $nestedData['delete_label']      = $delete;
                $nestedData['super_user']        = $super_user;

                $data[] = $nestedData;

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
     * create new customer
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function create()
    {
        $this->authorize('create customer');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/customers"), 'name' => __('locale.menu.Customers')],
                ['name' => __('locale.customer.add_new')],
        ];

        $languages = Language::where('status', 1)->get();

        return view('admin.customer.create', compact('breadcrumbs', 'languages'));
    }

    /**
     *
     * add new customer
     *
     * @param  StoreCustomerRequest  $request
     *
     * @return RedirectResponse
     */
    public function store(StoreCustomerRequest $request): RedirectResponse
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('admin.customers.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $customer = $this->customers->store($request->input());

        // Upload and save image
        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                $customer->image = $customer->uploadImage($request->file('image'));
                $customer->save();
            }
        }

        return redirect()->route('admin.customers.show', $customer->uid)->with([
                'status'  => 'success',
                'message' => __('locale.customer.customer_successfully_added'),
        ]);
    }

    /**
     * View customer for edit
     *
     * @param  User  $customer
     *
     * @return Application|Factory|View
     *
     * @throws AuthorizationException
     */

    public function show(User $customer)
    {
        $this->authorize('edit customer');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/customers"), 'name' => __('locale.menu.Customers')],
                ['name' => $customer->displayName()],
        ];


        $languages = Language::where('status', 1)->get();

        $categories = collect(config('customer-permissions'))->map(function ($value, $key) {
            $value['name'] = $key;

            return $value;
        })->groupBy('category');

        $permissions = $categories->keys()->map(function ($key) use ($categories) {
            return [
                    'title'       => $key,
                    'permissions' => $categories[$key],
            ];
        });

        $existing_permission = json_decode($customer->customer->permissions, true);

        return view('admin.customer.show', compact('breadcrumbs', 'customer', 'languages', 'permissions', 'existing_permission'));
    }

    /**
     * get customer avatar
     *
     * @param  User  $customer
     *
     * @return mixed
     */
    public function avatar(User $customer)
    {

        if ( ! empty($customer->imagePath())) {

            try {
                $image = Image::make($customer->imagePath());
            } catch (NotReadableException $exception) {
                $customer->image = null;
                $customer->save();

                $image = Image::make(public_path('images/profile/profile.jpg'));
            }
        } else {
            $image = Image::make(public_path('images/profile/profile.jpg'));
        }

        return $image->response();
    }

    /**
     * update avatar
     *
     * @param  User  $customer
     * @param  UpdateAvatarRequest  $request
     *
     * @return RedirectResponse
     */
    public function updateAvatar(User $customer, UpdateAvatarRequest $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.customers.show', $customer->uid)->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        try {
            // Upload and save image
            if ($request->hasFile('image')) {
                if ($request->file('image')->isValid()) {

                    // Remove old images
                    $customer->removeImage();
                    $customer->image = $customer->uploadImage($request->file('image'));
                    $customer->save();

                    return redirect()->route('admin.customers.show', $customer->uid)->with([
                            'status'  => 'success',
                            'message' => __('locale.customer.avatar_update_successful'),
                    ]);
                }

                return redirect()->route('admin.customers.show', $customer->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.invalid_image'),
                ]);
            }

            return redirect()->route('admin.customers.show', $customer->uid)->with([
                    'status'  => 'error',
                    'message' => __('locale.exceptions.invalid_image'),
            ]);

        } catch (Exception $exception) {
            return redirect()->route('admin.customers.show', $customer->uid)->with([
                    'status'  => 'error',
                    'message' => $exception->getMessage(),
            ]);
        }
    }


    /**
     * remove avatar
     *
     * @param  User  $customer
     *
     * @return JsonResponse
     */
    public function removeAvatar(User $customer): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        // Remove old images
        $customer->removeImage();
        $customer->image = null;
        $customer->save();

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.customer.avatar_remove_successful'),
        ]);
    }


    /**
     * update customer basic account information
     *
     * @param  User  $customer
     * @param  UpdateCustomerRequest  $request
     *
     * @return RedirectResponse
     */

    public function update(User $customer, UpdateCustomerRequest $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.customers.show', $customer->uid)->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->customers->update($customer, $request->input());

        return redirect()->route('admin.customers.show', $customer->uid)->withInput(['tab' => 'account'])->with([
                'status'  => 'success',
                'message' => __('locale.customer.customer_successfully_updated'),
        ]);
    }


    /**
     * update customer detail information
     *
     * @param  User  $customer
     * @param  UpdateInformationRequest  $request
     *
     * @return RedirectResponse
     */
    public function updateInformation(User $customer, UpdateInformationRequest $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.customers.show', $customer->uid)->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->customers->updateInformation($customer, $request->except('_token'));

        return redirect()->route('admin.customers.show', $customer->uid)->withInput(['tab' => 'information'])->with([
                'status'  => 'success',
                'message' => __('locale.customer.customer_successfully_updated'),
        ]);
    }


    /**
     * update user permission
     *
     * @param  User  $customer
     * @param  PermissionRequest  $request
     *
     * @return RedirectResponse
     */
    public function permissions(User $customer, PermissionRequest $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.customers.show', $customer->uid)->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->customers->permissions($customer, $request->only('permissions'));

        return redirect()->route('admin.customers.show', $customer->uid)->withInput(['tab' => 'permission'])->with([
                'status'  => 'success',
                'message' => __('locale.customer.customer_successfully_updated'),
        ]);
    }


    /**
     * change customer status
     *
     * @param  User  $customer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws GeneralException
     */
    public function activeToggle(User $customer): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }
        try {
            $this->authorize('edit customer');

            if ($customer->update(['status' => ! $customer->status])) {
                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.customer.customer_successfully_change'),
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
     * Bulk Action with Enable, Disable
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

            case 'enable':

                $this->authorize('edit customer');

                $this->customers->batchEnable($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.customer.customers_enabled'),
                ]);

            case 'disable':

                $this->authorize('edit customer');

                $this->customers->batchDisable($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.customer.customers_disabled'),
                ]);
        }

        return response()->json([
                'status'  => 'error',
                'message' => __('locale.exceptions.invalid_action'),
        ]);

    }

    /**
     * destroy customer
     *
     * @param  User  $customer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Exception
     */
    public function destroy(User $customer): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('delete customer');

        PhoneNumbers::where('user_id', $customer->id)->update([
                'status' => 'available',
        ]);

        Blacklists::where('user_id', $customer->id)->delete();
        Campaigns::where('user_id', $customer->id)->delete();
        ChatBox::where('user_id', $customer->id)->delete();
        ContactGroups::where('customer_id', $customer->id)->delete();
        Customer::where('user_id', $customer->id)->delete();
        Invoices::where('user_id', $customer->id)->delete();
        Keywords::where('user_id', $customer->id)->delete();
        Notifications::where('user_id', $customer->id)->delete();
        Plan::where('user_id', $customer->id)->delete();
        Reports::where('user_id', $customer->id)->delete();
        Senderid::where('user_id', $customer->id)->delete();
        SendingServer::where('user_id', $customer->id)->delete();
        Subscription::where('user_id', $customer->id)->delete();
        Templates::where('user_id', $customer->id)->delete();


        if ( ! $customer->delete()) {
            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.exceptions.something_went_wrong'),
            ]);
        }

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.customer.customer_successfully_deleted'),
        ]);

    }


    /**
     * @return Generator
     */
    public function customerGenerator(): Generator
    {
        foreach (User::where('is_customer', 1)->join('customers', 'user_id', '=', 'users.id')->cursor() as $customer) {
            yield $customer;
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
            return redirect()->route('admin.customers.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('edit customer');

        $file_name = (new FastExcel($this->customerGenerator()))->export(storage_path('Customers_'.time().'.xlsx'));

        return response()->download($file_name);
    }

    /**
     * add custom unit
     *
     * @param  User  $customer
     * @param  AddUnitRequest  $request
     *
     * @return RedirectResponse
     * @throws GeneralException
     */
    public function addUnit(User $customer, AddUnitRequest $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.customers.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        try {

            if ($customer->sms_unit != '-1') {

                $balance = $customer->sms_unit + $request->add_unit;

                if ($customer->update(['sms_unit' => $balance])) {

                    $subscription = $customer->customer->activeSubscription();

                    $subscription->addTransaction(SubscriptionTransaction::TYPE_SUBSCRIBE, [
                            'end_at'                 => $subscription->end_at,
                            'current_period_ends_at' => $subscription->current_period_ends_at,
                            'status'                 => SubscriptionTransaction::STATUS_SUCCESS,
                            'title'                  => 'Add '.$request->add_unit.' sms units',
                            'amount'                 => $request->add_unit.' sms units',
                    ]);

                    return redirect()->route('admin.customers.show', $customer->uid)->with([
                            'status'  => 'success',
                            'message' => __('locale.customer.add_unit_successful'),
                    ]);
                }

                throw new GeneralException(__('locale.exceptions.something_went_wrong'));
            }

            return redirect()->route('admin.customers.show', $customer->uid)->with([
                    'status'  => 'info',
                    'message' => 'You are already in unlimited plan',
            ]);

        } catch (ModelNotFoundException $exception) {
            return redirect()->route('admin.customers.show', $customer->uid)->with([
                    'status'  => 'error',
                    'message' => $exception->getMessage(),
            ]);
        }

    }

    /**
     * remove custom unit
     *
     * @param  User  $customer
     * @param  AddUnitRequest  $request
     *
     * @return RedirectResponse
     * @throws GeneralException
     */
    public function removeUnit(User $customer, AddUnitRequest $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.customers.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        try {

            if ($customer->sms_unit != '-1') {

                $balance = $customer->sms_unit - $request->add_unit;

                if ($balance < 0) {
                    return redirect()->route('admin.customers.show', $customer->uid)->with([
                            'status'  => 'error',
                            'message' => 'Sorry! You can remove maximum '.$customer->sms_unit.' unit',
                    ]);
                }

                if ($customer->update(['sms_unit' => $balance])) {

                    $subscription = $customer->customer->activeSubscription();

                    $subscription->addTransaction(SubscriptionTransaction::TYPE_SUBSCRIBE, [
                            'end_at'                 => $subscription->end_at,
                            'current_period_ends_at' => $subscription->current_period_ends_at,
                            'status'                 => SubscriptionTransaction::STATUS_SUCCESS,
                            'title'                  => 'Remove '.$request->add_unit.' sms units',
                            'amount'                 => $request->add_unit.' sms units',
                    ]);

                    return redirect()->route('admin.customers.show', $customer->uid)->with([
                            'status'  => 'success',
                            'message' => __('locale.customer.add_unit_successful'),
                    ]);
                }

                throw new GeneralException(__('locale.exceptions.something_went_wrong'));
            }

            return redirect()->route('admin.customers.show', $customer->uid)->with([
                    'status'  => 'info',
                    'message' => 'You are already in unlimited plan',
            ]);

        } catch (ModelNotFoundException $exception) {
            return redirect()->route('admin.customers.show', $customer->uid)->with([
                    'status'  => 'error',
                    'message' => $exception->getMessage(),
            ]);
        }

    }

}
