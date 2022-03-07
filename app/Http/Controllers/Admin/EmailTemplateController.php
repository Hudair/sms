<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\GeneralException;
use App\Http\Requests\Settings\EmailTemplateRequest;
use App\Models\EmailTemplates;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmailTemplateController extends AdminBaseController
{

    /**
     * view all email templates
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('view email_templates');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Settings')],
                ['name' => __('locale.menu.Email Templates')],
        ];

        $email_templates = EmailTemplates::all();

        return \view('admin.settings.EmailTemplates.index', compact('email_templates', 'breadcrumbs'));
    }


    /**
     * manage payment gateway
     *
     * @param  EmailTemplates  $template
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function show(EmailTemplates $template)
    {
        $this->authorize('update payment_gateways');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/email-templates"), 'name' => __('locale.menu.Email Templates')],
                ['name' => $template->name],
        ];

        return \view('admin.settings.EmailTemplates.show', compact('template', 'breadcrumbs'));
    }


    /**
     *
     * change status
     *
     * @param  EmailTemplates  $template
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws GeneralException
     */
    public function activeToggle(EmailTemplates $template): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        try {

            $this->authorize('view email_templates');

            if ($template->update(['status' => ! $template->status])) {
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
     * update email template
     *
     * @param  EmailTemplates  $email_template
     * @param  EmailTemplateRequest  $request
     *
     * @return RedirectResponse
     */

    public function update(EmailTemplates $email_template, EmailTemplateRequest $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.email-templates.show', $email_template->uid)->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $email_template->update([
                'subject' => $request->input('subject'),
                'content' => $request->input('content'),
        ]);

        return redirect()->route('admin.email-templates.show', $email_template->uid)->with([
                'status'  => 'success',
                'message' => __('locale.email_template.template_was_updated'),
        ]);

    }


}
