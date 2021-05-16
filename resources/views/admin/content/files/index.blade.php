@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">
                <div class="layui-input-inline">
                    <input type="text" name="keywords" id="keywords" placeholder="文件名" class="layui-input">
                </div>
                <div class="layui-input-inline" style="margin-top: -4px;">
                    <button class="layui-btn" lay-submit lay-filter="searchBtn">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="layui-card-body">
            <div class="layui-btn-group">
                @can('content.files.destroy')
                    <a class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete"><i class="layui-icon layui-icon-delete"></i> 删除</a>
                @endcan
                @can('content.files.create')
                    <a class="layui-btn layui-btn-sm" id="addBtn"><i class="layui-icon layui-icon-add-circle"></i> 上传</a>
                @endcan
                @can('content.files.recycle')
                    <a class="layui-btn layui-btn-sm layui-btn-normal" id="recycleBtn"><i class="fa fa-recycle"></i> 回收站</a>
                @endcan
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    <a class="layui-btn layui-btn-sm layui-btn-normal copy" lay-event="copy" data-clipboard-text="@{{ d.file_url }}" title="复制"><i class="fa fa-copy"></i></a>
                    <a class="layui-btn layui-btn-sm" lay-event="download" title="下载"><i class="fa fa-download"></i></a>
                    @can('content.files.destroy')
                        <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del"><i class="layui-icon layui-icon-delete"></i></a>
                    @endcan
                </div>
            </script>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{asset('static/admin/js/clipboard.min.js')}}"></script>
    <script>
        layui.use(['flow','table','okLayer','form'], function () {
            let $ = layui.$, flow = layui.flow, table = layui.table, okLayer = layui.okLayer, form = layui.form;

            let dataTable = table.render({
                elem: '#dataTable'
                ,url: "{{ route('admin.content.files.data') }}"
                ,page: true
                ,cols: [[
                    {checkbox: true, fixed: true}
                    ,{field: 'filename', title: '文件名'}
                    ,{field: 'type', title: '类型', width: 100, event:'preview', templet: function (d) {
                            let str = d.type, html = '<a href="JavaScript:;" title="点击查看"> \n';
                            if (str.indexOf("image") !== -1 ) {
                                html += '<img lay-src="'+ d.file_url +'" style="vertical-align:middle;" alt="'+d.filename+'" width="28" height="28" onerror="this.src=\'{{asset("static/admin/img/nopic.png")}}\'">\n';
                            }else{
                                html += '<img src="{{asset("static/admin/img/ico")}}/'+d.suffix+'.png" style="vertical-align:middle;" alt="'+d.filename+'" width="28" height="28" onerror="this.src=\'{{asset("static/admin/img/nopic.png")}}\'">\n';
                            }
                            html += '</a>';
                            return html;
                        }}
                    ,{field: 'size', title: '文件大小', width: 120, templet:function (d) { return GetFileSize(d.size); }}
                    ,{field: 'storage', title: '储存方式', width: 100}
                    ,{field: 'created_at', title: '创建时间', width: 180}
                    ,{fixed: 'right', width: 180, align:'center', toolbar: '#options'}
                ]]
                ,done: function () {
                    flow.lazyimg();
                }
            });

            $("#addBtn").on('click', function () {
                okLayer.open('上传文件', '{{route('admin.content.files.create')}}', {btn: false, width: '80%'});
            });
            $("#recycleBtn").on('click', function () {
                okLayer.open('回收站', '{{route('admin.content.files.recycle')}}', {btn: false, full: true});
            });
            //监听工具条
            table.on('tool(dataTable)', function(obj){
                let data = obj.data, layEvent = obj.event;
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                        $.post("{{ route('admin.content.files.destroy') }}", {_method:'delete',ids:[data.id]}, function (result) {
                            if (result.code == 0){
                                obj.del();
                            }
                            layer.close(index);
                            layer.msg(result.message);
                        });
                    });
                }else if(layEvent === 'preview'){
                    if(data.path){
                        if(data.type.indexOf("video") !== -1){
                            layer.open({
                                type: 2,
                                title: false,
                                area: ['530px', '360px'],
                                shade: 0.8,
                                closeBtn: 0,
                                shadeClose: true,
                                content: data.file_url
                            });
                        }else if (data.type.indexOf("image") !== -1){
                            layer.photos({
                                photos: {
                                    title: "查看",
                                    data: [{ src: data.file_url }]
                                },
                                shade: .01,
                                closeBtn: 1,
                                anim: 5
                            });
                        }
                    }
                } else if (layEvent === 'copy'){
                    let clipboard = new ClipboardJS('.copy');
                    clipboard.on('success', function(e) {
                        layer.msg('复制成功');
                        clipboard.destroy();
                        e.clearSelection();
                    });
                } else if (layEvent === 'download'){
                    let index = layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                    if (download(data.file_url, data.filename)) {
                        layer.close(index);
                    }
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
                if (ids.length > 0){
                    layer.confirm('确认删除吗？', function(index){
                        layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                        $.post("{{ route('admin.content.files.destroy') }}",{_method:'delete',ids:ids},function (result) {
                            if (result.code == 0){
                                dataTable.reload();
                            }
                            layer.close(index);
                            layer.msg(result.message, {icon:1});
                        });
                    })
                    return true;
                }
                layer.msg('请选择删除项', {icon:5})
            });

            form.on('submit(searchBtn)', function(data){
                dataTable.reload({
                    where:data.field,
                    page:{curr:1}
                })
            });
        })
    </script>
@endsection
