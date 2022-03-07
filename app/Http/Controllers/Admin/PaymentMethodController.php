<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\GeneralException;
use App\Http\Requests\Settings\UpdatePaymentMethods;
use App\Models\PaymentMethods;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentMethodController extends AdminBaseController
{
    /**
     * view all payment gateways
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('view payment_gateways');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Settings')],
                ['name' => __('locale.menu.Payment Gateways')],
        ];

        $payment_gateways = PaymentMethods::all();

        return \view('admin.settings.PaymentMethods.index', compact('payment_gateways', 'breadcrumbs'));
    }


    /**
     *
     * change status
     *
     * @param  PaymentMethods  $gateway
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws GeneralException
     */
    public function activeToggle(PaymentMethods $gateway): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        try {

            $this->authorize('view payment_gateways');

            if ($gateway->update(['status' => ! $gateway->status])) {
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
     * manage payment gateway
     *
     * @param  PaymentMethods  $gateway
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function show(PaymentMethods $gateway)
    {
        $this->authorize('update payment_gateways');

        $breadcrumbs = [
                ['link' => url(config('app.admin_path')."/dashboard"), 'name' => __('locale.menu.Dashboard')],
                ['link' => url(config('app.admin_path')."/payment-gateways"), 'name' => __('locale.menu.Payment Gateways')],
                ['name' => $gateway->name],
        ];

        return \view('admin.settings.PaymentMethods.show', compact('gateway', 'breadcrumbs'));
    }


    /**
     * update payment gateway information
     *
     * @param  PaymentMethods  $payment_gateway
     * @param  UpdatePaymentMethods  $request
     *
     * @return RedirectResponse
     * @throws GeneralException
     */
    public function update(PaymentMethods $payment_gateway, UpdatePaymentMethods $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('admin.payment-gateways.show', $payment_gateway->uid)->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $options = $request->except('_token', '_method', 'name', 'type');

        $payment_gateway->name    = $request->name;
        $payment_gateway->options = json_encode($options);

        if ( ! $payment_gateway->save()) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return redirect()->route('admin.payment-gateways.show', $payment_gateway->uid)->with([
                'status'  => 'success',
                'message' => __('locale.payment_gateways.gateway_was_updated'),
        ]);

    }

}
