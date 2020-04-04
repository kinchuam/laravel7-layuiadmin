{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label must">用户名</label>
    <div class="layui-input-inline" style="width: 55%;">
        <input type="text" name="username" value="{{ isset($user->username) ? $user->username : old('username') }}" lay-verify="username" lay-vertype="tips" placeholder="请输入用户名" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label must">昵称</label>
    <div class="layui-input-inline" style="width: 55%;">
        <input type="text" name="name" value="{{ isset($user->name) ? $user->name : old('name') }}"  placeholder="请输入昵称" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">邮箱</label>
    <div class="layui-input-inline" style="width: 55%;">
        <input type="email" name="email" value="{{isset($user->email)?$user->email:old('email')}}"  placeholder="请输入Email" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">手机号</label>
    <div class="layui-input-inline" style="width: 55%;">
        <input type="text" name="phone" value="{{isset($user->phone)?$user->phone:old('phone')}}" placeholder="请输入手机号" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label must">密码</label>
    <div class="layui-input-block" style="width: 55%;">
        <input type="password" name="password" placeholder="请输入密码"  class="layui-input">
    </div>
    @if(isset($user))
        <div class="layui-input-block layui-input-company">不修改密码则留空</div>
    @endif
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label must">确认密码</label>
    <div class="layui-input-inline" style="width: 55%;">
        <input type="password" name="password_confirmation" placeholder="请输入密码" class="layui-input">
    </div>

</div>

<div class="layui-form-item layui-hide">
    <div class="layui-input-block">
        <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
    </div>
</div>
