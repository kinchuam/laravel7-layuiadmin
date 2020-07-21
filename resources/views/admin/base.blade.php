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

    <link rel="stylesheet" href="{{ asset('static/common/layui/css/layui.css') }}" media="all">
    <link rel="stylesheet" href="{{ asset('static/common/style/admin.css') }}" media="all">
    <link rel="stylesheet" href="{{ asset('static/admin/css/style.css') }}" media="all">
</head>
<body>

<div class="layui-fluid">
    @yield('content')
</div>

<script src="//cdn.bootcdn.net/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="{{ asset('static/admin/js/echarts.min.js') }}"></script>
<script src="{{ asset('static/common/layui/layui.js') }}"></script>
<script src="{{ asset('static/admin/js/admin.js') }}"></script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    layui.config({
        base: "{{ asset('static/common').'/' }}" //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
        ,tag: 'plugins/tag/tag'
        ,opTable: 'plugins/opTable/opTable'
        ,notice: 'plugins/notice/notice'
        ,IconFonts: 'plugins/iconFonts/iconFonts'
        ,treeTable: 'plugins/treeTable/treeTable'
        ,xmSelect: 'plugins/xmSelect/xm-select'
    }).use(['element','layer','notice'],function () {
        var layer = layui.layer;

        //错误提示
        @if(count($errors)>0)
        @foreach($errors->all() as $error)
        //layer.msg("{{$error}}",{icon:5});
        layui.notice.error("{{$error}}");
        @break
        @endforeach
        @endif

        //信息提示
        @if(session('status'))
        layui.notice.success("{{session('status')}}");
        //layer.msg("{{session('status')}}",{icon:6});
        @endif

    });
</script>
@yield('script')
</body>
</html>
