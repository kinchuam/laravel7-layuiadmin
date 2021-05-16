@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-btn-group">
                @can('system.role.destroy')
                    <a class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete"><i class="layui-icon layui-icon-delete"></i> 删除</a>
                @endcan
                @can('system.role.create')
                    <a class="layui-btn layui-btn-sm" id="addBtn"><i class="layui-icon layui-icon-add-circle"></i> 添加</a>
                @endcan
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('system.role.edit')
                        <a class="layui-btn layui-btn-sm" lay-event="edit" title="编辑"><i class="layui-icon layui-icon-edit"></i></a>
                    @endcan
                    @can('system.role.permission')
                        <a class="layui-btn layui-btn-sm layui-btn-normal" lay-event="permission" title="设置权限"><i class="layui-icon layui-icon-set-fill"></i></a>
                    @endcan
                    @can('system.role.destroy')
                        <a class="layui-btn layui-btn-danger layui-btn-sm" title="删除" lay-event="del"><i class="layui-icon layui-icon-delete"></i></a>
                    @endcan
                </div>
            </script>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['table', 'okLayer'], function () {
            let $ = layui.$, table = layui.table, okLayer = layui.okLayer,
                dataTable = table.render({
                    elem: '#dataTable'
                    ,url: "{{ route('admin.role.data') }}"
                    ,page: true
                    ,cols: [[
                        {checkbox: true,fixed: true}
                        ,{field: 'name', title: '名称'}
                        ,{field: 'display_name', title: '显示名称'}
                        ,{field: 'created_at', title: '创建时间',width:200}
                        ,{field: 'updated_at', title: '更新时间',width:200}
                        ,{fixed: 'right', width: 200, align:'center', toolbar: '#options'}
                    ]]
                });

            $('#addBtn').on('click', function () {
                okLayer.open('添加角色', '{{route('admin.role.create')}}');
            })
            //监听工具条
            table.on('tool(dataTable)', function(obj){
                let data = obj.data ,layEvent = obj.event;
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                        $.post("{{ route('admin.role.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                            if (result.code === 0){
                                obj.del();
                            }
                            layer.close(index);
                            layer.msg(result.message,{icon:6});
                        });
                    });
                } else if(layEvent === 'edit'){
                    okLayer.open('编辑角色', getRouteUrl('admin/role/'+data.id+'/edit'));
                } else if (layEvent === 'permission'){
                    okLayer.open('编辑权限', getRouteUrl('admin/role/'+data.id+'/permission'), {height: '80%'});
                }
            });
            //按钮批量删除
            $("#listDelete").click(function () {
                let ids = [],hasCheck = table.checkStatus('dataTable'),hasCheckData = hasCheck.data;
                if (hasCheckData.length>0){
                    $.each(hasCheckData,function (index,element) {
                        ids.push(element.id);
                    })
                }
                if (ids.length>0){
                    layer.confirm('确认删除吗？', function(index){
                        layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                        $.post("{{ route('admin.role.destroy') }}", {_method:'delete',ids:ids}, function (result) {
                            if (result.code === 0){
                                dataTable.reload();
                            }
                            layer.close(index);
                            layer.msg(result.message,{icon:1});
                        });
                    })
                    return true;
                }
                layer.msg('请选择删除项',{icon:5});
            })
        })
    </script>
@endsection
