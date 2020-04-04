@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header">数据库列表</div>
        <div class="layui-card-body">

            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('database.databaserestore.restore')
                        <a class="layui-btn layui-btn-sm layui-btn-normal" lay-event="restore">恢复</a>
                    @endcan
                    @can('database.databaserestore.download')
                        <a class="layui-btn layui-btn-sm" lay-event="down">下载</a>
                    @endcan
                    @can('database.databaserestore.destroy')
                        <a class="layui-btn layui-btn-danger layui-btn-sm " lay-event="del">删除</a>
                    @endcan
                </div>
            </script>

        </div>
    </div>
@endsection

@section('script')
    @can('database.restore')
        <script>
            layui.use(['layer','table','laydate','laytpl'],function () {
                 var layer = layui.layer,table = layui.table,laytpl = layui.laytpl;
                laytpl.getfilesize = function (size=0) {
                    if (!size)
                        return "";
                    var num = 1024.00; //byte
                    if (size < num)
                        return size + "B";
                    if (size < Math.pow(num, 2))
                        return (size / num).toFixed(2) + "K"; //kb
                    if (size < Math.pow(num, 3))
                        return (size / Math.pow(num, 2)).toFixed(2) + "M"; //M
                    if (size < Math.pow(num, 4))
                        return (size / Math.pow(num, 3)).toFixed(2) + "G"; //G
                    return (size / Math.pow(num, 4)).toFixed(2) + "T"; //T
                };
                 table.render({
                    elem: '#dataTable'
                    ,url: "{{ route('admin.databaserestore.data') }}"
                    ,cols: [[
                        {field: 'title', title: '备份名称'}
                        ,{field: 'date', title: '备份时间'}
                        ,{field: 'size', title: '备份大小', templet: function (d) { return layui.laytpl.getfilesize(d.size);}}
                         ,{field: 'part', title: '卷数'}
                         ,{field: 'compress', title: '压缩'}
                         ,{field: '', title: '状态', templet: '<div>-</div>'}
                         ,{fixed: 'right', width: 180, align:'center', toolbar: '#options'}
                    ]]
                });

                //监听工具条
                table.on('tool(dataTable)', function(obj) {
                    var data = obj.data,code = ".";
                    if (obj.event === 'del') {
                        layer.confirm('确定删除这条数据？', { icon: 3, title: '提示' }, function(index) {
                            layer.close(index);
                            $.post('{{ route('admin.databaserestore.destroy') }}', { _method:'delete', 'time': data.time }, function(result) {
                                if (result.code==0){
                                    obj.del();
                                }
                                layer.close(index);
                                layer.msg(result.msg,{icon:6});
                            });
                        })
                    } else if (obj.event === 'down') {
                        window.open('{{ route('admin.databaserestore.download') }}' + "?time=" + data.time, '_self')
                    } else if (obj.event === 'restore') {
                        var self = this,
                            url = '{{ route('admin.databaserestore.restore') }}' + '?time=' + data.time;
                        layer.confirm('确定恢复此条数据库吗？', { icon: 3, title: '提示' }, function(index) {
                            layer.close(index);
                            $.get(url, success, "json");
                            window.onbeforeunload = function() { return "正在还原数据库，请不要关闭！" }
                            return false;
                        });
                        function success(result) {
                            if (result.code) {
                                var gz = result.data?result.data.gz:0,
                                    part = result.data?result.data.part:'';
                                if (gz) {
                                    result.msg += code;
                                    if (code.length === 5) {
                                        code = ".";
                                    } else {
                                        code += ".";
                                    }
                                }

                                if (part>0){
                                    layer.msg(result.msg, {
                                        icon: 16
                                        ,shade: 0.01
                                        ,time: 60*1000
                                    });
                                }else{
                                    layer.msg(result.msg);
                                }
                                $(self).parents('tr').find('td:eq(5)').find('div').text(result.msg);
                                //$(self).parent().prev().text(res.info);
                                if (part) {
                                    $.get(url, { "part": part, "start": result.data.start },
                                        success,
                                        "json"
                                    );
                                } else {
                                    window.onbeforeunload = function() { return null; }
                                }
                            } else {
                                layer.msg(result.msg);
                                $(self).parents('tr').find('td:eq(5)').find('div').text(result.msg);
                            }
                        }
                    }
                });
            })
        </script>
    @endcan
@endsection
