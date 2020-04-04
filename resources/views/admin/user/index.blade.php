@extends('admin.base')

@section('content')
    <div class="layui-card">

        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                @can('system.user.destroy')
                <button class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删 除</button>
                @endcan
                @can('system.user.create')
                <a class="layui-btn layui-btn-sm" onclick="active.openLayerForm('{{route('admin.user.create')}}','添加用户');">添 加</a>
                @endcan
            </div>
        </div>

        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('system.user.create')
                    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    @can('system.user.role')
                    <a class="layui-btn layui-btn-sm" lay-event="role">角色</a>
                    @endcan
                    @can('system.user.permission')
                    <a class="layui-btn layui-btn-sm" lay-event="permission">权限</a>
                    @endcan
                    @can('system.user.destroy')
                        @{{# if (d.id!=1){ }}
                        <a class="layui-btn layui-btn-danger layui-btn-sm " lay-event="del">删除</a>
                        @{{# } }}
                    @endcan
                </div>
            </script>
        </div>

    </div>
@endsection

@section('script')
    @can('system.user')
    <script>
        layui.use(['layer','table'],function () {
            var layer = layui.layer,table = layui.table;

            var dataTable = table.render({
                elem: '#dataTable'
                ,url: "{{ route('admin.data') }}"
                ,where:{model:"user"}
                ,page: true
                ,cols: [[
                    {checkbox: true,fixed: true}
                    ,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'name', title: '用户名'}
                    ,{field: 'email', title: '邮箱'}
                    ,{field: 'phone', title: '电话'}
                    ,{field: 'created_at', title: '创建时间',width:200}
                    ,{field: 'updated_at', title: '更新时间',width:200}
                    ,{fixed: 'right', width: 220, align:'center', toolbar: '#options'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){
                var data = obj.data
                    ,layEvent = obj.event;
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        $.post("{{ route('admin.user.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                            if (result.code==0){
                                obj.del();
                            }
                            layer.close(index);
                            layer.msg(result.msg,{icon:6});
                        });
                    });
                } else if(layEvent === 'edit'){
                    active.openLayerForm('{{Request()->getbaseUrl()}}/admin/user/'+data.id+'/edit','更新用户');
                } else if (layEvent === 'role'){
                    active.openLayerForm('{{Request()->getbaseUrl()}}/admin/user/'+data.id+'/role','用户【'+data.name+'】分配角色');
                } else if (layEvent === 'permission'){
                    active.openLayerForm('{{Request()->getbaseUrl()}}/admin/user/'+data.id+'/permission','用户 【'+data.name+'】分配直接权限，直接权限与角色拥有的角色权限不冲突');
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
                        $.post("{{ route('admin.user.destroy') }}",{_method:'delete',ids:ids},function (result) {
                            if (result.code==0){
                                dataTable.reload();
                            }
                            layer.close(index);
                            layer.msg(result.msg,{icon:5});
                        });
                    })
                }else {
                    layer.msg('请选择删除项',{icon:5});
                }
            });
        })
    </script>
    @endcan
@endsection



