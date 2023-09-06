<?php

namespace App\Exceptions;

use Throwable;
use Psr\Log\LogLevel;
use Illuminate\Http\Response;
use InvalidArgumentException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

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
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof  BadRequestException) {
            return response(['status' => 4, 'message' => 'Invalid request to firestore'], Response::HTTP_BAD_REQUEST);
        }
        
        if ($exception instanceof  UnauthorizedException) {
            return response(['status' => false, 'message' => 'User does not have the right permissions.'], Response::HTTP_UNAUTHORIZED);
        }

        // if ($exception instanceof TokenBlacklistedException) {
        //     return response(['status' => 3, 'message' => 'Token can not be used, get new one'], Response::HTTP_OK);
        // }
        // if ($exception instanceof TokenInvalidException) {
        //     return response(['status' => 3, 'message' => 'Token is invalid'], Response::HTTP_OK);
        // }
        // if ($exception instanceof TokenExpiredException) {
        //     return response(['status' => 3, 'message' => 'Token is expired'], Response::HTTP_OK);
        // }
        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }
        if ($exception instanceof AuthorizationException) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
        if ($exception instanceof UnauthorizedException) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
        if ($exception instanceof ModelNotFoundException) {
            // $modelName = strtolower(class_basename($exception->getModel()));
            $modelName = class_basename($exception->getModel());
            $message = "Cannot find {$modelName}";
            
            return response()->json([
                'status' => false,
                'message' => $message,
            ], Response::HTTP_NOT_FOUND);
        }
        if ($exception instanceof NotFoundHttpException) {
            if (str_contains(request()->url(), 'api')) {
                return response()->json(['status' => false, 'message' => 'Incorect route'], Response::HTTP_NOT_FOUND);
            }
        }
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json(['status' => false, 'message' => 'The specified method for the request is invalid'], Response::HTTP_METHOD_NOT_ALLOWED);
        }
        if ($exception instanceof ThrottleRequestsException) {
            return response()->json(['status' => false, 'message' => 'Too many attempt'], Response::HTTP_TOO_MANY_REQUESTS);
        }
       
        if ($exception instanceof InvalidArgumentException) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
     
        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($this->isFrontend($request)) {
            return redirect()->guest('login');
        }
        return response()->json(['status' => 3, 'message' => 'Unauthenticated'], Response::HTTP_UNAUTHORIZED);
    }

    private function isFrontend($request)
    {
        return $request->acceptsHtml() && collect($request->route()->middleware())
            ->contains('web');
    }
}
