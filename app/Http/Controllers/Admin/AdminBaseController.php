<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use App\Models\Invoices;
use App\Models\Reports;
use App\Models\Senderid;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminBaseController extends Controller
{
    /**
     * Show admin home.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function index()
    {
        $breadcrumbs = [
                ['link' => "/dashboard", 'name' => __('locale.menu.Dashboard')],
                ['name' => Auth::user()->displayName()],
        ];

        $sms_outgoing = Reports::currentMonth()
                ->selectRaw('Day(created_at) as day, count(send_by) as outgoing,send_by')
                ->where('send_by', "from")
                ->groupBy('day')->pluck('day', 'outgoing')->flip()->sortKeys();

        $sms_incoming = Reports::currentMonth()
                ->selectRaw('Day(created_at) as day, count(send_by) as incoming,send_by')
                ->where('send_by', "to")
                ->groupBy('day')->pluck('day', 'incoming')->flip()->sortKeys();


        $outgoing = (new LarapexChart)->lineChart()
                ->addData(__('locale.labels.outgoing'), $sms_outgoing->values()->toArray())
                ->setXAxis($sms_outgoing->keys()->toArray());


        $incoming = (new LarapexChart)->lineChart()
                ->addData(__('locale.labels.incoming'), $sms_incoming->values()->toArray())
                ->setXAxis($sms_incoming->keys()->toArray());


        $revenue = Invoices::CurrentMonth()
                ->selectRaw('Day(created_at) as day, sum(amount) as revenue')
                ->groupBy('day')
                ->pluck('revenue', 'day');

        $revenue_chart = (new LarapexChart)->lineChart()
                ->addData(__('locale.labels.revenue'), $revenue->values()->toArray())
                ->setXAxis($revenue->keys()->toArray());

        $customers = Customer::thisYear()
                ->selectRaw('DATE_FORMAT(created_at, "%m-%Y") as month, count(uid) as customer')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('customer', 'month');


        $customer_growth = (new LarapexChart)->barChart()
                ->addData(__('locale.labels.customers_growth'), $customers->values()->toArray())
                ->setXAxis($customers->keys()->toArray());

        $sms_history = (new LarapexChart)->pieChart()
                ->addData([
                        Reports::where('status', 'like', "%Delivered%")->count(),
                        Reports::where('status', 'not like', "%Delivered%")->count(),
                ]);

        $sender_ids = Senderid::where('status', 'pending')->latest()->take(10)->cursor();

        return view('admin.dashboard', compact('breadcrumbs', 'sms_incoming', 'sms_outgoing', 'outgoing', 'incoming', 'revenue_chart', 'customer_growth', 'sms_history','sender_ids'));
    }

    protected function redirectResponse(Request $request, $message, $type = 'success')
    {
        if ($request->wantsJson()) {
            return response()->json([
                    'status'  => $type,
                    'message' => $message,
            ]);
        }

        return redirect()->back()->with("flash_{$type}", $message);
    }

}
