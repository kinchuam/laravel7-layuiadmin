@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                @can('system.role.destroy')
                    <button class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删 除</button>
                @endcan
                @can('system.role.create')
                    <a class="layui-btn layui-btn-sm" onclick="active.openLayerForm('{{route('admin.role.create')}}','添加角色');">添 加</a>
                @endcan
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('system.role.edit')
                        <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    @can('system.role.permission')
                        <a class="layui-btn layui-btn-sm" lay-event="permission">权限</a>
                    @endcan
                    @can('system.role.destroy')
                        <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">删除</a>
                    @endcan
                </div>
            </script>
        </div>
    </div>
@endsection

@section('script')
    @can('system.role')
    <script>
        layui.use(['layer','table'],function () {
            var layer = layui.layer,table = layui.table;

            var dataTable = table.render({
                elem: '#dataTable'
                ,url: "{{ route('admin.data') }}"
                ,where:{model:"role"}
                ,page: true
                ,cols: [[
                    {checkbox: true,fixed: true}
                    ,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'name', title: '名称'}
                    ,{field: 'display_name', title: '显示名称'}
                    ,{field: 'created_at', title: '创建时间',width:200}
                    ,{field: 'updated_at', title: '更新时间',width:200}
                    ,{fixed: 'right', width: 260, align:'center', toolbar: '#options'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){
                var data = obj.data
                    ,layEvent = obj.event;
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        $.post("{{ route('admin.role.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                            if (result.code==0){
                                obj.del();
                            }
                            layer.close(index);
                            layer.msg(result.msg,{icon:6});
                        });
                    });
                } else if(layEvent === 'edit'){
                    active.openLayerForm('{{Request()->getbaseUrl()}}/admin/role/'+data.id+'/edit','编辑角色');
                } else if (layEvent === 'permission'){
                    active.openLayerForm('{{Request()->getbaseUrl()}}/admin/role/'+data.id+'/permission','编辑权限');
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
                        $.post("{{ route('admin.role.destroy') }}",{_method:'delete',ids:ids},function (result) {
                            if (result.code==0){
                                dataTable.reload();
                            }
                            layer.close(index);
                            layer.msg(result.msg,{icon:6});
                        });
                    })
                }else {
                    layer.msg('请选择删除项',{icon:5});
                }
            })
        });
    </script>
    @endcan
@endsection
