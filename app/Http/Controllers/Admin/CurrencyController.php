<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\GeneralException;
use App\Http\Requests\Currency\StoreCurrencyRequest;
use App\Http\Requests\Currency\UpdateCurrencyRequest;
use App\Models\Currency;
use App\Repositories\Contracts\CurrencyRepository;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Generator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CurrencyController extends AdminBaseController
{
    protected $currencies;


    /**
     * CurrencyController constructor.
     *
     * @param  CurrencyRepository  $currencies
     */

    public function __construct(CurrencyRepository $currencies)
    {
        $this->currencies = $currencies;
    }

    /**
     * @return Application|Factory|View
     * @throws AuthorizationException
     */

    public function index()
    {

        $this->authorize('manage currencies');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Plan')],
                ['name' => __('locale.menu.Currencies')],
        ];


        return view('admin.currency.index', compact('breadcrumbs'));
    }


    /**
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function search(Request $request)
    {

        $this->authorize('manage currencies');

        $columns = [
                0 => 'uid',
                1 => 'name',
                2 => 'code',
                3 => 'format',
                4 => 'status',
                5 => 'uid',
        ];

        $totalData = Currency::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $currencies = Currency::offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $currencies = Currency::whereLike(['uid', 'name', 'code', 'format'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Currency::whereLike(['uid', 'name', 'code', 'format'], $search)->count();
        }

        $data = [];
        if ( ! empty($currencies)) {
            foreach ($currencies as $currency) {
                $show = route('admin.currencies.show', $currency->uid);

                if ($currency->status === true) {
                    $status = 'checked';
                } else {
                    $status = '';
                }

                $action = null;

                if (Auth::user()->can('edit currencies')) {
                    $action .= "<a href='$show' class='text-primary mr-1'><i class='feather us-2x icon-edit'></i></a>";
                }

                if (Auth::user()->can('delete currencies')) {
                    $action .= "<span class='action-delete text-danger' data-id='$currency->uid'><i class='feather us-2x icon-trash'></i></span>";
                }


                $nestedData['uid']    = $currency->uid;
                $nestedData['name']   = $currency->name;
                $nestedData['code']   = $currency->code;
                $nestedData['format'] = $currency->format;
                $nestedData['status'] = "<div class='custom-control custom-switch switch-lg custom-switch-success'>
                <input type='checkbox' class='custom-control-input get_status' id='status_$currency->uid' data-id='$currency->uid' name='status' $status>
                <label class='custom-control-label' for='status_$currency->uid'>
                  <span class='switch-text-left'>".__('locale.labels.active')."</span>
                  <span class='switch-text-right'>".__('locale.labels.inactive')."</span>
                </label>
              </div>";
                $nestedData['action'] = $action;
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
        $this->authorize('create currencies');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/currencies"), 'name' => __('locale.menu.Currencies')],
                ['name' => __('locale.currencies.add_new_currency')],
        ];

        return view('admin.currency.create', compact('breadcrumbs'));
    }


    /**
     * View currency for edit
     *
     * @param  Currency  $currency
     *
     * @return Application|Factory|View
     *
     * @throws AuthorizationException
     */

    public function show(Currency $currency)
    {
        $this->authorize('edit currencies');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/currencies"), 'name' => __('locale.menu.Currencies')],
                ['name' => __('locale.currencies.update_currency')],
        ];

        return view('admin.currency.create', compact('breadcrumbs', 'currency'));
    }


    /**
     * @param  StoreCurrencyRequest  $request
     *
     * @return RedirectResponse
     */

    public function store(StoreCurrencyRequest $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.currencies.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->currencies->store($request->input());

        return redirect()->route('admin.currencies.index')->with([
                'status'  => 'success',
                'message' => __('locale.currencies.currency_successfully_added'),
        ]);

    }


    /**
     * @param  Currency  $currency
     * @param  UpdateCurrencyRequest  $request
     *
     * @return RedirectResponse
     */

    public function update(Currency $currency, UpdateCurrencyRequest $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.currencies.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->currencies->update($currency, $request->input());

        return redirect()->route('admin.currencies.index')->with([
                'status'  => 'success',
                'message' => __('locale.currencies.currency_successfully_updated'),
        ]);
    }

    /**
     * @param  Currency  $currency
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function destroy(Currency $currency): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }
        $this->authorize('delete currencies');

        $this->currencies->destroy($currency);

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.currencies.currency_successfully_deleted'),
        ]);

    }

    /**
     * change currency status
     *
     * @param  Currency  $currency
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     * @throws GeneralException
     */
    public function activeToggle(Currency $currency): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        try {
            $this->authorize('edit currencies');

            if ($currency->update(['status' => ! $currency->status])) {
                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.currencies.currency_successfully_change'),
                ]);
            }

            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                    'status'  => 'error',
                    'message' => $exception->getMessage(),
            ]);
        }


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
                $this->authorize('delete currencies');

                $this->currencies->batchDestroy($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.currencies.currencies_deleted'),
                ]);

            case 'enable':
                $this->authorize('edit currencies');

                $this->currencies->batchActive($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.currencies.currencies_enabled'),
                ]);

            case 'disable':

                $this->authorize('edit currencies');

                $this->currencies->batchDisable($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.currencies.currencies_disabled'),
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

    public function currencyGenerator(): Generator
    {
        foreach (Currency::cursor() as $currency) {
            yield $currency;
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
            return redirect()->route('admin.currencies.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('manage currencies');

        $file_name = (new FastExcel($this->currencyGenerator()))->export(storage_path('Currency_'.time().'.xlsx'));

        return response()->download($file_name);
    }

}
