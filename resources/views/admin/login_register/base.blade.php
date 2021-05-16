<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LaravelAdmin 后台管理系统</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('favicon-32x32.png')}}">
    <link rel="stylesheet" href="{{ asset('static/common/layui/css/layui.css') }}" media="all">
    <link rel="stylesheet" href="{{ asset('static/common/style/admin.css') }}" media="all">
    <link rel="stylesheet" href="{{ asset('static/common/style/login.css') }}" media="all">
    <style>
        .layadmin-user-login-main{background-color: #fff;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;}
        .layadmin-user-login {position: absolute;right: 0;margin:  0 auto;}
        body{background-image: linear-gradient(to top, rgb(251, 194, 235) 0%, rgb(166, 193, 238) 100%);}
    </style>
</head>
<body>

<div class="layadmin-user-login layadmin-user-display-show" >
    <div class="layadmin-user-login-main">
        <div class="layadmin-user-login-box layadmin-user-login-header">
            <h2>LaravelAdmin</h2>
            <p>LaravelAdmin 后台管理系统</p>
        </div>
        @yield('content')
    </div>
</div>

<script src="{{ asset('static/common/layui/layui.js') }}"></script>
<script src="{{asset('static/admin/js/app.js')}}"></script>
<script>

    layui.config({
        base: "{{ asset('static/common/') }}/" //静态资源所在路径
    }).extend({
        sliderVerify: 'plugins/sliderVerify/sliderVerify'
    }).use(['layer', 'sliderVerify', 'form'],function () {
        let $ = layui.$
            , layer = layui.layer
            , form = layui.form
            , sliderVerify = layui.sliderVerify
            , condition = false;

        let slider = sliderVerify.render({
            elem: '#slider',
            onOk: function(){
                layer.msg("滑块验证通过");
            }
        });
        //监听提交
        form.on('submit(formDemo)', function(data) {
            let field = data.field;
            if (condition) { return false;}
            if(!slider.isOk()){
                layer.msg("请先通过滑块验证"); return false;
            }
            condition = true;
            field.username = window.btoa(field.username);
            field.password = window.btoa(field.password);
            layer.msg('正在处理请求...', { icon: 16, shade: 0.01, time:false });
            $.post(location.href, field, function (result) {
                condition = false;
                layer.msg('登录成功', {time: 2000, icon: 6})
                window.location.replace(result.url);
            });
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                condition = false;
                slider.reset();
                layer.msg(get_responseText(XMLHttpRequest), {time: 3000, icon: 5});
            }
        });

    })
</script>
</body>
</html>
