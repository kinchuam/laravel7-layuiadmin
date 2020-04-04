@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                @can('content.article.category.create')
                    <a class="layui-btn layui-btn-sm" onclick="active.openLayerForm('{{route('admin.article.category.create')}}','添加分类',{'width':'50%','height':'50%'});">添 加</a>
                @endcan
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('content.article.category.edit')
                        <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    @can('content.article.category.destroy')
                        <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">删除</a>
                    @endcan
                </div>
            </script>
        </div>
    </div>
@endsection

@section('script')
    @can('content.category')
        <script>
            layui.use(['layer','table'],function () {
                var layer = layui.layer,table = layui.table;
                var dataTable = table.render({
                    elem: '#dataTable'
                    ,url: "{{ route('admin.article.category.data') }}"
                    ,page: true
                    ,cols: [[
                        {checkbox: true,fixed: true}
                        ,{field: 'id', title: 'ID', sort: true,width:90}
                        ,{field: 'sort', title: '排序',width:90}
                        ,{field: 'name', title: '名称'}
                        ,{field: 'created_at', title: '创建时间',width:200}
                        ,{field: 'updated_at', title: '更新时间',width:200}
                        ,{fixed: 'right', width: 190, align:'center', toolbar: '#options'}
                    ]]
                });

                //监听工具条
                table.on('tool(dataTable)', function(obj){
                    var data = obj.data,layEvent = obj.event;
                    if(layEvent === 'del'){
                        layer.confirm('确认删除吗？', function(index){
                            $.post("{{ route('admin.article.category.destroy') }}",{_method:'delete',ids:data.id},function (result) {
                                if (result.code==0){
                                    obj.del();
                                }
                                layer.close(index);
                                layer.msg(result.msg);
                            });
                        });
                    } else if(layEvent === 'edit'){
                        active.openLayerForm('{{Request()->getbaseUrl()}}/admin/category/'+data.id+'/edit','编辑分类',{'width':'50%','height':'50%'});
                    }
                });

            })
        </script>
    @endcan
@endsection
