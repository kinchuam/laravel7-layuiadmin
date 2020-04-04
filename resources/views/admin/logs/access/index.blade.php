@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-form  layui-card-header layuiadmin-card-header-auto" lay-filter="layadmin-userfront-formlist">
            <div class="layui-form-item">

                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <select name="method" id="method" lay-search>
                            <option value="">Method</option>
                            @foreach($methods as $method)
                                <option value="{{$method}}">{{$method}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input type="text" name="path" id="path" placeholder="Path" class="layui-input" >
                    </div>
                </div>
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input type="text" name="ip" id="ip" placeholder="Ip" class="layui-input" >
                    </div>
                </div>

                <div class="layui-inline" style="margin-top: -4px;">
                    <button class="layui-btn" id="searchBtn" lay-submit="" lay-filter="searchBtn">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    <a class="layui-btn layui-btn-sm" lay-event="show">查看</a>
                </div>
            </script>

        </div>
    </div>
@endsection

@section('script')
    @can('logs.access')
        <script>
            layui.use(['layer','opTable','form','table'],function () {
                var layer = layui.layer,opTable = layui.opTable,form = layui.form,table = layui.table;

                var dataTable = opTable.render({
                    elem: '#dataTable'
                    ,url: "{{ route('admin.access.data') }}"
                    ,page: true
                    ,cols: [[
                        {field: 'ip', title: 'Ip地址'}
                        ,{field: 'method', title: '请求方式',templet: function (it) {
                                return '<span class="layui-btn layui-btn-xs" style="background-color: '+it.method_color+';">'+it.method+'</span>'
                            },width:90}
                        ,{field: 'path', title: '请求地址'}
                        ,{field: 'platform', title: '系统'}
                        ,{field: 'browser', title: '浏览器'}
                        ,{field: 'created_at', title: '创建时间'}
                        ,{fixed: 'right', width: 180, align:'center', toolbar: '#options'}
                    ]]
                    , openCols: [{
                        field: 'code', title: '数据', templet: function (it) {
                            if (!it.code) {
                                return "暂无示例代码"
                            }
                            return "<pre class='layui-code' >" + JSON.stringify(JSON.parse(it.code), null, 2) + "</pre>";
                        }
                    }]
                });

                //监听工具条
                table.on('tool(dataTable)', function(obj){
                    var data = obj.data
                        ,layEvent = obj.event;
                    if(layEvent === 'del'){
                        layer.confirm('确认删除吗？', function(index){
                            $.post("{{ route('admin.access.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                                if (result.code==0){
                                    obj.del();
                                }
                                layer.close(index);
                                layer.msg(result.msg);
                            });
                        });
                    }else if(layEvent==='show'){
                        active.openLayerForm('{{Request()->getbaseUrl()}}/admin/access/'+data.id+'/show','详情',{'btn':false,'width':'70%','height':'60%'});
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
