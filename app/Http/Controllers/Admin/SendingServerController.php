<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SendingServer\StoreCustomServer;
use App\Http\Requests\SendingServer\StoreSendingServerRequest;
use App\Models\CustomSendingServer;
use App\Models\SendingServer;
use App\Repositories\Contracts\SendingServerRepository;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Generator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application as ApplicationAlias;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SendingServerController extends AdminBaseController
{
    protected $sendingServers;

    /**
     * SendingServerController constructor.
     *
     * @param  SendingServerRepository  $sendingServers
     */

    public function __construct(SendingServerRepository $sendingServers)
    {
        $this->sendingServers = $sendingServers;
    }


    /**
     * @return ApplicationAlias|Factory|View
     * @throws AuthorizationException
     */

    public function index()
    {

        $this->authorize('view sending_servers');

        $breadcrumbs = [
                [
                        'link' => url(config('app.admin_path')."/dashboard"),
                        'name' => __('locale.menu.Dashboard'),
                ],
                [
                        'link' => url(config('app.admin_path')."/dashboard"),
                        'name' => __('locale.menu.Sending'),
                ],
                ['name' => __('locale.menu.Sending Servers')],
        ];


        return view('admin.SendingServer.index', compact('breadcrumbs'));
    }


    /**
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function search(Request $request)
    {

        $this->authorize('view sending_servers');

        $columns = [
                0 => 'uid',
                1 => 'name',
                2 => 'type',
                3 => 'quota_value',
                4 => 'status',
                5 => 'uid',
        ];

        $totalData = SendingServer::where('user_id', auth()->user()->id)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $sending_servers = SendingServer::where('user_id', auth()->user()->id)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $sending_servers = SendingServer::where('user_id', auth()->user()->id)
                    ->whereLike(['uid', 'name', 'type'], $search)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = SendingServer::where('user_id', auth()->user()->id)
                    ->whereLike(['uid', 'name', 'type'], $search)
                    ->count();
        }

        $data = [];
        if ( ! empty($sending_servers)) {
            foreach ($sending_servers as $sending_server) {
                $show = route('admin.sending-servers.show', $sending_server->uid);

                if ($sending_server->status === true) {
                    $status = 'checked';
                } else {
                    $status = '';
                }

                if ($sending_server->type == 'http') {
                    $color = 'success';
                } else {
                    if ($sending_server->type == 'smpp') {
                        $color = 'primary';
                    } else {
                        $color = 'info';
                    }
                }

                $nestedData['uid']         = $sending_server->uid;
                $nestedData['name']        = $sending_server->name;
                $nestedData['type']        = "<div class='chip chip-$color'>
                <div class='chip-body'>
                    <div class='chip-text text-uppercase'> $sending_server->type </div>
                    </div>
                </div>";
                $nestedData['quota_value'] = "<div> <p class='text-capitalize'>"
                        .__('locale.sending_servers.sending_limit')
                        ." <span class='text-danger'>$sending_server->quota_value </span> "
                        .__('locale.sending_servers.per')
                        ." <span class='text-info'> $sending_server->quota_base $sending_server->quota_unit</span></p>  </div>";
                $nestedData['status']      = "<div class='custom-control custom-switch switch-lg custom-switch-success'>
                <input type='checkbox' class='custom-control-input get_status' id='status_$sending_server->uid' data-id='$sending_server->uid' name='status' $status>
                <label class='custom-control-label' for='status_$sending_server->uid'>
                  <span class='switch-text-left'>".__('locale.labels.active')."</span>
                  <span class='switch-text-right'>".__('locale.labels.inactive')
                        ."</span>
                </label>
              </div>";
                $nestedData['action']      = "<a href='$show' class='text-primary mr-1'><i class='feather us-2x icon-edit'></i></a>
                                         <span class='action-delete text-danger' data-id='$sending_server->uid'><i class='feather us-2x icon-trash'></i></span>";
                $data[]                    = $nestedData;

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
     * Get all sending servers
     *
     * @return ApplicationAlias|Factory|View
     *
     * @throws AuthorizationException
     */

    public function select()
    {

        $this->authorize('create sending_servers');

        $breadcrumbs = [
                [
                        'link' => url(config('app.admin_path')."/dashboard"),
                        'name' => __('locale.menu.Dashboard'),
                ],
                [
                        'link' => url(config('app.admin_path')."/sending-servers"),
                        'name' => __('locale.menu.Sending Servers'),
                ],
                [
                        'name' => __('locale.sending_servers.select_sending_server'),
                ],
        ];

        $sending_servers = $this->sendingServers->allSendingServer();

        return view('admin.SendingServer.list', compact('breadcrumbs', 'sending_servers'));
    }

    /**
     * Create New Server
     *
     * @param $type
     *
     * @return ApplicationAlias|Factory|View
     *
     * @throws AuthorizationException
     */

    protected function create($type)
    {

        $this->authorize('create sending_servers');

        $breadcrumbs = [
                [
                        'link' => url(config('app.admin_path')."/dashboard"),
                        'name' => __('locale.menu.Dashboard'),
                ],
                [
                        'link' => url(config('app.admin_path')."/sending-servers"),
                        'name' => __('locale.menu.Sending Servers'),
                ],
                [
                        'link' => url(config('app.admin_path')."/sending-servers/select"),
                        'name' => __('locale.sending_servers.select_sending_server'),
                ],
        ];

        if ($type == 'custom') {

            $breadcrumbs[] = [
                    'name' => __('locale.sending_servers.create_own_server'),
            ];

            return view('admin.SendingServer.create_custom', compact('breadcrumbs'));
        }

        $server = $this->sendingServers->allSendingServer()[$type];

        $breadcrumbs[] = ['name' => $server['name']];

        return view('admin.SendingServer.create', compact('server', 'breadcrumbs'));

    }


    /**
     * Store Sending Server
     *
     * @param  StoreSendingServerRequest  $request
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function store(StoreSendingServerRequest $request): RedirectResponse
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('admin.sending-servers.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->authorize('create sending_servers');

        $this->sendingServers->store($request->input());

        return redirect()->route('admin.sending-servers.index')->with([
                'status'  => 'success',
                'message' => __('locale.sending_servers.sending_server_successfully_added'),
        ]);
    }

    /**
     * Show existing sending server
     *
     * @param  SendingServer  $server
     *
     * @return ApplicationAlias|Factory|RedirectResponse|View
     * @throws AuthorizationException
     */

    public function show(SendingServer $server)
    {
        $this->authorize('edit sending_servers');

        $server = $server->toArray();
        if ( ! is_array($server)) {
            return redirect()->route('admin.sending-servers.index')->with([
                    'status'  => 'error',
                    'message' => __('locale.sending_servers.sending_server_not_found'),
            ]);
        }

        $breadcrumbs = [
                [
                        'link' => url(config('app.admin_path')."/dashboard"),
                        'name' => __('locale.menu.Dashboard'),
                ],
                [
                        'link' => url(config('app.admin_path')."/sending-servers"),
                        'name' => __('locale.menu.Sending Servers'),
                ],
        ];

        if ($server['custom'] == true) {

            $custom_info = CustomSendingServer::where('server_id', $server['id'])->first();

            if ($custom_info) {
                $breadcrumbs[] = [
                        'name' => __('locale.sending_servers.create_own_server'),
                ];

                $data = $custom_info->toArray();

                return view('admin.SendingServer.edit_custom', compact('server', 'data', 'breadcrumbs'));
            }

            return redirect()->route('admin.sending-servers.index')->with([
                    'status'  => 'error',
                    'message' => __('locale.sending_servers.sending_server_not_found'),
            ]);
        }

        $breadcrumbs[] = ['name' => $server['name']];

        return view('admin.SendingServer.create', compact('server', 'breadcrumbs'));
    }


    /**
     * Update existing sending server
     *
     * @param  SendingServer  $sendingServer
     * @param  StoreSendingServerRequest  $request
     *
     * @return RedirectResponse
     * @throws AuthorizationException
     */

    public function update(SendingServer $sendingServer, StoreSendingServerRequest $request): RedirectResponse
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('admin.sending-servers.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->authorize('edit sending_servers');

        $this->sendingServers->update($sendingServer, $request->input());

        return redirect()->route('admin.sending-servers.index')->with([
                'status'  => 'success',
                'message' => __('locale.sending_servers.sending_server_successfully_updated'),
        ]);
    }

    /**
     * Add Customer Server
     *
     * @param  StoreCustomServer  $request
     *
     * @return RedirectResponse
     * @throws AuthorizationException
     */

    public function addCustomServer(StoreCustomServer $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.sending-servers.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->authorize('create sending_servers');

        $this->sendingServers->storeCustom($request->input());

        return redirect()->route('admin.sending-servers.index')->with([
                'status'  => 'success',
                'message' => __('locale.sending_servers.sending_server_successfully_added'),
        ]);
    }

    /**
     * Update existing sending server
     *
     * @param  SendingServer  $sendingServer
     * @param  StoreCustomServer  $request
     *
     * @return RedirectResponse
     * @throws AuthorizationException
     */

    public function updateCustomServer(SendingServer $sendingServer, StoreCustomServer $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.sending-servers.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('edit sending_servers');

        $this->sendingServers->updateCustom($sendingServer, $request->input());

        return redirect()->route('admin.sending-servers.index')->with([
                'status'  => 'success',
                'message' => __('locale.sending_servers.sending_server_successfully_updated'),
        ]);
    }

    /**
     * change sending server status
     *
     * @param  SendingServer  $server
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */

    public function activeToggle(SendingServer $server): JsonResponse
    {

        if (config('app.env') == 'demo') {

            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->authorize('edit sending_servers');

        $server->update(['status' => ! $server->status]);

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.sending_servers.sending_server_successfully_change'),
        ]);

    }


    /**
     * Delete sending server
     *
     * @param  SendingServer  $sendingServer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(SendingServer $sendingServer): JsonResponse
    {

        if (config('app.env') == 'demo') {

            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->authorize('delete sending_servers');

        $this->sendingServers->destroy($sendingServer);

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.sending_servers.sending_server_successfully_deleted'),
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
                $this->authorize('delete sending_servers');

                $this->sendingServers->batchDestroy($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.sending_servers.sending_servers_deleted'),
                ]);

            case 'enable':
                $this->authorize('edit sending_servers');

                $this->sendingServers->batchActive($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.sending_servers.sending_servers_enabled'),
                ]);

            case 'disable':

                $this->authorize('edit sending_servers');

                $this->sendingServers->batchDisable($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.sending_servers.sending_servers_disabled'),
                ]);
        }

        return response()->json([
                'status'  => 'error',
                'message' => __('locale.exceptions.invalid_action'),
        ]);

    }


    /**
     *
     * @return Generator
     */

    public function sendingServerGenerator(): Generator
    {
        foreach (SendingServer::cursor() as $sendingServer) {
            yield $sendingServer;
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
            return redirect()->route('admin.sending-servers.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('view sending_servers');

        $file_name = (new FastExcel($this->sendingServerGenerator()))->export(storage_path('SendingServers_'.time().'.xlsx'));

        return response()->download($file_name);
    }


}
