<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Library\Tool;
use App\Models\Invoices;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{

    /**
     * view invoices
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('view invoices');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Reports')],
                ['name' => __('locale.menu.All Invoices')],
        ];

        return view('admin.Invoices.index', compact('breadcrumbs'));
    }

    /**
     * @param  Request  $request
     *
     * @return void
     */
    public function search(Request $request)
    {

        $columns = [
                0 => 'uid',
                1 => 'created_at',
                2 => 'type',
                3 => 'description',
                4 => 'amount',
                5 => 'status',
                6 => 'user_id',
        ];

        $totalData = Invoices::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $invoices = Invoices::offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $invoices = Invoices::whereLike(['uid', 'type', 'created_at', 'description', 'amount', 'status', 'user.first_name', 'user.last_name'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Invoices::whereLike(['uid', 'type', 'created_at', 'description', 'amount', 'status', 'user.first_name', 'user.last_name'], $search)->count();

        }

        $data = [];
        if ( ! empty($invoices)) {
            foreach ($invoices as $invoice) {

                $show = route('admin.invoices.view', $invoice->uid);

                $customer_profile = route('admin.customers.show', $invoice->user->uid);
                $customer_name    = $invoice->user->displayName();
                $user_id          = "<a href='$customer_profile' class='text-primary mr-1'>$customer_name</a>";

                $nestedData['uid']         = $invoice->uid;
                $nestedData['user_id']     = $user_id;
                $nestedData['created_at']  = Tool::customerDateTime($invoice->created_at);
                $nestedData['type']        = strtoupper($invoice->type);
                $nestedData['description'] = str_limit($invoice->description, 35);
                $nestedData['amount']      = Tool::format_price($invoice->amount, $invoice->currency->format);
                $nestedData['status']      = $invoice->getStatus();
                $nestedData['action']      = "<a href='$show' class='text-primary mr-1'><i class='feather us-2x icon-eye'></i></a>
                                         <span class='action-delete text-danger' data-id='$invoice->uid'><i class='feather us-2x icon-trash'></i></span>";
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


    public function view(Invoices $invoice)
    {

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/invoices"), 'name' => __('locale.menu.All Invoices')],
                ['name' => __('locale.labels.invoice')],
        ];

        return view('admin.Invoices.view', compact('breadcrumbs', 'invoice'));
    }

    /**
     * @param  Invoices  $invoice
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Invoices $invoice): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        if ( ! $invoice->delete()) {
            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.exceptions.something_went_wrong'),
            ]);
        }

        return response()->json([
                'status'  => 'success',
                'message' => 'Invoice was deleted successfully.',
        ]);

    }

    /**
     * batch actions
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
        $ids = $request->get('ids');

        if (Invoices::whereIn('uid', $ids)->delete()) {
            return response()->json([
                    'status'  => 'success',
                    'message' => __('locale.subscription.invoices_deleted'),
            ]);
        }

        return response()->json([
                'status'  => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
        ]);
    }
}
