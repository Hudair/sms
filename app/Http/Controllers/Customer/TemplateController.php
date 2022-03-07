<?php

namespace App\Http\Controllers\Customer;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Templates\StoreTemplate;
use App\Http\Requests\Templates\UpdateTemplate;
use App\Models\Templates;
use App\Models\TemplateTags;
use App\Repositories\Contracts\TemplatesRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TemplateController extends Controller
{

    protected $templates;


    /**
     * TemplateController constructor.
     *
     * @param  TemplatesRepository  $templates
     */

    public function __construct(TemplatesRepository $templates)
    {
        $this->templates = $templates;
    }

    /**
     * view all templates
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */

    public function index()
    {
        $this->authorize('sms_template');

        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('dashboard'), 'name' => __('locale.menu.Sending')],
                ['name' => __('locale.menu.SMS Template')],
        ];

        return view('customer.Templates.index', compact('breadcrumbs'));
    }


    /**
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function search(Request $request)
    {

        $this->authorize('sms_template');

        $columns = [
                0 => 'uid',
                1 => 'name',
                2 => 'message',
                3 => 'status',
                4 => 'uid',
        ];

        $totalData = Templates::where('user_id', Auth::user()->id)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $templates = Templates::where('user_id', Auth::user()->id)->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $templates = Templates::where('user_id', Auth::user()->id)->whereLike(['uid', 'name', 'message'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Templates::where('user_id', Auth::user()->id)->whereLike(['uid', 'name', 'message'], $search)->count();
        }

        $data = [];
        if ( ! empty($templates)) {
            foreach ($templates as $template) {
                $show = route('customer.templates.show', $template->uid);

                if ($template->status === true) {
                    $status = 'checked';
                } else {
                    $status = '';
                }

                if (strlen($template->message) > 100) {
                    $message = str_limit($template->message);
                } else {
                    $message = $template->message;
                }

                $nestedData['uid']     = $template->uid;
                $nestedData['name']    = $template->name;
                $nestedData['message'] = $message;
                $nestedData['status']  = "<div class='custom-control custom-switch switch-lg custom-switch-success'>
                <input type='checkbox' class='custom-control-input get_status' id='status_$template->uid' data-id='$template->uid' name='status' $status>
                <label class='custom-control-label' for='status_$template->uid'>
                  <span class='switch-text-left'>".__('locale.labels.active')."</span>
                  <span class='switch-text-right'>".__('locale.labels.inactive')."</span>
                </label>
              </div>";
                $nestedData['action']  = "<a href='$show' class='text-primary mr-1'><i class='feather us-2x icon-edit'></i></a>
                                         <span class='action-delete text-danger' data-id='$template->uid'><i class='feather us-2x icon-trash'></i></span>";
                $data[]                = $nestedData;

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
     * create new template
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */

    public function create()
    {
        $this->authorize('sms_template');

        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('templates'), 'name' => __('locale.menu.SMS Template')],
                ['name' => __('locale.templates.add_template')],
        ];

        $template_tags = TemplateTags::cursor();

        return view('customer.Templates.create', compact('breadcrumbs', 'template_tags'));
    }


    /**
     * View template for edit
     *
     * @param  Templates  $template
     *
     * @return Application|Factory|View
     *
     * @throws AuthorizationException
     */

    public function show(Templates $template)
    {
        $this->authorize('sms_template');

        $breadcrumbs   = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('templates'), 'name' => __('locale.menu.SMS Template')],
                ['name' => __('locale.templates.update_template')],
        ];
        $template_tags = TemplateTags::cursor();

        return view('customer.Templates.create', compact('breadcrumbs', 'template', 'template_tags'));
    }


    /**
     * store new template
     *
     * @param  StoreTemplate  $request
     *
     * @return RedirectResponse
     */
    public function store(StoreTemplate $request): RedirectResponse
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('customer.templates.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->templates->store($request->input());

        return redirect()->route('customer.templates.index')->with([
                'status'  => 'success',
                'message' => __('locale.templates.template_successfully_added'),
        ]);

    }


    /**
     * update template
     *
     * @param  Templates  $template
     * @param  UpdateTemplate  $request
     *
     * @return RedirectResponse
     */

    public function update(Templates $template, UpdateTemplate $request): RedirectResponse
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('customer.templates.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->templates->update($template, $request->input());

        return redirect()->route('customer.templates.index')->with([
                'status'  => 'success',
                'message' => __('locale.templates.template_successfully_updated'),
        ]);
    }

    /**
     * remove existing template
     *
     * @param  Templates  $template
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Templates $template): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('sms_template');

        $this->templates->destroy($template);

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.templates.template_successfully_deleted'),
        ]);

    }

    /**
     * change template status
     *
     * @param  Templates  $template
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     * @throws GeneralException
     */
    public function activeToggle(Templates $template): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }
        try {
            $this->authorize('sms_template');

            if ($template->update(['status' => ! $template->status])) {
                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.templates.template_successfully_change'),
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

        $this->authorize('sms_template');

        $action = $request->get('action');
        $ids    = $request->get('ids');

        switch ($action) {
            case 'destroy':

                $this->templates->batchDestroy($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.templates.templates_deleted'),
                ]);

            case 'enable':

                $this->templates->batchActive($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.templates.templates_enabled'),
                ]);

            case 'disable':

                $this->templates->batchDisable($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.templates.templates_disabled'),
                ]);
        }

        return response()->json([
                'status'  => 'error',
                'message' => __('locale.exceptions.invalid_action'),
        ]);

    }
}
