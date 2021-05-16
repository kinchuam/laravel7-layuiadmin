<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>LaravelAdmin - 内页</title>
    <meta name="keywords" content="LaravelAdmin - 内页">
    <meta name="description" content="LaravelAdmin - 内页">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="baseUrl" content="{{config('app.url')}}">
    <link rel="stylesheet" href="{{ asset('static/common/layui/css/layui.css') }}" media="all">
    <link rel="stylesheet" href="{{ asset('static/common/style/admin.css') }}" media="all">
    <link rel="stylesheet" href="{{ asset('static/admin/css/style.css') }}" media="all">
</head>
<body>

<div class="layui-fluid">
    @yield('content')
</div>

<script src="{{ asset('static/common/layui/layui.js') }}"></script>
<script src="{{ asset('static/admin/js/app.js') }}"></script>
<script src="{{asset('static/admin/js/font-awesome.min.js')}}"></script>

<script>
    layui.config({
        base: "{{asset('static/common')}}/" //静态资源所在路径
    }).extend({
        index: 'lib/index'
        ,tag: 'plugins/tag/tag'
        ,opTable: 'plugins/opTable/opTable.min'
        ,treeTable: 'plugins/treeTable'
        ,xmSelect: 'plugins/xm-select'
        ,okLayer: 'plugins/okLayer'
        ,Library: 'plugins/fileLibrary/fileLibrary'
    }).use(['layer','form'],function () {
        let $ = layui.$
            , form = layui.form
            , layer = layui.layer
            , status = false;

        form.on('submit(formDemo)', function(data){
            if(status){return false;}
            let field = data.field;
            status = true;
            layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
            $.post($(this).parents("form").attr('action'), field, function (result) {
                status = false;
                if (result.code == 0) {
                    layer.msg(result.message, {time: 2000, icon: 6}); return true;
                }
                layer.msg(result.message, {time: 3000, icon: 5})
            });
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                layer.msg(get_responseText(XMLHttpRequest), {time: 3000, icon: 5});
            }
        });

    });
</script>
@yield('script')
</body>
</html>
