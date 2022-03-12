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
                0  => 'responsive_id',
                1  => 'uid',
                2  => 'uid',
                3  => 'created_at',
                4  => 'id',
                5  => 'type',
                6  => 'description',
                7  => 'amount',
                8  => 'status',
                10 => 'actions',
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
                $invoice_number   = "<a href='$show' class='text-primary fw-bold'>#$invoice->id</a>";

                $nestedData['responsive_id'] = '';
                $nestedData['uid']           = $invoice->uid;
                $nestedData['id']            = $invoice_number;
                $nestedData['created_at']    = Tool::customerDateTime($invoice->created_at);
                $nestedData['type']          = strtoupper($invoice->type);
                $nestedData['description']   = str_limit($invoice->description, 35);
                $nestedData['amount']        = Tool::format_price($invoice->amount, $invoice->currency->format);
                $nestedData['status']        = $invoice->getStatus();
                $nestedData['edit']          = $show;

                $data[] = $nestedData;

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

    public function print(Invoices $invoice)
    {

        $pageConfigs = ['pageHeader' => false];

        return view('customer.Accounts.print', compact('invoice', 'pageConfigs'));
    }


}
