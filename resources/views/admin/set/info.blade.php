@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header">修改密码</div>
        <div class="layui-card-body" pad15>
            <form class="layui-form" action="{{route('admin.set.setinfo')}}" method="post">
                {{csrf_field()}}
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">用户名</label>
                    <div class="layui-input-block" style="width: 55%">
                        <input type="text" name="username" value="{{ $item->username ?: old('username') }}" lay-verify="required" lay-vertype="tips" placeholder="请输入用户名" class="layui-input" >
                    </div>
                    <div class="layui-input-block layui-input-company">修改谨慎，用于账号登录</div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">昵称</label>
                    <div class="layui-input-block" style="width: 55%">
                        <input type="text" name="name" value="{{ $item->name ?: old('name') }}" lay-verify="required" lay-vertype="tips" placeholder="请输入昵称" class="layui-input" >
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">邮箱</label>
                    <div class="layui-input-block" style="width: 55%">
                        <input type="email" name="email" value="{{$item->email?:old('email')}}"  placeholder="请输入Email" class="layui-input" >
                    </div>
                </div>

                <div class="layui-form-item">
                    <label for="" class="layui-form-label">手机号</label>
                    <div class="layui-input-block" style="width: 55%">
                        <input type="text" name="phone" value="{{$item->phone?:old('phone')}}"  placeholder="请输入手机号" class="layui-input">
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

        });
    </script>
@endsection
