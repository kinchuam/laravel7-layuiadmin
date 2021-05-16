<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
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
        return $request->expectsJson()
            ? $this->AjaxResponse($exception)
            : parent::render($request, $exception);
    }

    protected function AjaxResponse($exception): \Illuminate\Http\JsonResponse
    {
        $response = [
            "status" => 'error',
            "code" => 0,
            "message" => 'something error',
            "data" => (object) [],
        ];
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException) {
            $response["code"] = $exception->getStatusCode();
            $response["message"] = $exception->getMessage();
            return response()->json($response);
        }
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $response["code"] = 422;
            $response["message"] = $exception->validator->errors()->first();
            return response()->json($response);
        }
        $error = $this->convertExceptionToResponse($exception);
        $response["code"] = $error->getStatusCode();
        if(config('app.debug')) {
            $response["message"] = empty($exception->getMessage()) ? 'something error' : $exception->getMessage();
            if($response["code"] >= 500) {
                $response["code"] = $exception->getCode();
                //$response["error"] = $exception->getTraceAsString();
            }
        }
        return response()->json($response);
    }

    /**
     * 登录过期后的跳转地址
     * @param \Illuminate\Http\Request $request
     * @param AuthenticationException $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $exception->getMessage()], 401)
            : redirect()->guest(route('admin.login'));
    }
}
