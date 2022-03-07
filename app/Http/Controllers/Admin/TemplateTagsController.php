<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\GeneralException;
use App\Http\Requests\TemplateTags\StoreTag;
use App\Http\Requests\TemplateTags\UpdateTag;
use App\Models\TemplateTags;
use App\Repositories\Contracts\TemplateTagsRepository;
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
use Illuminate\View\View;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TemplateTagsController extends AdminBaseController
{

    protected $template_tags;

    /**
     * TemplateTagsController constructor.
     *
     * @param  TemplateTagsRepository  $template_tags
     */

    public function __construct(TemplateTagsRepository $template_tags)
    {
        $this->template_tags = $template_tags;
    }

    /**
     * @return Application|Factory|View
     * @throws AuthorizationException
     */

    public function index()
    {

        $this->authorize('view tags');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Sending')],
                ['name' => __('locale.menu.Template Tags')],
        ];

        return view('admin.TemplateTags.index', compact('breadcrumbs'));
    }


    /**
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function search(Request $request)
    {

        $this->authorize('view tags');

        $columns = [
                0 => 'uid',
                1 => 'name',
                2 => 'tag',
                3 => 'type',
                4 => 'required',
                5 => 'uid',
        ];

        $totalData = TemplateTags::count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $template_tags = TemplateTags::offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $template_tags = TemplateTags::whereLike(['uid', 'name', 'tag', 'type'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = TemplateTags::whereLike(['uid', 'name', 'tag', 'type'], $search)->count();

        }

        $data = [];
        if ( ! empty($template_tags)) {
            foreach ($template_tags as $tags) {
                $show = route('admin.tags.show', $tags->uid);

                if ($tags->required === 1) {
                    $required = 'checked';
                } else {
                    $required = '';
                }

                $nestedData['uid']      = $tags->uid;
                $nestedData['name']     = $tags->name;
                $nestedData['tag']      = $tags->tag;
                $nestedData['type']     = $tags->type;
                $nestedData['required'] = "<div class='custom-control custom-switch switch-md custom-switch-success'>
                <input type='checkbox' class='custom-control-input get_required' id='required_$tags->uid' data-id='$tags->uid' name='status' $required>
                <label class='custom-control-label' for='required_$tags->uid'>
                  <span class='switch-text-left'>".__('locale.labels.yes')."</span>
                  <span class='switch-text-right'>".__('locale.labels.no')."</span>
                </label>
              </div>";
                $nestedData['action']   = "<a href='$show' class='text-primary mr-1'><i class='feather us-2x icon-edit'></i></a>
                                         <span class='action-delete text-danger' data-id='$tags->uid'><i class='feather us-2x icon-trash'></i></span>";
                $data[]                 = $nestedData;

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
        $this->authorize('create tags');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/tags"), 'name' => __('locale.menu.Template Tags')],
                ['name' => __('locale.template_tags.new_template_tag')],
        ];

        return view('admin.TemplateTags.create', compact('breadcrumbs'));
    }


    /**
     * View sender id for edit
     *
     * @param  TemplateTags  $tag
     *
     * @return Application|Factory|View
     *
     * @throws AuthorizationException
     */

    public function show(TemplateTags $tag)
    {
        $this->authorize('edit tags');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/tags"), 'name' => __('locale.menu.Template Tags')],
                ['name' => __('locale.template_tags.update_template_tag')],
        ];

        return view('admin.TemplateTags.create', compact('breadcrumbs', 'tag'));
    }


    /**
     * @param  StoreTag  $request
     *
     * @return RedirectResponse
     */

    public function store(StoreTag $request): RedirectResponse
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('admin.tags.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $input          = $request->input();
        $tag            = strtolower(str_replace([" ", '-'], '_', $input['name']));
        $available_tags = ['email', 'username', 'company', 'first_name', 'last_name', 'birth_date', 'anniversary_date', 'address'];

        if (in_array($tag, $available_tags)) {
            return redirect()->route('admin.tags.create')->with([
                    'status'  => 'error',
                    'message' => __('locale.template_tags.template_tag_available', ['template_tag' => $tag]),
            ]);
        }

        $input['tag'] = $tag;

        $this->template_tags->store($input);

        return redirect()->route('admin.tags.index')->with([
                'status'  => 'success',
                'message' => __('locale.template_tags.template_tag_successfully_added'),
        ]);

    }


    /**
     * @param  TemplateTags  $tag
     * @param  UpdateTag  $request
     *
     * @return RedirectResponse
     */

    public function update(TemplateTags $tag, UpdateTag $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.tags.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->template_tags->update($tag, $request->input());

        return redirect()->route('admin.tags.index')->with([
                'status'  => 'success',
                'message' => __('locale.template_tags.template_tag_successfully_updated'),
        ]);
    }


    /**
     * change sender id status
     *
     * @param  TemplateTags  $tag
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     * @throws GeneralException
     */
    public function activeToggle(TemplateTags $tag): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        try {
            $this->authorize('edit tags');

            if ($tag->update(['required' => ! $tag->required])) {
                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.template_tags.template_tag_successfully_change'),
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
     * @param  TemplateTags  $tag
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function destroy(TemplateTags $tag): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $this->authorize('delete tags');

        $this->template_tags->destroy($tag);

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.template_tags.template_tag_successfully_deleted'),
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
                $this->authorize('delete tags');

                $this->template_tags->batchDestroy($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.template_tags.template_tags_deleted'),
                ]);

            case 'required':
                $this->authorize('edit tags');

                $this->template_tags->batchRequired($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.template_tags.template_tags_required'),
                ]);

            case 'optional':

                $this->authorize('edit tags');

                $this->template_tags->batchOptional($ids);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.template_tags.template_tags_optional'),
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
    public function templateTagsGenerator(): Generator
    {
        foreach (TemplateTags::cursor() as $tags) {
            yield $tags;
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
            return redirect()->route('admin.tags.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $this->authorize('view tags');

        $file_name = (new FastExcel($this->templateTagsGenerator()))->export(storage_path('TemplateTags_'.time().'.xlsx'));

        return response()->download($file_name);
    }

}
