@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-btn-group ">
                @can('content.files.expurgate')
                    <a class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete"><i class="layui-icon layui-icon-delete"></i> 删除</a>
                @endcan
                @can('content.files.recover')
                    <a class="layui-btn layui-btn-sm" id="listRecover"><i class="fa fa-wrench"></i> 恢复</a>
                @endcan
            </div>
        </div>

        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('content.files.recover')
                        <a class="layui-btn layui-btn-sm" lay-event="recover" title="恢复"><i class="fa fa-wrench"></i> </a>
                    @endcan
                    @can('content.files.expurgate')
                        <a class="layui-btn layui-btn-sm layui-btn-danger" lay-event="del" title="删除"><i class="layui-icon layui-icon-delete"></i> </a>
                    @endcan
                </div>
            </script>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['table'], function () {
            let $ = layui.$, table = layui.table,
                dataTable = table.render({
                    elem: '#dataTable'
                    ,url: "{{ route('admin.content.files.data') }}"
                    ,where:{recycle:1}
                    ,page: true
                    ,cols: [[
                        {checkbox: true, fixed: true}
                        ,{field: 'filename', title: '文件名'}
                        ,{field: 'size', title: '文件大小', width: 100, templet:function (d) { return GetFileSize(d.size); }}
                        ,{field: 'storage', title: '储存方式', width: 90}
                        ,{field: 'type', title: '文件类型', width: 110}
                        ,{field: 'deleted_at', title: '删除时间', width: 200}
                        ,{fixed: 'right', width: 150, align:'center', toolbar: '#options'}
                    ]]
                });
            //监听工具条
            table.on('tool(dataTable)', function(obj){
                let data = obj.data,layEvent = obj.event;
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                        $.post("{{ route('admin.content.files.expurgate') }}", {_method:'delete',ids:[data.id]}, function (result) {
                            if (result.code == 0){
                                obj.del();
                            }
                            layer.close(index);
                            layer.msg(result.message);
                        });
                    });
                }else if(layEvent === 'recover'){
                    layer.confirm('确认恢复吗？', function(index){
                        layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                        $.post("{{ route('admin.content.files.recover') }}", {ids:[data.id]}, function (result) {
                            if (result.code == 0){
                                obj.del();
                            }
                            layer.close(index);
                            layer.msg(result.message);
                        });
                    });
                }
            });
            //按钮批量删除
            $("#listDelete").click(function () {
                postData('删除', "{{ route('admin.content.files.expurgate') }}", {_method:'delete'});
            });

            $("#listRecover").click(function () {
                postData('恢复', "{{ route('admin.content.files.recover') }}");
            })

            function postData(msg, url, data = {})
            {
                let ids = [], hasCheck = table.checkStatus('dataTable'), hasCheckData = hasCheck.data;
                if (hasCheckData.length > 0){
                    layui.each(hasCheckData, function (index, element) {
                        ids.push(element.id);
                    })
                }
                if (ids.length > 0){
                    layer.confirm('确认'+msg+'吗？', function(index){
                        layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                        Object.assign(data, {ids});
                        $.post(url, data, function (result) {
                            if (result.code == 0){
                                dataTable.reload();
                            }
                            layer.close(index);
                            layer.msg(result.message, {icon:1});
                        });
                    });
                    return true;
                }
                layer.msg('请选择'+msg+'项',{icon:5});
            }
        })
    </script>
@endsection
