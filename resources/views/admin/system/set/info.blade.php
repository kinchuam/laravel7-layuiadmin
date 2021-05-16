@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header">修改密码</div>
        <div class="layui-card-body" pad15>
            <form class="layui-form layui-form-pane" action="{{route('admin.basic.setInfo')}}" method="post" lay-filter="example">
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">账号</label>
                    <div class="layui-input-block" style="width: 55%">
                        <input type="text" name="username" value="" lay-verify="required" lay-vertype="tips" placeholder="请输入账号" class="layui-input" >
                    </div>
                    <div class="layui-input-block layui-input-company">谨慎修改，用于账号登录</div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">昵称</label>
                    <div class="layui-input-block" style="width: 55%">
                        <input type="text" name="name" value="" lay-verify="required" lay-vertype="tips" placeholder="请输入昵称" class="layui-input" >
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">邮箱</label>
                    <div class="layui-input-block" style="width: 55%">
                        <input type="email" name="email" value=""  placeholder="请输入Email" class="layui-input" >
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">手机号</label>
                    <div class="layui-input-block" style="width: 55%">
                        <input type="text" name="phone" value=""  placeholder="请输入手机号" class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn" lay-submit lay-filter="formDemo"><i class="layui-icon layui-icon-release"></i> 确认修改</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form'], function () {
            let form = layui.form;
            form.val('example', {
                "username": "{{isset($item->username)?$item->username:old('username')}}"
                ,"name": "{{isset($item->name)?$item->name:old('name')}}"
                ,"email": "{{isset($item->email)?$item->email:old('email')}}"
                ,"phone": "{{isset($item->phone)?$item->phone:old('phone')}}"
            });
        })
    </script>
@endsection
