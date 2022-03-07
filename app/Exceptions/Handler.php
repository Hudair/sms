<?php

namespace App\Exceptions;

use Exception;
use Facade\Ignition\Exceptions\ViewException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
            AuthenticationException::class,
            AuthorizationException::class,
            HttpException::class,
            ModelNotFoundException::class,
            ValidationException::class,
            TokenMismatchException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
            'password',
            'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  Throwable  $exception
     *
     * @return void
     *
     * @throws Exception
     * @throws Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param  Throwable  $exception
     *
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {

        if ($request->wantsJson()) {
            return \response()->json([
                    'status'  => 'error',
                    'message' => $exception->getMessage(),
            ]);
        }

        if (config('app.env') != 'local') {
            if ($exception instanceof ViewException || $exception instanceof ModelNotFoundException) {
                return \response()->view('errors.500', compact('exception'), 500);
            }
            if ($exception instanceof AuthenticationException || $exception instanceof AuthorizationException) {
                return \response()->view('errors.401', compact('exception'), 401);
            }

            if ($exception instanceof TokenMismatchException) {
                return \response()->view('errors.419', compact('exception'), 419);
            }

            if ($exception instanceof HttpException) {
                return \response()->view('errors.404', compact('exception'), 404);
            }
        }

        return parent::render($request, $exception);
    }
}
