<?php

namespace App\Exceptions;

use App\Supports\Traits\ResponseStatus;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ResponseStatus;
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * render
     *
     * @param \Illuminate\Http\Request $request
     * @param Throwable $e
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        $message = $e->getMessage();
        if ($e instanceof AuthenticationException) {
            return $this->responseStatusFailed(401, 'Unauthorized');
        } else if ($e instanceof ModelNotFoundException) {
            $message = $message ?? $e->getMessage();
            return $this->responseStatusFailed(404, $message);
        } else if ($e instanceof NotFoundHttpException) {
            $message = $message ?? 'Api not found';
            return $this->responseStatusFailed(404, $message);
        } else if ($e instanceof MethodNotAllowedHttpException) {
            $message = $message ?? 'Method not support';
            return $this->responseStatusFailed(405, $message);
        }
        return parent::render($request, $e);
    }
}
