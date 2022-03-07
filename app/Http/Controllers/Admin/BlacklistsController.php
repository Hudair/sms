<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Blacklists\StoreBlacklist;
use App\Models\Blacklists;
use App\Repositories\Contracts\BlacklistsRepository;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
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

class BlacklistsController extends AdminBaseController
{
    protected $blacklists;


    /**
     * BlacklistsController constructor.
     *
     * @param  BlacklistsRepository  $blacklists
     */

    public function __construct(BlacklistsRepository $blacklists)
    {
        $this->blacklists = $blacklists;
    }

    /**
     * @return Application|Factory|View
     * @throws AuthorizationException
     */

    public function index()
    {

        $this->authorize('view blacklist');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Security')],
                ['name' => __('locale.menu.Blacklist')],
        ];

        return view('admin.Blacklists.index', compact('breadcrumbs'));
    }


    /**
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function search(Request $request)
    {

        $this->authorize('view blacklist');

        $columns = [
                0 => 'uid',
                1 => 'number',
                2 => 'user_id',
                3 => 'reason',
                4 => 'uid',
        ];

        $totalData = Blacklists::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $blacklists = Blacklists::offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $blacklists = Blacklists::whereLike(['uid', 'number', 'reason', 'user.first_name', 'user.last_name'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Blacklists::whereLike(['uid', 'number', 'reason', 'user.first_name', 'user.last_name'], $search)->count();

        }

        $data = [];
        if ( ! empty($blacklists)) {
            foreach ($blacklists as $blacklist) {

                if ($blacklist->reason) {
                    $reason = $blacklist->reason;
                } else {
                    $reason = '--';
                }

                if ($blacklist->user->is_admin) {
                    $assign_to = $blacklist->user->displayName();
                } else {

                    $customer_profile = route('admin.customers.show', $blacklist->user->uid);
                    $customer_name    = $blacklist->user->displayName();
                    $assign_to        = "<a href='$customer_profile' class='text-primary mr-1'>$customer_name</a>";
                }

                $nestedData['uid']     = $blacklist->uid;
                $nestedData['number']  = $blacklist->number;
                $nestedData['user_id'] = $assign_to;
                $nestedData['reason']  = $reason;
                $nestedData['action']  = "<span class='action-delete text-danger' data-id='$blacklist->uid'><i class='feather us-2x icon-trash'></i></span>";
                $data[]                = $nestedData;

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
        $this->authorize('create blacklist');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/blacklists"), 'name' => __('locale.menu.Blacklist')],
                ['name' => __('locale.blacklist.add_new_blacklist')],
        ];

        return view('admin.Blacklists.create', compact('breadcrumbs'));
    }


    /**
     * @param  StoreBlacklist  $request
     *
     * @return RedirectResponse
     */

    public function store(StoreBlacklist $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.blacklists.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->blacklists->store($request->input());

        return redirect()->route('admin.blacklists.index')->with([
                'status'  => 'success',
                'message' => __('locale.blacklist.blacklist_successfully_added'),
        ]);

    }

    /**
     * @param  Blacklists  $blacklist
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function destroy(Blacklists $blacklist): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }
        $this->authorize('delete blacklist');

        $this->blacklists->destroy($blacklist);

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.blacklist.blacklist_successfully_deleted'),
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
                $this->authorize('delete blacklist');

                $this->blacklists->batchDestroy($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.blacklist.blacklists_deleted'),
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

    public function blacklistsGenerator(): Generator
    {
        foreach (Blacklists::cursor() as $blacklist) {
            yield $blacklist;
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
            return redirect()->route('admin.blacklists.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('view blacklist');

        $file_name = (new FastExcel($this->blacklistsGenerator()))->export(storage_path('blacklists_'.time().'.xlsx'));

        return response()->download($file_name);
    }

}
