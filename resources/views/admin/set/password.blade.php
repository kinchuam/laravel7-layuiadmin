@extends('admin.base')

@section('content')
<div class="layui-card">
    <div class="layui-card-header">修改密码</div>
    <div class="layui-card-body" pad15>
        <form class="layui-form" action="{{route('admin.set.setpassword')}}" method="post">
            {{csrf_field()}}
            <div class="layui-form-item">
                <label class="layui-form-label">当前密码</label>
                <div class="layui-input-block" style="width: 55%">
                    <input type="password" name="old_password" lay-verify="pass" placeholder="请输入当前密码" lay-vertype="tips"  class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">新密码</label>
                <div class="layui-input-block" style="width: 55%">
                    <input type="password" name="password" lay-verify="pass" placeholder="请输入新密码" lay-vertype="tips"  class="layui-input">
                </div>
                <div class="layui-input-block layui-input-company">6到14个字符</div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">确认新密码</label>
                <div class="layui-input-block" style="width: 55%">
                    <input type="password" name="password_confirmation" placeholder="请输入新密码" lay-verify="pass" lay-vertype="tips" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit lay-filter="setmypass">确认修改</button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection

@section('script')
    <script>
        layui.use(['form'],function () {
            var form = layui.form;
            form.verify({
                pass: [
                    /^[\S]{6,14}$/
                    ,'密码必须6到14位，且不能出现空格'
                ]
            });
        });
    </script>
@endsection
