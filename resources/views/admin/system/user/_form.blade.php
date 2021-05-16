<div class="layui-form-item">
    <label for="" class="layui-form-label must">账号</label>
    <div class="layui-input-block" >
        <input type="text" name="username" value="" lay-verify="username" lay-vertype="tips" placeholder="请输入账号" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label must">昵称</label>
    <div class="layui-input-block" >
        <input type="text" name="name" value="" lay-verify="required" lay-vertype="tips" placeholder="请输入昵称" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">邮箱</label>
    <div class="layui-input-block" >
        <input type="email" name="email" value=""  placeholder="请输入Email" class="layui-input" >
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">手机号</label>
    <div class="layui-input-block" >
        <input type="text" name="phone" value="" placeholder="请输入手机号" class="layui-input">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label must">密码</label>
    <div class="layui-input-block" >
        <input type="password" name="password" placeholder="请输入密码" lay-verify="pass" lay-vertype="tips"  class="layui-input">
    </div>
    <div class="layui-input-block layui-input-company layui-hide pass_text">不修改密码则留空</div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label must">确认密码</label>
    <div class="layui-input-block" >
        <input type="password" name="password_confirmation" lay-verify="pass_confirm" lay-vertype="tips" placeholder="请输入确认密码" class="layui-input">
    </div>
    <div class="layui-input-block layui-input-company layui-hide pass_text">不修改密码则留空</div>
</div>

<div class="layui-form-item layui-hide">
    <div class="layui-input-block">
        <input type="button" class="layui-btn" lay-submit="" lay-filter="formDemo" id="formDemo" value="确 认">
    </div>
</div>
