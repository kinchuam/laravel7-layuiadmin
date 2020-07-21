@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group ">
                @can('system.permission.destroy')
                    <button class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删 除</button>
                @endcan
                @can('system.permission.create')
                    <a class="layui-btn layui-btn-sm"  onclick="active.openLayerForm('{{route('admin.permission.create')}}','添加权限');">添 加</a>
                @endcan
                <button class="layui-btn layui-btn-sm" id="returnParent" pid="0" style="display: none;">返回上级</button>
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
        </div>
    </div>
@endsection

@section('script')
    @can('system.permission')
        <script>
            layui.use(['layer','treeTable','form'],function () {
                var layer = layui.layer,treeTable = layui.treeTable;

                var dataTable = treeTable.render({
                    elem: '#dataTable',
                    url: "{{ route('admin.data') }}",
                    where:{model:"permission"},
                    tree: {
                        iconIndex: 1,
                        isPidData: true,
                        idName: 'id',
                        pidName: 'parent_id'
                    },
                    cols: [[
                        {type: 'checkbox',fixed: true}
                        ,{field: 'name', title: '权限名称'}
                        ,{field: 'display_name', title: '显示名称', templet: function (d) {
                                return '<i class="layui-icon '+d.icon+'"> '+ d.display_name +' </i>';
                            },width:180}
                        ,{field: 'route', title: '路由' }
                        ,{field: 'created_at', title: '创建时间',width:200}
                        ,{field: 'updated_at', title: '更新时间',width:200}
                        ,{fixed: 'right', width: 230, align:'center', toolbar: function (d) {
                                var tier = 3, layIndex = d.LAY_INDEX.split('-');
                                var html = '<div class="layui-btn-group">';
                                @can('system.permission.create')
                                if(layIndex.length === 1 && tier > 1) {
                                    html += '<a class="layui-btn layui-btn-sm layui-btn-normal" lay-event="addChild"><i class="layui-icon layui-icon-addition"></i></a>';
                                }else if(layIndex.length === 2 && tier > 2) {
                                    html += '<a class="layui-btn layui-btn-sm layui-btn-normal" lay-event="addChild"><i class="layui-icon layui-icon-addition"></i></a>';
                                }else if(layIndex.length === 3 && tier > 3) {
                                    html += '<a class="layui-btn layui-btn-sm layui-btn-normal" lay-event="addChild"><i class="layui-icon layui-icon-addition"></i></a>';
                                }
                                @endcan
                                    @can('system.permission.edit')
                                    html +=  '<a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>';
                                @endcan
                                    @can('system.permission.destroy')
                                    html += '<a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">删除</a>';
                                @endcan
                                    html += '</div>'
                                return html;
                            }}
                    ]]
                });

                //监听工具条
                treeTable.on('tool(dataTable)', function(obj){
                    var data = obj.data
                        ,layEvent = obj.event;
                    if(layEvent === 'del'){
                        layer.confirm('确认删除吗？', function(index){
                            $.post("{{ route('admin.permission.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                                if (result.code===0){
                                    obj.del();
                                }
                                layer.close(index);
                                layer.msg(result.msg,{icon:6});
                            });
                        });
                    } else if(layEvent === 'edit'){
                        active.openLayerForm('{{Request()->getbaseUrl()}}/admin/permission/'+data.id+'/edit','编辑权限');
                    } else if (layEvent === 'addChild'){
                        active.openLayerForm('{{Request()->getbaseUrl()}}/admin/permission/create?parentid='+data.id,'添加子权限');
                    }
                });

                //按钮批量删除
                $("#listDelete").click(function () {
                    layer.msg("由于权限重要性，系统已禁止批量删除",{icon:5});
                });

            });
        </script>
    @endcan
@endsection
