<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\PlansSendingServer;
use App\Models\SendingServer;
use App\Models\User;
use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeveloperController extends Controller
{

    /**
     * view developer inform
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */

    public function settings()
    {
        $this->authorize('developers');

        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['name' => __('locale.menu.Developers')],
        ];

        $plan_id = Auth::user()->customer->activeSubscription()->plan_id;

        // Check the customer has permissions using sending servers and has his own sending servers
        if (Auth::user()->customer->getOption('create_sending_server') == 'yes') {
            if (PlansSendingServer::where('plan_id', $plan_id)->count()) {

                $sending_server = SendingServer::where('user_id', Auth::user()->id)->where('status', true)->get();

                if ($sending_server->count() == 0) {
                    $sending_server_ids = PlansSendingServer::where('plan_id', $plan_id)->pluck('sending_server_id')->toArray();
                    $sending_server     = SendingServer::where('status', true)->whereIn('id', $sending_server_ids)->get();
                }
            } else {
                $sending_server_ids = PlansSendingServer::where('plan_id', $plan_id)->pluck('sending_server_id')->toArray();
                $sending_server     = SendingServer::where('status', true)->whereIn('id', $sending_server_ids)->get();
            }
        } else {
            // If customer don't have permission creating sending servers
            $sending_server_ids = PlansSendingServer::where('plan_id', $plan_id)->pluck('sending_server_id')->toArray();
            $sending_server     = SendingServer::where('status', true)->whereIn('id', $sending_server_ids)->get();
        }

        return view('customer.Developers.settings', compact('breadcrumbs', 'sending_server'));
    }

    /**
     * generate new token
     *
     * @return JsonResponse
     */
    public function generate(): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $user        = Auth::user();
        $permissions = json_decode($user->customer->permissions, true);

        $token = $user->createToken($user->email, $permissions)->plainTextToken;

        $user->update([
                'api_token' => $token,
        ]);

        return response()->json([
                'status'  => 'success',
                'token'   => $token,
                'message' => __('locale.customer.token_successfully_regenerate'),
        ]);
    }

    public function docs()
    {
        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('developers/settings'), 'name' => __('locale.menu.Developers')],
                ['name' => __('locale.developers.api_documents')],
        ];

        return view('customer.Developers.documentation', compact('breadcrumbs'));
    }

    public function sendingServer(Request $request)
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('customer.developer.settings')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $status = Auth::user()->update([
                'api_sending_server' => $request->sending_server,
        ]);

        if ($status) {
            return redirect()->route('customer.developer.settings')->with([
                    'status'  => 'success',
                    'message' => __('locale.settings.settings_successfully_updated'),
            ]);
        }

        return redirect()->route('customer.developer.settings')->with([
                'status'  => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
        ]);
    }
}
