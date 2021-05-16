@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-btn-group ">
                @can('system.permission.create')
                    <a class="layui-btn layui-btn-sm" id="addBtn" ><i class="layui-icon layui-icon-add-circle"></i> 添加</a>
                @endcan
                <a class="layui-btn layui-btn-sm layui-btn-primary" id="btnExpandAll">展开全部</a>
                <a class="layui-btn layui-btn-sm layui-btn-primary" id="btnFoldAll">折叠全部</a>
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['treeTable', 'okLayer'],function () {
            let $ = layui.$
                , treeTable = layui.treeTable
                , okLayer = layui.okLayer
                , dataTable = treeTable.render({
                elem: '#dataTable',
                url: "{{ route('admin.permission.data') }}",
                tree: {
                    iconIndex: 0,
                    isPidData: true,
                    idName: 'id',
                    pidName: 'parent_id'
                },
                cols: [[
                    {field: 'name', title: '权限名称'}
                    ,{field: 'display_name', title: '显示名称' , templet: function (d) {
                            return '<i class="layui-icon '+d.icon+'"> '+ d.display_name +' </i>';
                        }}
                    ,{field: 'route', title: '路由' }
                    ,{field: 'created_at', title: '创建时间', width:180}
                    ,{fixed: 'right', width: 200, align:'center', toolbar: function (d) {
                            let tier = 3, layIndex = d.LAY_INDEX.split('-'), addChild = false;
                            let html = '<div class="layui-btn-group">';
                            @can('system.permission.create')
                            if(layIndex.length === 1 && tier > 1) {
                                addChild = true;
                            }else if(layIndex.length === 2 && tier > 2) {
                                addChild = true;
                            }else if(layIndex.length === 3 && tier > 3) {
                                addChild = true;
                            }
                            if (addChild) {
                                html += '<a class="layui-btn layui-btn-sm layui-btn-normal" lay-event="addChild" title="添加子权限"><i class="layui-icon layui-icon-addition"></i></a>';
                            }
                            @endcan
                                @can('system.permission.edit')
                                html += '<a class="layui-btn layui-btn-sm" lay-event="edit" title="编辑"><i class="layui-icon layui-icon-edit"></i></a>';
                            @endcan
                                @can('system.permission.destroy')
                                html += '<a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del" title="删除"><i class="layui-icon layui-icon-delete"></i></a>';
                            @endcan
                                html += '</div>';
                            return html;
                        }}
                ]]
                , done:function () {
                    dataTable.expand(2);
                }
            });

            $("#addBtn").on('click', function (){
                okLayer.open('添加权限', '{{route('admin.permission.create')}}', {height:'80%'});
            });
            //监听工具条
            treeTable.on('tool(dataTable)', function(obj){
                let data = obj.data ,layEvent = obj.event;
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                        $.post("{{ route('admin.permission.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                            if (result.code===0){
                                obj.del();
                            }
                            layer.close(index);
                            layer.msg(result.message, {icon:6});
                        });
                    });
                } else if(layEvent === 'edit'){
                    okLayer.open('编辑权限', getRouteUrl('admin/permission/'+data.id+'/edit'), {height:'80%'});
                } else if (layEvent === 'addChild'){
                    okLayer.open('添加子权限', "{{route('admin.permission.create')}}?parent_id="+data.id, {height:'80%'});
                }
            });
            $("#btnExpandAll").on('click', function () {
                dataTable.expandAll();
            });

            $("#btnFoldAll").on('click', function () {
                dataTable.foldAll();
            });

        });
    </script>
@endsection
