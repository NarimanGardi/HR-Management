<?php

namespace App\Exceptions;

use ErrorException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
class Handler extends ExceptionHandler
{
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
        HttpResponseException::class,
        AuthorizationException::class,
        AuthenticationException::class,
        ValidationException::class,
        ModelNotFoundException::class,
        NotFoundHttpException::class,
        QueryException::class,
        ThrottleRequestsException::class,
        ErrorException::class,
        \Exception::class,

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
     */
    public function render($request, $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        if ($exception instanceof ValidationException) {
            $errors = $exception->validator->errors()->getMessages();

            return response()->json([
                'errors' => $errors,
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($exception instanceof AuthenticationException) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if ($exception instanceof NotFoundHttpException) {
            return response()->json(['error' => 'Endpoint not found'], 404);
        }

        if ($exception instanceof QueryException) {
            return response()->json(['error' => 'Database error'], 500);
        }

        if ($exception instanceof ThrottleRequestsException) {
            return response()->json(['error' => 'Too many requests'], 429);
        }

        if ($exception instanceof ErrorException) {
            return response()->json(['error' => 'Internal server error'], 500);
        }

        if ($exception instanceof AuthorizationException) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if($exception instanceof \Exception) {
            return response()->json(['error' => 'Something went wrong'], 400);
        }

        return parent::render($request, $exception);
    }
}
