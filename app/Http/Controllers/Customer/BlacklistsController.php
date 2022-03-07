<?php

namespace App\Http\Controllers\Customer;

use App\Http\Requests\Blacklists\StoreBlacklist;
use App\Models\Blacklists;
use App\Repositories\Contracts\BlacklistsRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BlacklistsController extends CustomerBaseController
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

        $this->authorize('view_blacklist');

        $breadcrumbs = [
                ['link' => url("dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['name' => __('locale.menu.Blacklist')],
        ];

        return view('customer.Blacklists.index', compact('breadcrumbs'));
    }


    /**
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function search(Request $request)
    {

        $this->authorize('view_blacklist');

        $columns = [
                0 => 'uid',
                1 => 'number',
                2 => 'reason',
                3 => 'uid',
        ];

        $totalData = Blacklists::where('user_id', Auth::user()->id)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $blacklists = Blacklists::where('user_id', Auth::user()->id)->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $blacklists = Blacklists::where('user_id', Auth::user()->id)->whereLike(['uid', 'number', 'reason'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Blacklists::where('user_id', Auth::user()->id)->whereLike(['uid', 'number', 'reason'], $search)->count();

        }

        $data = [];
        if ( ! empty($blacklists)) {
            foreach ($blacklists as $blacklist) {

                if ($blacklist->reason) {
                    $reason = $blacklist->reason;
                } else {
                    $reason = '--';
                }

                $nestedData['uid']    = $blacklist->uid;
                $nestedData['number'] = $blacklist->number;
                $nestedData['reason'] = $reason;
                $nestedData['action'] = "<span class='action-delete text-danger' data-id='$blacklist->uid'><i class='feather us-2x icon-trash'></i></span>";
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

    public function create()
    {
        $this->authorize('create_blacklist');

        $breadcrumbs = [
                ['link' => url("dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('blacklists'), 'name' => __('locale.menu.Blacklist')],
                ['name' => __('locale.blacklist.add_new_blacklist')],
        ];

        return view('customer.Blacklists.create', compact('breadcrumbs'));
    }


    /**
     * @param  StoreBlacklist  $request
     *
     * @return RedirectResponse
     */

    public function store(StoreBlacklist $request): RedirectResponse
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('customer.blacklists.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->blacklists->store($request->input());

        return redirect()->route('customer.blacklists.index')->with([
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


        $this->authorize('delete_blacklist');

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
                $this->authorize('delete_blacklist');

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

}
