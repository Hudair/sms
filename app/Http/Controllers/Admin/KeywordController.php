<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Keywords\StoreKeywordsRequest;
use App\Http\Requests\Keywords\UpdateKeywordsRequest;
use App\Library\Tool;
use App\Models\Currency;
use App\Models\Keywords;
use App\Models\User;
use App\Repositories\Contracts\KeywordRepository;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Generator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class KeywordController extends AdminBaseController
{

    protected $keywords;


    /**
     * KeywordController constructor.
     *
     * @param  KeywordRepository  $keywords
     */

    public function __construct(KeywordRepository $keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @return Application|Factory|View
     * @throws AuthorizationException
     */

    public function index()
    {

        $this->authorize('view keywords');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Sending')],
                ['name' => __('locale.menu.Keywords')],
        ];


        return view('admin.keywords.index', compact('breadcrumbs'));
    }


    /**
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function search(Request $request)
    {

        $this->authorize('view keywords');

        $columns = [
                0 => 'uid',
                1 => 'title',
                2 => 'keyword_name',
                3 => 'user_id',
                4 => 'price',
                5 => 'status',
                6 => 'uid',
        ];

        $totalData = Keywords::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $keywords = Keywords::offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $keywords = Keywords::whereLike(['uid', 'title', 'keyword_name', 'user.first_name', 'user.last_name'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Keywords::whereLike(['uid', 'title', 'keyword_name', 'user.first_name', 'user.last_name'], $search)->count();
        }

        $data = [];
        if ( ! empty($keywords)) {
            foreach ($keywords as $keyword) {
                $show = route('admin.keywords.show', $keyword->uid);

                $edit       = __('locale.buttons.edit');
                $delete     = __('locale.buttons.delete');
                $remove_mms = __('locale.buttons.remove_mms');


                if ($keyword->user->is_admin) {
                    $assign_to = $keyword->user->displayName();
                } else {

                    $customer_profile = route('admin.customers.show', $keyword->user->uid);
                    $customer_name    = $keyword->user->displayName();
                    $assign_to        = "<a href='$customer_profile' class='text-primary mr-1'>$customer_name</a>";
                }

                if ($keyword->status == 'available') {
                    $status = '<div class="chip chip-primary"> <div class="chip-body"><div class="chip-text text-uppercase">'.__('locale.labels.available').'</div></div></div>';
                } elseif ($keyword->status == 'assigned') {
                    $status = '<div class="chip chip-success"> <div class="chip-body"><div class="chip-text text-uppercase">'.__('locale.labels.assigned').'</div></div></div>';
                } else {
                    $status = '<div class="chip chip-danger"> <div class="chip-body"><div class="chip-text text-uppercase">'.__('locale.labels.expired').'</div></div></div>';
                }

                $action_url = '';

                if ($keyword->reply_mms) {
                    $action_url .= "<span class='action-remove-sms text-warning mr-1' data-id='$keyword->uid' data-toggle='tooltip' data-placement='top' title='$remove_mms'><i class='feather us-2x icon-delete'></i></span>";
                }

                $action_url .= "<a href='$show' class='text-primary mr-1' data-toggle='tooltip' data-placement='top' title='$edit'><i class='feather us-2x icon-edit' ></i></a>
                                <span class='action-delete text-danger' data-id='$keyword->uid'  data-toggle='tooltip' data-placement='top' title='$delete'><i class='feather us-2x icon-trash'></i></span>";

                $nestedData['uid']          = $keyword->uid;
                $nestedData['title']        = $keyword->title;
                $nestedData['keyword_name'] = $keyword->keyword_name;
                $nestedData['user_id']      = $assign_to;
                $nestedData['price']        = "<div>
                                                        <p class='text-bold-600'>".Tool::format_price($keyword->price, $keyword->currency->format)." </p>
                                                        <p class='text-muted'>".$keyword->displayFrequencyTime()."</p>
                                                   </div>";
                $nestedData['status']       = $status;
                $nestedData['action']       = $action_url;
                $data[]                     = $nestedData;

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
        $this->authorize('create keywords');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/keywords"), 'name' => __('locale.menu.Keywords')],
                ['name' => __('locale.keywords.create_new_keyword')],
        ];

        $customers  = User::where('status', true)->get();
        $currencies = Currency::where('status', true)->get();

        return view('admin.keywords.create', compact('breadcrumbs', 'customers', 'currencies'));
    }

    /**
     * @param  StoreKeywordsRequest  $request
     *
     * @param  Keywords  $keyword
     *
     * @return RedirectResponse
     */

    public function store(StoreKeywordsRequest $request, Keywords $keyword): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.keywords.create')->withInput($request->except('_token'))->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->keywords->store($request->except('_token'), $keyword::billingCycleValues());

        return redirect()->route('admin.keywords.index')->with([
                'status'  => 'success',
                'message' => __('locale.keywords.keyword_successfully_added'),
        ]);

    }


    /**
     * View currency for edit
     *
     * @param  Keywords  $keyword
     *
     * @return Application|Factory|View
     *
     * @throws AuthorizationException
     */

    public function show(Keywords $keyword)
    {
        $this->authorize('edit keywords');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/keywords"), 'name' => __('locale.menu.Keywords')],
                ['name' => __('locale.keywords.update_keyword')],
        ];
        $customers   = User::where('status', true)->get();
        $currencies  = Currency::where('status', true)->get();

        return view('admin.keywords.create', compact('breadcrumbs', 'keyword', 'customers', 'currencies'));
    }


    /**
     * @param  Keywords  $keyword
     * @param  UpdateKeywordsRequest  $request
     *
     * @return RedirectResponse
     */

    public function update(Keywords $keyword, UpdateKeywordsRequest $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.keywords.show', $keyword->uid)->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->keywords->update($keyword, $request->input(), $keyword::billingCycleValues());

        return redirect()->route('admin.keywords.show', $keyword->uid)->with([
                'status'  => 'success',
                'message' => __('locale.keywords.keyword_successfully_updated'),
        ]);
    }

    /**
     * remove mms file
     *
     * @param  Keywords  $keyword
     *
     * @return JsonResponse
     */

    public function removeMMS(Keywords $keyword): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        if ( ! $keyword->update(['reply_mms' => null])) {
            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.exceptions.something_went_wrong'),
            ]);
        }

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.keywords.keyword_mms_file_removed'),
        ]);
    }


    /**
     * @param  Keywords  $keyword
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function destroy(Keywords $keyword): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('delete keywords');

        $this->keywords->destroy($keyword);

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.keywords.keyword_successfully_deleted'),
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
                $this->authorize('delete keywords');

                $this->keywords->batchDestroy($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.keywords.keywords_deleted'),
                ]);

            case 'available':
                $this->authorize('edit keywords');

                $this->keywords->batchAvailable($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.keywords.available_keywords'),
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

    public function keywordGenerator(): Generator
    {
        foreach (Keywords::cursor() as $keyword) {
            yield $keyword;
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
            return redirect()->route('admin.keywords.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('view keywords');

        $file_name = (new FastExcel($this->keywordGenerator()))->export(storage_path('Keyword_'.time().'.xlsx'));

        return response()->download($file_name);
    }

}
