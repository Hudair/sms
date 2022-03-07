<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Library\Tool;
use App\Models\Reports;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Exception;
use Generator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportsController extends Controller
{

    /**
     * sms reports
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function reports()
    {
        $this->authorize('view sms_history');


        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Reports')],
                ['name' => __('locale.menu.SMS History')],
        ];


        return view('admin.Reports.all_messages', compact('breadcrumbs'));
    }

    /**
     * get all message reports
     *
     * @param  Request  $request
     *
     * @throws AuthorizationException
     */
    public function searchAllMessages(Request $request)
    {
        $this->authorize('view sms_history');

        $columns = [
                0 => 'uid',
                1 => 'created_at',
                2 => 'send_by',
                3 => 'sms_type',
                4 => 'from',
                5 => 'to',
                6 => 'cost',
                7 => 'status',
                8 => 'user_id',
        ];

        $totalData = Reports::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $sms_reports = Reports::offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $sms_reports = Reports::whereLike(['uid', 'send_by', 'from', 'to', 'cost', 'status', 'created_at', 'user.first_name', 'user.last_name'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Reports::whereLike(['uid', 'send_by', 'from', 'to', 'cost', 'status', 'created_at', 'user.first_name', 'user.last_name'], $search)->count();
        }

        $data = [];
        if ( ! empty($sms_reports)) {
            foreach ($sms_reports as $report) {

                $action = null;
                $view   = __('locale.buttons.view');
                $delete = __('locale.buttons.delete');

                $action .= "<span class='action-view text-success mr-1' data-id='$report->uid' data-toggle='tooltip' data-placement='top' title='$view'><i class='feather us-2x icon-eye'></i></span>";

                $action .= "<span class='action-delete text-danger' data-id='$report->uid' data-toggle='tooltip' data-placement='top' title='$delete'><i class='feather us-2x icon-trash'></i></span>";

                if ($report->created_at == null) {
                    $created_at = null;
                }else {
                    $created_at = Tool::customerDateTime($report->created_at);
                }

                $nestedData['uid']        = $report->uid;
                $nestedData['created_at'] = $created_at;
                $nestedData['user_id']    = $report->user->displayName();
                $nestedData['send_by']    = $report->getSendBy();
                $nestedData['sms_type']   = $report->getSMSType();
                $nestedData['from']       = $report->from;
                $nestedData['to']         = $report->to;
                $nestedData['cost']       = $report->cost;
                $nestedData['status']     = str_limit($report->status, 20);
                $nestedData['action']     = $action;
                $data[]                   = $nestedData;

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
     * view single reports
     *
     * @param  Reports  $uid
     *
     * @return JsonResponse
     */
    public function viewReports(Reports $uid): JsonResponse
    {
        return response()->json([
                'status' => 'success',
                'data'   => $uid,
        ]);

    }

    /**
     * @param  Reports  $uid
     *
     * @return JsonResponse
     * @throws Exception|Exception
     */

    public function destroy(Reports $uid): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        if ( ! $uid->delete()) {
            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.exceptions.something_went_wrong'),
            ]);
        }

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.campaigns.sms_was_successfully_deleted'),
        ]);

    }

    /**
     * bulk sms delete
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

        if (Reports::whereIn('uid', $ids)->delete()) {
            return response()->json([
                    'status'  => 'success',
                    'message' => __('locale.campaigns.sms_was_successfully_deleted'),
            ]);
        }

        return response()->json([
                'status'  => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
        ]);
    }


    /**
     * @return Generator
     */

    public function reportsGenerator(): Generator
    {
        foreach (Reports::cursor() as $report) {
            yield $report;
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
            return redirect()->route('admin.reports.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('view sms_history');

        $file_name = (new FastExcel($this->reportsGenerator()))->export(storage_path('Reports_'.time().'.xlsx'));

        return response()->download($file_name);
    }
}
