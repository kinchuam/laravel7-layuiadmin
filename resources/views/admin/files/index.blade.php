@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group ">
                @can('content.files.destroy')
                    <button class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删 除</button>
                @endcan
                @can('content.files.create')
                    <a class="layui-btn layui-btn-sm" onclick="active.openLayerForm('{{route('admin.files.create')}}','上传文件',{'btn':false,'width':'75%'});">上 传</a>
                @endcan
                @can('content.files.recycle')
                    <a class="layui-btn layui-btn-sm layui-btn-normal" onclick="active.openLayerForm('{{route('admin.files.recycle')}}','回收站',{'btn':false,'width':'75%'});" >回收站</a>
                @endcan
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="icon">
                <i class="layui-icon @{{ d.icon.class }}"></i>
            </script>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    <a class="layui-btn layui-btn-sm layui-btn-normal copy" lay-event="copy" data-clipboard-text="@{{ d.path }}">复制链接</a>
                    <a class="layui-btn layui-btn-sm" lay-event="download">下载</a>
                    @can('content.files.destroy')
                        <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">删除</a>
                    @endcan
                </div>
            </script>
        </div>
    </div>
@endsection

@section('script')
    @can('content.files')
        <script src="https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js"></script>
        <script>

            layui.use(['layer','table','laytpl'],function () {
                var layer = layui.layer,table = layui.table,laytpl = layui.laytpl;

                laytpl.getfilesize = function (size=0) {
                    if (!size)
                        return 0;
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
                    ,where:{model:"files"}
                    ,page: true
                    ,cols: [[
                        {checkbox: true,fixed: true}
                        ,{field: 'filename', title: '文件名'}
                        ,{field: 'size', title: '文件大小',templet:function (d) { return layui.laytpl.getfilesize(d.size);},width: 120}
                        ,{field: 'storage', title: '储存方式',width: 100}
                        ,{field: 'type', title: '文件类型',width: 180, templet: function (d) {
                                var html = '<a href="JavaScript:;" title="点击查看">\n',str = d.type;
                                if ( str.indexOf("image") != -1 ){
                                    html += '<img src="'+d.path+'" style="vertical-align:middle;" alt="'+d.filename+'" width="28" height="28" onerror="this.src=\'{{asset('static/admin/img/nopic.png')}}\'"> '+d.type+' \n';
                                }else{
                                    html += '<img src="{{asset('static/admin/img/ico')}}/'+d.suffix+'.png" style="vertical-align:middle;" alt="'+d.filename+'" width="28" height="28" onerror="this.src=\'{{asset('static/admin/img/nopic.png')}}\'"> '+d.type+' \n';
                                }
                                html += '</a>';
                                return html;
                            },event:'preview'}
                        ,{field: 'created_at', title: '创建时间',width: 190}
                        ,{fixed: 'right', width: 230, align:'center', toolbar: '#options'}
                    ]]
                });

                //监听工具条
                table.on('tool(dataTable)', function(obj){
                    var data = obj.data
                        ,layEvent = obj.event;
                    if(layEvent === 'del'){
                        layer.confirm('确认删除吗？', function(index){
                            $.post("{{ route('admin.files.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                                if (result.code==0){
                                    obj.del();
                                }
                                layer.close(index);
                                layer.msg(result.msg);
                            });
                        });
                    }else if(layEvent === 'preview'){
                        if(data.path!=null){
                            if(data.type.indexOf("video") != -1){
                                layer.open({
                                    type: 2,
                                    title: false,
                                    area: ['630px', '360px'],
                                    shade: 0.8,
                                    closeBtn: 0,
                                    shadeClose: true,
                                    content: data.path
                                });
                            }else if (data.type.indexOf("image") != -1){
                                layer.photos({
                                    photos: {
                                        title: "查看",
                                        data: [{
                                            src: data.path
                                        }]
                                    },
                                    shade: .01,
                                    closeBtn: 1,
                                    anim: 5
                                });
                            }
                        }
                    } else if (layEvent === 'copy'){
                        var clipboard = new ClipboardJS('.copy');
                        clipboard.on('success', function(e) {
                            layer.msg('复制成功');
                            clipboard.destroy();  //解决多次弹窗
                            e.clearSelection();
                        });

                    } else if (layEvent === 'download'){
                        download(data.path,data.filename);
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
                            $.post("{{ route('admin.files.destroy') }}",{_method:'delete',ids:ids},function (result) {
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

            });
        </script>
    @endcan
@endsection
