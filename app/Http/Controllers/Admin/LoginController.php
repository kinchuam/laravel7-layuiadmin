<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    /**
     * 登录表单
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showLoginForm()
    {
        if ($this->guard()->check()) {
            // 用户已经登录了...
            return redirect(route('admin.layout'));
        }

        return view('admin.login_register.login');
    }

    public function login(Request $request)
    {
        $request->merge([
            'username' => base64_decode($request->get('username')),
            'password' => base64_decode($request->get('password'))
        ]);
        $this->validateLogin($request);
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->login_log('密码错误次数已达'.$this->maxAttempts().'次，已锁定!', $request->get('username','guest'));
            $this->fireLockoutEvent($request);
            $this->sendLockoutResponse($request);
        }
        if ($this->attemptLogin($request)) {
            $user = $this->guard()->user();
            $this->login_log('登录成功', $user->username??'');
            return $this->sendLoginResponse($request);
        }
        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);
        if ($request->expectsJson()) {
            return $this->authenticated($request, $this->guard()->user()) ?: response()->json([
                'url' => $request->session()->pull('url.intended', $this->redirectPath())
            ]);
        }
        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());
    }

    /**
     * 用于登录的字段
     * @return string
     */
    public function username(): string
    {
        return 'username';
    }

    /**
     * 登录成功后的跳转地址
     * @return string
     */
    public function redirectTo(): string
    {
        return route('admin.layout');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request)
    {
        $user = $this->guard()->user();
        Cache::forget('adminMenus:'.($user->uuid??''));
        $this->login_log('退出登录', $user->username??'');
        Cache::forget('cachedUser:'.($user->id??''));
        $this->guard()->logout();
        $request->session()->invalidate();
        return redirect(route('admin.login'));
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard(): \Illuminate\Contracts\Auth\StatefulGuard
    {
        return Auth::guard('admin');
    }


    protected function login_log($message, $username)
    {
        if (!empty($username)) {
            try {
                $agent = new \Jenssegers\Agent\Agent();
                $ip = GetClientIp();
                $data = [
                    'ip' => $ip,
                    'username' => $username,
                    'message' => $message,
                    'platform' => $agent->platform(),
                    'browser'  => $agent->browser(),
                    'agent' => $_SERVER['HTTP_USER_AGENT'],
                ];
                if (!empty($ip) && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    $data['ip_data'] = json_encode((new \Ip2Region())->btreeSearch($ip));
                }
                LoginLog::create($data);
            }catch (\Exception $e) {
                logger()->error('login_log error: '.$e->getMessage());
            }
        }
    }

}
