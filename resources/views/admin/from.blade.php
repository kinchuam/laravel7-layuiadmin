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
    <link rel="stylesheet" href="{{ asset('static/common/layui/css/layui.css') }}" media="all">
    <link rel="stylesheet" href="{{ asset('static/admin/css/style.css') }}" media="all">
</head>
<body>

<div class="layui-fluid" style="margin-top: 15px;">
    @yield('content')
</div>
<script src="//cdn.bootcdn.net/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="{{ asset('static/common/layui/layui.js') }}"></script>
<script src="{{ asset('static/admin/js/admin.js') }}"></script>
<script>

    layui.config({
        base: "{{ asset('static/common').'/' }}" //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
        ,IconFonts: 'plugins/iconFonts/iconFonts'
        ,tag: 'plugins/tag/tag'
        ,tinymce: 'plugins/tinymce/tinymce'
        ,inputTags: 'plugins/inputTags/inputTags'
        ,xmSelect: 'plugins/xmSelect/xm-select'
    }).use(['form','layer'], function(){
        var form = layui.form,layer = layui.layer,ftype = false;

        layui.onevent('groupData','submitData',function(data){
            var field = data.field,newarr = []; //获取提交的字段
            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引

            $(".layui-form .layui-btn-container").each(function(r,v) {
                newarr[$(v).attr('id')] = [];
                $(v).find('.tag-item').each(function(ra,va) {
                    var str = $(va).text(),str1 = $(va).find('i').text();
                    newarr[$(v).attr('id')].push(str.replace(str1,''));
                });
            });
            if (newarr!==''){
                for (let i in newarr) {
                    field[i] = newarr[i].join('|');
                }
            }

            //提交 Ajax 成功后，关闭当前弹层并重载表格
            $.post($("form").attr('action'),field,function (result) {
                ftype = false;
                if (result.status === 'success') {
                    if (typeof(result.noRefresh) !="undefined" && !result.noRefresh){
                        if(result.fromdata){
                            if(parent.layui.table) {
                                parent.layui.table.reload('dataTable', {
                                    where: result.fromdata
                                });
                            }else if(parent.layui.treeTable) {
                                parent.layui.treeTable.reload('dataTable',{
                                    where: result.fromdata
                                });
                            }
                        }else{
                            if(parent.layui.table) {
                                parent.layui.table.reload('dataTable');
                            }else if(parent.layui.treeTable) {
                                parent.layui.treeTable.reload('dataTable');
                            }
                        }
                    }else if (result.refresh){
                        parent.location.reload();
                    }
                    parent.layer.close(index);
                    parent.layer.msg(result.message, {time: 2000, icon: 6})
                } else {
                    layer.msg(result.message, {time: 3000, icon: 5})
                }

            });
        });

        form.on('submit(formDemo)', function(data) {
            layui.event('groupData','submitData', data);
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                var status = XMLHttpRequest.status,responseText = XMLHttpRequest.responseText,msg = '不好，有错误';
                switch (status) {
                    case 400:
                        msg = responseText !== '' ? responseText : '失败了';
                        break;
                    case 401:
                        msg = responseText !== '' ? responseText : '你没有权限';
                        break;
                    case 403:
                        msg =  '你没有权限执行此操作!';
                        break;
                    case 404:
                        msg = '你访问的操作不存在';
                        break;
                    case 406:
                        msg = '请求格式不正确';
                        break;
                    case 410:
                        msg = '你访问的资源已被删除';
                        break;
                    case 422:
                        var errors = $.parseJSON(XMLHttpRequest.responseText);
                        if (errors instanceof Object) {
                            var m = '';
                            $.each(errors, function(index, item) {
                                if (item instanceof Object) {
                                    $.each(item, function(index, i) {
                                        m = m + i + '<br>';
                                    });
                                } else {
                                    m = m + item + '<br>';
                                }
                            });
                            msg = m;
                        }
                        break;
                    case 429:
                        msg = '超出访问频率限制';
                        break;
                    case 500:
                        msg = '500 INTERNAL SERVER ERROR';
                        break;
                    default:
                        return true;
                }
                ftype = false;
                layer.msg(msg, {time: 3000, icon: 5});
            }
        });
    });

</script>
@yield('script')
</body>
</html>
