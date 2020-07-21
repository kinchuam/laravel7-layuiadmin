<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class LogOperation
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->shouldLogOperation($request)){

            $agent = new Agent();
            $ip = $request->getClientIp();
            $ipData = geoip($ip);
            $log = [
                'path'    => substr($request->path(), 0, 255),
                'method'  => $request->method(),
                'ip'      => $ip,
                'input'   => json_encode($request->input()),
                'agent'   => $agent->getUserAgent()??$_SERVER['HTTP_USER_AGENT'],
                'platform' => $agent->platform()??'',
                'browser' => $agent->browser()??'',
                'ipData' => json_encode($ipData['attributes']),
            ];

            try {
                \App\Models\AccessLog::create($log);
            } catch (\Exception $exception) {
                // pass
                Log::channel('accessLog')->error($exception->getMessage());
            }
        }

        return $next($request);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function shouldLogOperation(Request $request)
    {
        return config('custom.operation_log.enable')
            && !$this->inExceptArray($request)
            && $this->inAllowedMethods($request->method());
    }


    protected function inAllowedMethods($method)
    {
        $allowedMethods = collect(config('custom.operation_log.allowed_methods'))->filter();

        if ($allowedMethods->isEmpty()) {
            return true;
        }

        return $allowedMethods->map(function ($method) {
            return strtoupper($method);
        })->contains($method);
    }

    protected function inExceptArray($request)
    {
        foreach (config('custom.operation_log.except') as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            $methods = [];

            if (Str::contains($except, ':')) {
                list($methods, $except) = explode(':', $except);
                $methods = explode(',', $methods);
            }

            $methods = array_map('strtoupper', $methods);

            if ($request->is($except) &&
                (empty($methods) || in_array($request->method(), $methods))) {
                return true;
            }
        }

        return false;
    }

}
