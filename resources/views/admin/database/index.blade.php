@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header">数据库列表</div>
        <div class="layui-card-body">
            <script type="text/html" id="toolbar">
                <div class="layui-btn-container">
                    <button class="layui-btn layui-btn-sm" lay-event="export">立即备份</button>
                    <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="optimize">优化表</button>
                    <button class="layui-btn layui-btn-sm layui-btn-normal" lay-event="repair">修复表</button>
                </div>
            </script>
            <table id="dataTable" lay-filter="dataTable"></table>
        </div>
    </div>
@endsection

@section('script')
    @can('database.backup')
        <script>
            layui.use(['layer','table','laydate','laytpl','element'],function () {
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
                var dataTable = table.render({
                    elem: '#dataTable'
                    ,url: "{{ route('admin.database.backup.data') }}"
                    ,toolbar: '#toolbar'
                    ,defaultToolbar:[]
                    ,cols: [[
                        {checkbox: true,fixed: true}
                        ,{field: 'name', title: '数据库表'}
                        ,{field: 'rows', title: '记录条数',sort: true}
                        ,{field: 'data_length', title: '占用空间', templet: function (d) { return layui.laytpl.getfilesize(d.data_length); }}
                        ,{field: 'engine', title: '类型'}
                        ,{field: 'collation', title: '编码'}
                        ,{field: 'create_time', title: '创建时间'}
                        ,{field: 'comment', title: '说明'}
                        ,{field: 'info', title: '备份状态' , align: 'center', templet: function (d) {
                                return '<div class="layui-progress layui-progress-big" lay-showPercent="yes" lay-filter="progress-'+ d.name +'"><div class="layui-progress-bar layui-bg-red" lay-percent="0%"></div></div><br>';
                            }}
                    ]]
                    ,done:function () {
                        layui.element.render('progress');
                    }
                });

                //监听工具条
                table.on('toolbar(dataTable)', function(obj){
                    var checkStatus = table.checkStatus(obj.config.id);
                    switch(obj.event){
                        case 'export':
                            var data = checkStatus.data,arr = [],$export = $(this);
                            if (checkStatus.data.length <= 0) {
                                layer.msg("请选择需要备份的数据");
                                return false;
                            }
                            $(data).each(function(i, o) {
                                arr.push(o.name);
                            });
                            $export.parent().children().addClass("layui-btn-disabled");

                            layer.msg('正在发送备份请求...', {
                                icon: 16
                                ,shade: 0.01,
                            });
                            $.post("{{ route('admin.database.backup.store') }}", { tables: arr },
                                function(data) {
                                    if (data.status=='success') {
                                        tables = data.data.tables;
                                        layer.msg(data.message + "开始备份，请不要关闭本页面！", {
                                            icon: 16
                                            ,shade: 0.01,
                                        });
                                        backup($export,data.data.tab);
                                        window.onbeforeunload = function() { return "正在备份数据库，请不要关闭！" }
                                    } else {
                                        layer.msg(data.message, { icon: 5 });
                                        $(this).parent().children().removeClass("disabled");
                                    }
                                },
                                "json"
                            );
                            break;
                        case 'optimize':
                            operation("{{ route('admin.database.backup.optimize') }}",checkStatus.data);
                            break;
                        case 'repair':
                            operation("{{ route('admin.database.backup.repair') }}",checkStatus.data);
                            break;
                    }
                });

                function operation(url,data) {
                    var arr = [];
                    if (data.length <= 0) {
                        layer.msg("请选择需要操作的数据");
                        return false;
                    }
                    $(data).each(function(i, o) {
                        arr.push(o.name);
                    });
                    $.post(url, { tables: arr }, function(result) {
                        if (result.code==0) {
                            layer.msg(result.msg, { icon: 1 });
                        } else {
                            layer.msg(result.msg, { icon: 5 });
                        }
                    }, "json");
                }

                function backup($export, tab, code) {
                    code && show_msg(tab.id, {rate:'0%'});
                    $.get("{{ route('admin.database.backup.store') }}", tab, function(data) {
                        if (data.status=='success') {
                            show_msg(tab.id, data);
                            var res = data.data?data.data.tab:'';
                            if (res == '') {
                                $export.parent().children().removeClass("layui-btn-disabled");
                                layer.msg(data.message, { icon: 1 },function () {
                                    dataTable.reload();
                                });
                                window.onbeforeunload = function() { return null };
                                return;
                            }
                            backup($export, data.data.tab, tab.id != data.data.tab.id);
                        } else {
                            layer.msg(data.message, { icon: 5 });
                            $export.parent().children().removeClass("disabled");
                        }
                    });
                }

                function show_msg(id, data) {
                    layui.element.progress('progress-'+ $('.layui-table').find('tr:eq('+id+') td:eq(1)').find('div').text(), (data.rate||'0%'));
                }

            })
        </script>
    @endcan
@endsection
