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
                0  => 'responsive_id',
                1  => 'uid',
                2  => 'uid',
                3  => 'created_at',
                4  => 'send_by',
                5  => 'sms_type',
                6  => 'from',
                7  => 'to',
                8  => 'cost',
                9  => 'status',
                10 => 'user_id',
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
                if ($report->created_at == null) {
                    $created_at = null;
                } else {
                    $created_at = Tool::customerDateTime($report->created_at);
                }

                $customer_profile = route('admin.customers.show', $report->user->uid);
                $customer_name    = $report->user->displayName();
                $user_id          = "<a href='$customer_profile' class='text-primary mr-1'>$customer_name</a>";

                $nestedData['responsive_id'] = '';
                $nestedData['uid']           = $report->uid;
                $nestedData['avatar']        = route('admin.customers.avatar', $report->user->uid);
                $nestedData['email']         = $report->user->email;
                $nestedData['created_at']    = $created_at;
                $nestedData['user_id']       = $user_id;
                $nestedData['send_by']       = $report->getSendBy();
                $nestedData['sms_type']      = $report->getSMSType();
                $nestedData['from']          = $report->from;
                $nestedData['to']            = $report->to;
                $nestedData['cost']          = $report->cost;
                $nestedData['status']        = str_limit($report->status, 20);
                $data[]                      = $nestedData;

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
     * @param $request
     *
     * @return Generator
     */
    public function exportData($request): Generator
    {
        $start_date = null;
        $end_date   = null;

        if ($request->start_date && $request->end_date) {
            $start_time = $request->start_date.' '.$request->start_time;
            $start_date = Tool::systemTimeFromString($start_time, config('app.timezone'));

            $end_time = $request->end_date.' '.$request->end_time;
            $end_date = Tool::systemTimeFromString($end_time, config('app.timezone'));
        }

        $status    = $request->status;
        $direction = $request->direction;
        $type      = $request->type;
        $to        = $request->to;
        $from      = $request->from;

        if ($status == 'delivered') {
            $status = 'Delivered';
        }

        $get_data = Reports::query()->when($status, function ($query) use ($status) {
            $query->whereLike(['status'], $status);
        })->when($from, function ($query) use ($from) {
            $query->whereLike(['from'], $from);
        })->when($to, function ($query) use ($to) {
            $query->whereLike(['to'], $to);
        })->when($direction, function ($query) use ($direction) {
            $query->where('send_by', $direction);
        })->when($start_date, function ($query) use ($start_date, $end_date) {
            $query->whereBetween('created_at', [$start_date, $end_date]);
        })->where('sms_type', $type)->cursor();

        foreach ($get_data as $report) {
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
    public function export(Request $request)
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('admin.reports.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('view sms_history');

        Tool::resetMaxExecutionTime();

        $file_name = (new FastExcel($this->exportData($request)))->export(storage_path('Reports_'.time().'.xlsx'));

        return response()->download($file_name);
    }
}
