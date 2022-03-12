<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\GeneralException;
use App\Http\Requests\Administrator\StoreAdminRole;
use App\Http\Requests\Administrator\UpdateAdminRole;
use App\Library\Tool;
use App\Models\Role;
use App\Repositories\Contracts\RoleRepository;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RoleController extends AdminBaseController
{
    /**
     * @var RoleRepository
     */
    protected $roles;

    /**
     * Create a new controller instance.
     *
     * @param  RoleRepository  $roles
     */
    public function __construct(RoleRepository $roles)
    {
        $this->roles = $roles;
    }


    /**
     * @return Application|Factory|View
     * @throws AuthorizationException
     */

    public function index()
    {

        $this->authorize('view roles');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Administrator')],
                ['name' => __('locale.menu.Admin Roles')],
        ];

        return view('admin.AdminRoles.index', compact('breadcrumbs'));
    }


    /**
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function search(Request $request)
    {

        $this->authorize('view roles');

        $columns = [
                0 => 'responsive_id',
                1 => 'uid',
                2 => 'uid',
                3 => 'name',
                4 => 'admins',
                5 => 'status',
                6 => 'actions',
        ];

        $totalData = Role::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $admin_roles = Role::offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $admin_roles = Role::whereLike(['uid', 'name'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Role::whereLike(['uid', 'name'], $search)->count();

        }

        $data = [];
        if ( ! empty($admin_roles)) {
            foreach ($admin_roles as $role) {
                $show = route('admin.roles.show', $role->uid);

                if ($role->status == true) {
                    $status = 'checked';
                } else {
                    $status = '';
                }


                $edit   = null;
                $delete = null;

                if (Auth::user()->can('edit roles')) {
                    $edit .= $show;
                }

                if (Auth::user()->can('delete roles')) {
                    $delete .= $role->uid;
                }

                $nestedData['uid']           = $role->uid;
                $nestedData['responsive_id'] = '';
                $nestedData['name']          = "<div>
                                        <h5 class='text-bold-600'><a href='$show' >".ucfirst($role->name)."</a>  </h5>
                                        <span class='text-muted'>".__('locale.labels.created_at').": ".Tool::formatDate($role->created_at)."</span>
                                        </div>";
                $nestedData['admins']        = "<div>
                                        <h5 class='text-bold-600'>".$role->admins->count()."</h5>
                                        <span class='text-muted'>".__('locale.labels.admins')."</span>
                                        </div>";

                $nestedData['status'] = "<div class='form-check form-switch form-check-primary'>
                <input type='checkbox' class='form-check-input get_status' id='status_$role->uid' data-id='$role->uid' name='status' $status>
                <label class='form-check-label' for='status_$role->uid'>
                  <span class='switch-icon-left'><i data-feather='check'></i> </span>
                  <span class='switch-icon-right'><i data-feather='x'></i> </span>
                </label>
              </div>";

                $nestedData['edit']   = $edit;
                $nestedData['delete'] = $delete;

                $data[] = $nestedData;

            }
        }

        $json_data = [
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => $totalData,
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
        $this->authorize('create roles');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/roles"), 'name' => __('locale.menu.Admin Roles')],
                ['name' => __('locale.role.create_role')],
        ];

        $categories = collect(config('permissions'))->map(function ($value, $key) {
            $value['name'] = $key;

            return $value;
        })->groupBy('category');

        $permissions = $categories->keys()->map(function ($key) use ($categories) {
            return [
                    'title'       => $key,
                    'permissions' => $categories[$key],
            ];
        });

        return view('admin.AdminRoles.create', compact('breadcrumbs', 'permissions'));
    }


    /**
     * store new plan
     *
     * @param  StoreAdminRole  $request
     *
     * @return RedirectResponse
     */
    public function store(StoreAdminRole $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.roles.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->roles->store($request->input());

        return redirect()->route('admin.roles.index')->with([
                'status'  => 'success',
                'message' => __('locale.role.role_successfully_added'),
        ]);
    }


    /**
     * View role for edit
     *
     * @param  Role  $role
     *
     * @return Application|Factory|View
     *
     * @throws AuthorizationException
     */

    public function show(Role $role)
    {
        $this->authorize('edit roles');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/roles"), 'name' => __('locale.menu.Admin Roles')],
                ['name' => __('locale.role.update_role')],
        ];
        $categories  = collect(config('permissions'))->map(function ($value, $key) {
            $value['name'] = $key;

            return $value;
        })->groupBy('category');

        $permissions = $categories->keys()->map(function ($key) use ($categories) {
            return [
                    'title'       => $key,
                    'permissions' => $categories[$key],
            ];
        });

        $existing_permission = $role->permissions;

        return view('admin.AdminRoles.create', compact('breadcrumbs', 'permissions', 'role', 'existing_permission'));
    }


    /**
     * @param  Role  $role
     * @param  UpdateAdminRole  $request
     *
     * @return RedirectResponse
     */

    public function update(Role $role, UpdateAdminRole $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.roles.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->roles->update($role, $request->input());

        return redirect()->route('admin.roles.index')->with([
                'status'  => 'success',
                'message' => __('locale.role.role_successfully_updated'),
        ]);
    }


    /**
     * change role status
     *
     * @param  Role  $role
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws GeneralException
     */
    public function activeToggle(Role $role): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        try {
            $this->authorize('edit roles');

            if ($role->update(['status' => ! $role->status])) {
                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.role.role_successfully_change'),
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
     * delete admin role
     *
     * @param  Role  $role
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */

    public function destroy(Role $role): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('delete roles');

        $this->roles->destroy($role);

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.role.role_successfully_deleted'),
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
                $this->authorize('delete roles');

                $this->roles->batchDestroy($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.role.roles_deleted'),
                ]);

            case 'enable':
                $this->authorize('edit roles');

                $this->roles->batchActive($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.role.roles_enabled'),
                ]);

            case 'disable':

                $this->authorize('edit roles');

                $this->roles->batchDisable($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.role.roles_disabled'),
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

    public function adminRoleGenerator(): Generator
    {
        foreach (Role::cursor() as $role) {
            yield $role;
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
            return redirect()->route('admin.roles.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->authorize('edit roles');

        $file_name = (new FastExcel($this->adminRoleGenerator()))->export(storage_path('AdminRoles_'.time().'.xlsx'));

        return response()->download($file_name);
    }

}
