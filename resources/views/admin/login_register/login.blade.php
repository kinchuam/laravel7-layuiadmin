@extends('admin.login_register.base')

@section('content')
    <div class="layadmin-user-login-box layadmin-user-login-body layui-form">
        <form action="" method="post">
            {{csrf_field()}}
            <div class="layui-form-item">
                <label class="layadmin-user-login-icon layui-icon layui-icon-username" for="LAY-user-login-username"></label>
                <input type="text" name="username" value="{{old('username')}}" lay-verify="required" lay-vertype="tips" placeholder="用户名" class="layui-input">
            </div>
            <div class="layui-form-item">
                <label class="layadmin-user-login-icon layui-icon layui-icon-password" for="LAY-user-login-password"></label>
                <input type="password" name="password"  lay-verify="required" lay-vertype="tips" placeholder="密码" class="layui-input">
            </div>
            <div class="layui-form-item">
                <div id="slider"></div>
            </div>

            <div class="layui-form-item" style="margin-bottom: 20px;">
                <input type="checkbox" name="remember" value="1" lay-skin="primary" title="记住密码" checked>
            </div>
            <div class="layui-form-item">
                <button type="submit" class="layui-btn layui-btn-fluid" lay-submit lay-filter="formDemo">登 入</button>
            </div>
        </form>
    </div>
@endsection
