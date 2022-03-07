<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Reports;
use App\Repositories\Contracts\UserRepository;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * UserController constructor.
     *
     * @param  UserRepository  $users
     */
    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * Show user homepage.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function index()
    {

        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['name' => Auth::user()->displayName()],
        ];


        $sms_outgoing = Reports::currentMonth()
                ->where('user_id', Auth::user()->id)
                ->selectRaw('Day(created_at) as day, count(send_by) as outgoing,send_by')
                ->where('send_by', "from")
                ->groupBy('day')->pluck('day', 'outgoing')->flip()->sortKeys();

        $sms_incoming = Reports::currentMonth()
                ->where('user_id', Auth::user()->id)
                ->selectRaw('Day(created_at) as day, count(send_by) as incoming,send_by')
                ->where('send_by', "to")
                ->groupBy('day')->pluck('day', 'incoming')->flip()->sortKeys();


        $outgoing = (new LarapexChart)->lineChart()
                ->addData(__('locale.labels.outgoing'), $sms_outgoing->values()->toArray())
                ->setXAxis($sms_outgoing->keys()->toArray());


        $incoming = (new LarapexChart)->lineChart()
                ->addData(__('locale.labels.incoming'), $sms_incoming->values()->toArray())
                ->setXAxis($sms_incoming->keys()->toArray());

        $sms_history = (new LarapexChart)->pieChart()
                ->addData([
                        Reports::where('status', 'like', "%Delivered%")->where('user_id', Auth::user()->id)->count(),
                        Reports::where('status', 'not like', "%Delivered%")->where('user_id', Auth::user()->id)->count(),
                ]);

        return view('customer.dashboard', compact('breadcrumbs', 'sms_incoming', 'sms_outgoing', 'outgoing', 'incoming', 'sms_history'));
    }

}
