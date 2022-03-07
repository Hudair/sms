<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class CustomerBaseController extends Controller
{
    /**
     * Show admin home.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function index()
    {
        return view('customer.dashboard');
    }

    protected function redirectResponse(Request $request, $message, $type = 'success')
    {
        if ($request->wantsJson()) {
            return response()->json([
                    'status'  => $type,
                    'message' => $message,
            ]);
        }

        return redirect()->back()->with("flash_{$type}", $message);
    }

}
