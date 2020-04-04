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
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('system.permission')
                        <a class="layui-btn layui-btn-sm" lay-event="children">子权限</a>
                    @endcan
                    @can('system.permission.edit')
                        <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    @can('system.permission.destroy')
                        <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">删除</a>
                    @endcan
                </div>
            </script>
        </div>
    </div>
@endsection

@section('script')
    @can('system.permission')
    <script>
        layui.use(['layer','table','form'],function () {
            var layer = layui.layer,table = layui.table;

            var dataTable = table.render({
                elem: '#dataTable'
                ,url: "{{ route('admin.data') }}"
                ,where:{model:"permission"}
                ,page: true
                ,cols: [[
                    {checkbox: true,fixed: true}
                    ,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'name', title: '权限名称'}
                    ,{field: 'display_name', title: '显示名称'}
                    ,{field: 'route', title: '路由' }
                    ,{field: 'icon_id', title: '图标', width: 90, templet: function (d) {
                            return '<i class="layui-icon '+d.icon+'"></i>';
                        }}
                    ,{field: 'created_at', title: '创建时间',width:200}
                    ,{field: 'updated_at', title: '更新时间',width:200}
                    ,{fixed: 'right', width: 230, align:'center', toolbar: '#options'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){
                var data = obj.data
                    ,layEvent = obj.event;
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        $.post("{{ route('admin.permission.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                            if (result.code==0){
                                obj.del();
                            }
                            layer.close(index);
                            layer.msg(result.msg,{icon:6});
                        });
                    });
                } else if(layEvent === 'edit'){
                    var pid = $("#returnParent").attr("pid");
                    active.openLayerForm('{{Request()->getbaseUrl()}}/admin/permission/'+data.id+'/edit','编辑权限');
                } else if (layEvent === 'children'){
                    var pid = $("#returnParent").attr("pid");
                    if (data.parent_id!=0){
                        $("#returnParent").attr("pid",pid+'_'+data.parent_id);
                    }
                    $("#returnParent").show();
                    dataTable.reload({
                        where:{model:"permission",parent_id:data.id},
                        page:{curr:1}
                    })
                }
            });

            //按钮批量删除
            $("#listDelete").click(function () {
                layer.msg("由于权限重要性，系统已禁止批量删除",{icon:5});
            });
            //返回上一级
            $("#returnParent").click(function () {
                var pid = $(this).attr("pid");
                if (pid!='0'){
                    ids = pid.split('_');
                    parent_id = ids.pop();
                    $(this).attr("pid",ids.join('_'));
                    $("#returnParent").show();
                }else {
                    parent_id=pid;
                    $("#returnParent").hide();
                }
                dataTable.reload({
                    where:{model:"permission",parent_id:parent_id},
                    page:{curr:1}
                })
            });
        });
    </script>
    @endcan
@endsection
