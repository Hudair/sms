<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Library\Tool;
use App\Models\Invoices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{

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
        ];

        $totalData = Invoices::where('user_id', Auth::user()->id)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $invoices = Invoices::where('user_id', Auth::user()->id)->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $invoices = Invoices::where('user_id', Auth::user()->id)->whereLike(['uid', 'type', 'created_at', 'description', 'amount', 'status'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Invoices::where('user_id', Auth::user()->id)->whereLike(['uid', 'type', 'created_at', 'description', 'amount', 'status'], $search)->count();

        }

        $data = [];
        if ( ! empty($invoices)) {
            foreach ($invoices as $invoice) {

                $show = route('customer.invoices.view', $invoice->uid);
                $view = __('locale.labels.view');

                $nestedData['uid']         = $invoice->uid;
                $nestedData['created_at']  = Tool::customerDateTime($invoice->created_at);
                $nestedData['type']        = strtoupper($invoice->type);
                $nestedData['description'] = str_limit($invoice->description, 35);
                $nestedData['amount']      = Tool::format_price($invoice->amount, $invoice->currency->format);
                $nestedData['status']      = $invoice->getStatus();
                $nestedData['action']      = "<a href='$show' class='text-primary' data-toggle='tooltip' data-placement='top' title='$view'><i class='feather us-2x icon-eye'></i></a>";
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
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('subscriptions'), 'name' => __('locale.labels.billing')],
                ['name' => __('locale.labels.invoice')],
        ];

        return view('customer.Accounts.invoice', compact('breadcrumbs', 'invoice'));
    }

}
