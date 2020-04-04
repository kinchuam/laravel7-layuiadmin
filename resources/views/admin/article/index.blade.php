@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header">筛选 </div>
        <div class="layui-form layui-card-header layuiadmin-card-header-auto" lay-filter="layadmin-userfront-formlist">
            <div class="layui-form-item">

                <div class="layui-input-inline">
                    <select name="category_id" lay-verify="required" id="category_id">
                        <option value="">请选择分类</option>
                        @foreach($categorys as $category)
                            <option value="{{ $category->id }}" >{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-input-inline">
                    <input type="text" name="title" id="title" placeholder="请输入文章标题" class="layui-input">
                </div>

                <div class="layui-input-inline" style="margin-top: -4px;">
                    <button class="layui-btn" id="searchBtn" lay-submit lay-filter="searchBtn">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>
    <div class="layui-card">

        <div class="layui-card-body">
            <div class="layui-btn-group " style="padding-bottom: 10px;">
                @can('content.article.destroy')
                    <button class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删 除</button>
                @endcan
                @can('content.article.create')
                    <a class="layui-btn layui-btn-sm" onclick="active.openLayerForm('{{route('admin.article.create')}}','添加文章',{'width':'90%','height':'90%'});">添 加</a>
                @endcan
                @can('content.article.recycle')
                    <a class="layui-btn layui-btn-sm layui-btn-normal" onclick="active.openLayerForm('{{route('admin.article.recycle')}}','回收站',{'btn':false,'width':'75%'});" >回收站</a>
                @endcan
            </div>

            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('content.article.edit')
                        <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    @can('content.article.destroy')
                        <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">删除</a>
                    @endcan
                </div>
            </script>
            <script type="text/html" id="thumb">
                <a href="JavaScript:;" title="点击查看"><img src="@{{d.thumb?d.thumb:''}}" alt="" width="30" height="30" onerror="this.src='{{asset('static/admin/img/nopic.png')}}'"> @{{ d.title }}</a>
            </script>
            <script type="text/html" id="statustpl">
                @can('content.article.edit')
                    <input type="checkbox" name="status" value="@{{d.id}}" lay-skin="switch" lay-text="已发布|待修改" lay-filter="status" @{{ d.status == 1 ? 'checked' : '' }}>
                @else
                    @{{ d.status == 1 ? '已发布' : '待修改' }}
                @endif
            </script>
        </div>
    </div>
@endsection

@section('script')
    @can('content.article')
        <script>
            layui.use(['layer','table','form'],function () {
                var layer = layui.layer,form = layui.form,table = layui.table;
                var dataTable = table.render({
                    elem: '#dataTable'
                    ,url: "{{ route('admin.article.data') }}"
                    ,page: true
                    ,limit:15
                    ,cols: [[
                        {checkbox: true,fixed: true}
                        ,{field: 'id', title: 'ID', sort: true,width:80}
                        ,{field: 'category', title: '分类',width:150,toolbar:'<div>@{{ d.category.name }}</div>'}
                        ,{field: 'thumb', title: '标题',toolbar:'#thumb',event:'preview' }
                        ,{field: 'click', title: '点击量',width:100}
                        ,{field: 'status', title: '状态',width:120, templet: '#statustpl', unresize: true}
                        ,{field: 'created_at', title: '创建时间',width:180}
                        ,{fixed: 'right', width: 180, align:'center', toolbar: '#options'}
                    ]]
                });

                //监听工具条
                table.on('tool(dataTable)', function(obj){
                    var data = obj.data
                        ,layEvent = obj.event;
                    if(layEvent === 'del'){
                        layer.confirm('确认删除吗？', function(index){
                            $.post("{{ route('admin.article.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                                if (result.code==0){
                                    obj.del();
                                }
                                layer.close(index);
                                layer.msg(result.msg);
                            });
                        });
                    }else if(layEvent === 'edit'){
                        active.openLayerForm('{{Request()->getbaseUrl()}}/admin/article/'+data.id+'/edit','编辑文章',{'width':'90%','height':'90%'});
                    } else if(layEvent === 'preview'){
                        if(data.thumb!=null){
                            layer.photos({
                                photos: {
                                    title: "查看",
                                    data: [{
                                        src: data.thumb
                                    }]
                                },
                                shade: .01,
                                closeBtn: 1,
                                anim: 5
                            });
                        }

                    }
                });

                form.on('switch(status)', function(obj){
                    $.post("{{ route('admin.article.status') }}",{status:obj.elem.checked?1:0,id:this.value},function (result) {
                        if (result.code===0){
                            layui.notice.success(result.message);
                            return;
                        }
                        layui.notice.error(result.message);
                    });
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
                            $.post("{{ route('admin.article.destroy') }}",{_method:'delete',ids:ids},function (result) {
                                if (result.code==0){
                                    dataTable.reload();
                                }
                                layer.close(index);
                                layer.msg(result.msg);
                            });
                        })
                    }else {
                        layer.msg('请选择删除项')
                    }
                });

                //搜索
                form.on('submit(searchBtn)', function(data){
                    dataTable.reload({
                        where:data.field,
                        page:{curr:1}
                    })
                });
            })
        </script>
    @endcan
@endsection
