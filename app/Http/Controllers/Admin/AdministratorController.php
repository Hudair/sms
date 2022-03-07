<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\GeneralException;
use App\Http\Requests\Administrator\StoreAdministrator;
use App\Http\Requests\Administrator\UpdateAdministrator;
use App\Library\Tool;
use App\Models\Language;
use App\Models\User;
use App\Repositories\Contracts\RoleRepository;
use App\Repositories\Contracts\UserRepository;
use Auth;
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
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdministratorController extends AdminBaseController
{

    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * @var RoleRepository
     */
    protected $roles;

    /**
     * Create a new controller instance.
     *
     * @param  UserRepository  $users
     * @param  RoleRepository  $roles
     */
    public function __construct(UserRepository $users, RoleRepository $roles)
    {
        $this->users = $users;
        $this->roles = $roles;
    }


    /**
     * @return Application|Factory|View
     * @throws AuthorizationException
     */

    public function index()
    {

        $this->authorize('view administrator');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Administrator')],
                ['name' => __('locale.menu.Administrators')],
        ];

        return view('admin.administrator.index', compact('breadcrumbs'));
    }


    /**
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function search(Request $request)
    {

        $this->authorize('view administrator');

        $columns = [
                0 => 'uid',
                1 => 'name',
                2 => 'roles',
                3 => 'created_at',
                4 => 'status',
                5 => 'uid',
        ];


        $totalData = User::where('is_admin', 1)->where('id', '!=', 1)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $administrators = User::where('is_admin', 1)->where('id', '!=', 1)->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $administrators = User::where('is_admin', 1)->where('id', '!=', 1)->whereLike(['uid', 'first_name', 'last_name', 'status', 'email', 'created_at'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = User::where('is_admin', 1)->where('id', '!=', 1)->whereLike(['uid', 'first_name', 'last_name', 'status', 'email', 'created_at'], $search)->count();
        }

        $data = [];
        if ( ! empty($administrators)) {
            foreach ($administrators as $administrator) {
                $show = route('admin.administrators.show', $administrator->uid);

                if ($administrator->status == true) {
                    $status = 'checked';
                } else {
                    $status = '';
                }

                $get_roles = collect($administrator->roles)->map(function ($key) {
                    return ucfirst($key->display_name());
                })->join(',');

                if ($get_roles) {
                    $roles = $get_roles;
                } else {
                    $roles = __('locale.administrator.no_active_roles');
                }

                $action = null;

                if (Auth::user()->can('edit administrator')) {
                    $action .= "<a href='$show' class='text-primary mr-1'><i class='feather us-2x icon-edit'></i></a>";
                }

                if (Auth::user()->can('delete administrator')) {
                    $action .= "<span class='action-delete text-danger' data-id='$administrator->uid'><i class='feather us-2x icon-trash'></i></span>";
                }

                $nestedData['uid']        = $administrator->uid;
                $nestedData['name']       = "<div>
                                            <h5 class='text-bold-600'><a href='$show' > $administrator->first_name $administrator->last_name </a></h5>
                                             <span class='text-muted'> $administrator->email </span>
                                          </div>";
                $nestedData['roles']      = $roles;
                $nestedData['created_at'] = Tool::formatDate($administrator->created_at);
                $nestedData['status']     = "<div class='custom-control custom-switch switch-lg custom-switch-success'>
                <input type='checkbox' class='custom-control-input get_status' id='status_$administrator->uid' data-id='$administrator->uid' name='status' $status>
                <label class='custom-control-label' for='status_$administrator->uid'>
                  <span class='switch-text-left'>".__('locale.labels.active')."</span>
                  <span class='switch-text-right'>".__('locale.labels.inactive')."</span>
                </label>
              </div>";


                $nestedData['action'] = $action;
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
     * create new administrator
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View
     * @throws AuthorizationException
     */
    public function create()
    {

        $this->authorize('create administrator');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/administrators"), 'name' => __('locale.menu.Administrators')],
                ['name' => __('locale.administrator.create_administrator')],
        ];

        $roles = $this->roles->getAllowedRoles();

        return view('admin.administrator.create', compact('breadcrumbs', 'roles'));
    }


    public function store(StoreAdministrator $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.administrators.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $admin = $this->users->store($request->input());

        // Upload and save image
        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                $admin->image = $admin->uploadImage($request->file('image'));
                $admin->save();
            }
        }

        return redirect()->route('admin.administrators.index')->with([
                'status'  => 'success',
                'message' => __('locale.administrator.administrator_successfully_added'),
        ]);

    }


    /**
     * View administrator for edit
     *
     * @param  User  $administrator
     *
     * @return Application|Factory|View
     *
     * @throws AuthorizationException
     */

    public function show(User $administrator)
    {
        $this->authorize('edit administrator');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/administrators"), 'name' => __('locale.menu.Administrators')],
                ['name' => $administrator->displayName()],
        ];

        $get_roles = collect($administrator->roles)->map(function ($key) {
            return $key->id;
        })->join(',');

        $languages = Language::where('status', 1)->get();
        $roles     = $this->roles->getAllowedRoles();

        return view('admin.administrator.show', compact('breadcrumbs', 'administrator', 'languages', 'roles', 'get_roles'));
    }


    /**
     * @param  User  $administrator
     * @param  UpdateAdministrator  $request
     *
     * @return RedirectResponse
     */
    public function update(User $administrator, UpdateAdministrator $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.administrators.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->users->update($administrator, $request->input());

        // Upload and save image
        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                $administrator->image = $administrator->uploadImage($request->file('image'));
                $administrator->save();
            }
        }

        return redirect()->route('admin.administrators.index')->with([
                'status'  => 'success',
                'message' => __('locale.administrator.administrator_successfully_updated'),
        ]);
    }


    /**
     * change administrator status
     *
     * @param  User  $administrator
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws GeneralException
     */
    public function activeToggle(User $administrator): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }
        try {
            $this->authorize('edit administrator');

            if ($administrator->update(['status' => ! $administrator->status])) {
                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.administrator.administrator_successfully_change'),
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
     * delete administrator
     *
     * @param  User  $administrator
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(User $administrator): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('delete administrator');

        $this->users->destroy($administrator);

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.administrator.administrator_successfully_deleted'),
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
                $this->authorize('delete administrator');

                $this->users->batchDestroy($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.administrator.administrators_deleted'),
                ]);

            case 'enable':
                $this->authorize('edit administrator');

                $this->users->batchEnable($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.administrator.administrators_enabled'),
                ]);

            case 'disable':

                $this->authorize('edit administrator');

                $this->users->batchDisable($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.administrator.administrators_disabled'),
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

    public function AdministratorGenerator(): Generator
    {
        foreach (User::where('is_admin', 1)->cursor() as $administrator) {
            yield $administrator;
        }
    }


    /**
     *
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
            return redirect()->route('admin.administrators.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('edit administrator');

        $file_name = (new FastExcel($this->AdministratorGenerator()))->export(storage_path('Administrator_'.time().'.xlsx'));

        return response()->download($file_name);
    }

    /**
     * get allowed roles
     *
     * @return Collection
     */
    public function getRoles(): Collection
    {
        return $this->roles->getAllowedRoles();
    }
}
