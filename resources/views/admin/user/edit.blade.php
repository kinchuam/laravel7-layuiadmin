@extends('admin.from')

@section('content')
<div class="layui-card-body">
    <form class="layui-form" action="{{route('admin.user.update',['id'=>$user->id])}}" method="post">
        <input type="hidden" name="id" value="{{$user->id}}">
        {{method_field('put')}}
        @include('admin.user._form')
    </form>
</div>
@endsection

@section('script')
    <script>
        layui.use(['form'],function () {
            var form = layui.form;
            form.verify({
                username: [
                    /^[\S]{4,14}$/
                    ,'用户名必须至少4到14字符'
                ],
                pass: [
                    /^[\S]{6,14}$/
                    ,'密码必须6到14位，且不能出现空格'
                ]
            });
        });
    </script>
@endsection
