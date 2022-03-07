<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
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

        return view('customer.Developers.settings', compact('breadcrumbs'));
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
}
