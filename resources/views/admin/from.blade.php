<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>LaravelAdmin - 表单页</title>
    <meta name="keywords" content="LaravelAdmin - 表单页">
    <meta name="description" content="LaravelAdmin - 表单页">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="baseUrl" content="{{config('app.url')}}">
    <link rel="stylesheet" href="{{ asset('static/common/layui/css/layui.css') }}" media="all">
    <link rel="stylesheet" href="{{ asset('static/admin/css/style.css') }}" media="all">
</head>
<body>

<div class="layui-fluid fromPage">
    @yield('content')
</div>

<script src="{{ asset('static/common/layui/layui.js') }}"></script>
<script src="{{asset('static/admin/js/app.js')}}"></script>
<script>

    layui.config({
        base: "{{asset('static/common')}}/" //静态资源所在路径
    }).extend({
        index: 'lib/index'
        ,iconHhysFa: 'plugins/iconHhys/iconHhysFa'
        ,tinymce: 'plugins/tinymce/tinymce'
        ,xmSelect: 'plugins/xm-select'
    }).use(['form','layer'], function(){
        let $ = layui.$
            , layer = layui.layer
            , form = layui.form
            , condition = false;

        form.on('submit(formDemo)', function(data) {
            if (condition) { return false; }
            layui.event('fromPage','submitData', data);
        });

        layui.onevent('fromPage', 'submitData', function(data){
            let field = data.field, index = parent.layer.getFrameIndex(window.name);
            condition = true;
            layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
            $.post($("form").attr('action'), field, function (result) {
                condition = false
                check_succeed(result, index);
            });
        });

        function check_succeed(result, index) {
            function return_msg(index, message) {
                parent.layer.close(index);
                parent.layer.msg(message, {time: 2000, icon: 6});
                return true;
            }
            let noRefresh = result.noRefresh, fromData = result.fromData;
            if (result.code == 0) {
                if (typeof(noRefresh) != "undefined" && !noRefresh){
                    if(fromData){
                        if(parent.layui.table) {
                            parent.layui.table.reload('dataTable', {
                                where: fromData
                            });
                        }else if(parent.layui.treeTable) {
                            parent.layui.treeTable.reload('dataTable',{
                                where: fromData
                            });
                        }
                        return return_msg(index, result.message);
                    }
                    if(parent.layui.table) {
                        parent.layui.table.reload('dataTable');
                    }else if(parent.layui.treeTable) {
                        parent.layui.treeTable.reload('dataTable');
                    }
                    return return_msg(index, result.message);
                }
                parent.location.reload();
                return return_msg(index, result.message);
            }
            layer.msg(result.message, {time: 3000, icon: 5});
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function(XMLHttpRequest, Status, errorThrown) {
                condition = false;
                layer.msg(get_responseText(XMLHttpRequest), {time: 3000, icon: 5});
            }
        });
    });

</script>
@yield('script')
</body>
</html>
