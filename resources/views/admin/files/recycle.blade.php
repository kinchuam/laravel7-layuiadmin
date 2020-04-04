@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group ">
                @can('system.files.expurgate')
                    <button class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删 除</button>
                @endcan
                @can('system.files.recover')
                    <button class="layui-btn layui-btn-sm" id="listRecover">恢 复</button>
                @endcan
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('system.files.recover')
                        <a class="layui-btn layui-btn-sm" lay-event="recover">恢复</a>
                    @endcan
                    @can('system.files.expurgate')
                        <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">删除</a>
                    @endcan
                </div>
            </script>
        </div>
    </div>
@endsection

@section('script')
    @can('config.site.update')
        <script>

            layui.use(['layer','table','laytpl'],function () {
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
                    ,url: "{{ route('admin.files.data') }}"
                    ,where:{model:"files",recycle:1}
                    ,page: true
                    ,cols: [[
                        {checkbox: true,fixed: true}
                        ,{field: 'id', title: 'ID', sort: true,width:80}
                        ,{field: 'filename', title: '文件名'}
                        ,{field: 'size', title: '文件大小',templet:function (d) { return layui.laytpl.getfilesize(d.size);},width: 100}
                        ,{field: 'storage', title: '储存方式',width: 90}
                        ,{field: 'type', title: '文件类型',width: 110}
                        ,{field: 'deleted_at', title: '删除时间',width: 160}
                        ,{fixed: 'right', width: 150, align:'center', toolbar: '#options'}
                    ]]
                });

                //监听工具条
                table.on('tool(dataTable)', function(obj){
                    var data = obj.data
                        ,layEvent = obj.event;
                    if(layEvent === 'del'){
                        layer.confirm('确认删除吗？', function(index){
                            $.post("{{ route('admin.files.expurgate') }}",{_method:'delete',ids:[data.id]},function (result) {
                                if (result.code==0){
                                    obj.del();
                                }
                                layer.close(index);
                                layer.msg(result.msg);
                            });
                        });
                    }else if(layEvent === 'recover'){
                        layer.confirm('确认恢复吗？', function(index){
                            $.post("{{ route('admin.files.recover') }}",{ids:[data.id]},function (result) {
                                if (result.code==0){
                                    obj.del(); //删除对应行（tr）的DOM结构
                                }
                                layer.close(index);
                                layer.msg(result.msg);
                            });
                        });
                    }
                });

                //按钮批量删除
                $("#listDelete").click(function () {
                    var ids = [],hasCheck = table.checkStatus('dataTable'),hasCheckData = hasCheck.data;
                    if (hasCheckData.length>0){
                        $.each(hasCheckData,function (index,element) {
                            ids.push(element.id);
                        })
                    }
                    if (ids.length>0){
                        layer.confirm('确认删除吗？', function(index){
                            $.post("{{ route('admin.files.expurgate') }}",{_method:'delete',ids:ids},function (result) {
                                if (result.code==0){
                                    dataTable.reload();
                                }
                                layer.close(index);
                                layer.msg(result.msg);
                            });
                        })
                    }else {
                        layer.msg('请选择删除项');
                    }
                });

                $("#listRecover").click(function () {
                    var ids = [],hasCheck = table.checkStatus('dataTable'),hasCheckData = hasCheck.data;
                    if (hasCheckData.length>0){
                        $.each(hasCheckData,function (index,element) {
                            ids.push(element.id)
                        })
                    }
                    if (ids.length>0){
                        layer.confirm('确认恢复吗？', function(index){
                            $.post("{{ route('admin.files.recover') }}",{ids:ids},function (result) {
                                if (result.code==0){
                                    dataTable.reload();
                                }
                                layer.close(index);
                                layer.msg(result.msg);
                            });
                        })
                    }else {
                        layer.msg('请选择恢复项');
                    }
                })
            });
        </script>
    @endcan
@endsection
