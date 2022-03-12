<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\GeneralException;

use App\Helpers\Helper;
use App\Http\Requests\Settings\StoreCountryRequest;
use App\Models\Country;
use App\Models\PlansCoverageCountries;
use App\Repositories\Contracts\CountriesRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class CountriesController extends AdminBaseController
{
    protected $countries;

    /**
     * CountriesController constructor.
     *
     * @param  CountriesRepository  $countries
     */

    public function __construct(CountriesRepository $countries)
    {
        $this->countries = $countries;
    }


    /**
     * view all active languages
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('general settings');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Settings')],
                ['name' => __('locale.menu.Countries')],
        ];

        return \view('admin.settings.Countries.index', compact('breadcrumbs'));
    }

    /**
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function search(Request $request)
    {

        $this->authorize('general settings');

        $columns = [
                0 => 'responsive_id',
                1 => 'uid',
                2 => 'uid',
                3 => 'name',
                4 => 'iso_code',
                5 => 'country_code',
                6 => 'status',
                7 => 'actions',
        ];

        $totalData = Country::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $countries = Country::offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $countries = Country::whereLike(['uid', 'name', 'iso_code', 'country_code'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Country::whereLike(['uid', 'name', 'iso_code', 'country_code'], $search)->count();
        }

        $data = [];
        if ( ! empty($countries)) {
            foreach ($countries as $country) {

                if ($country->status === true) {
                    $status = 'checked';
                } else {
                    $status = '';
                }

                $nestedData['responsive_id'] = '';
                $nestedData['uid']           = $country->uid;
                $nestedData['name']          = $country->name;
                $nestedData['country_code']  = $country->country_code;
                $nestedData['iso_code']      = $country->iso_code;
                $nestedData['status']        = "<div class='form-check form-switch form-check-primary'>
                <input type='checkbox' class='form-check-input get_status' id='status_$country->uid' data-id='$country->uid' name='status' $status>
                <label class='form-check-label' for='status_$country->uid'>
                  <span class='switch-icon-left'><i data-feather='check'></i> </span>
                  <span class='switch-icon-right'><i data-feather='x'></i> </span>
                </label>
              </div>";
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
     * add new language
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function create()
    {
        $this->authorize('general settings');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/countries"), 'name' => __('locale.menu.Countries')],
                ['name' => __('locale.settings.add_new')],
        ];

        $countries = Helper::countries();

        return \view('admin.settings.Countries.new', compact('breadcrumbs', 'countries'));
    }


    /**
     * store new language
     *
     * @param  StoreCountryRequest  $request
     *
     * @return RedirectResponse
     */
    public function store(StoreCountryRequest $request): RedirectResponse
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('admin.languages.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->countries->store($request->input());

        return redirect()->route('admin.countries.index')->with([
                'status'  => 'success',
                'message' => __('locale.settings.successfully_added'),
        ]);

    }

    /**
     *
     * change status
     *
     * @param  Country  $country
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws GeneralException
     */
    public function activeToggle(Country $country): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        try {

            $this->authorize('general settings');

            if ($country->update(['status' => ! $country->status])) {

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.settings.status_successfully_change'),
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
     * delete language
     *
     * @param  Country  $country
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Country $country): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->authorize('general settings');

        PlansCoverageCountries::where('country_id', $country->id)->delete();

        $this->countries->destroy($country);

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.settings.successfully_deleted'),
        ]);
    }

}
