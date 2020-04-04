<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\User\LoginLog;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $this->validateLogin($request);
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            LoginLog::create([
                'ip' => $request->getClientIp(),
                'uuid' => $this->guard()->user()?$this->guard()->user()->uuid:'',
                'message' => '登录成功',
                'agent' => $_SERVER['HTTP_USER_AGENT'],
            ]);
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * 用于登录的字段
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * 登录成功后的跳转地址
     * @return string
     */
    public function redirectTo()
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
        $uuid = $this->guard()->user()?$this->guard()->user()->uuid:'';
        $this->guard()->logout();
        $request->session()->invalidate();
        if ($uuid) {
            LoginLog::create([
                'ip' => $request->getClientIp(),
                'uuid' => $uuid,
                'message' => '退出登录',
                'agent' => $_SERVER['HTTP_USER_AGENT'],
            ]);
        }
        return redirect(route('admin.login'));
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }

}
