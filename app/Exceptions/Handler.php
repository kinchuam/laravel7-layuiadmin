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
        if($request->is('api/*')){
            $response = [];
            $error = $this->convertExceptionToResponse($exception);
            $response['status'] = $error->getStatusCode();
            $response['msg'] = 'something error';
            if(config('app.debug')) {
                $response['msg'] = empty($exception->getMessage()) ? 'request error' : $exception->getMessage();
                if($error->getStatusCode() >= 500) {
                    if(config('app.debug')) {
                        $response['trace'] = $exception->getTraceAsString();
                        $response['code'] = $exception->getCode();
                    }
                }
            }
            $response['data'] = [];
            return response()->json($response, $error->getStatusCode());
        }else{
            return parent::render($request, $exception);
        }
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
