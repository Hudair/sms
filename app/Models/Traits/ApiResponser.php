<?php


namespace App\Models\Traits;


use Illuminate\Http\JsonResponse;

trait ApiResponser
{

    /**
     * Return a success JSON response.
     *
     * @param  array|string  $data
     * @param  string|null  $message
     * @param  int|null  $code
     *
     * @return JsonResponse
     */
    protected function success($data, string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
                'status'  => 'success',
                'message' => $message,
                'data'    => $data,
        ], $code);
    }


    /**
     * Return an error JSON response.
     *
     * @param  string|null  $message
     * @param  int  $code
     * @param  null  $data
     *
     * @return JsonResponse
     */
    protected function error(string $message = null, int $code, $data = null): JsonResponse
    {
        return response()->json([
                'status'  => 'error',
                'message' => $message,
                'data'    => $data,
        ], $code);
    }
}
