@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-form layui-card-body">
            <div class="layui-form-item">

                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <select name="method" lay-search>
                            <option value="">Method</option>
                            @foreach($methods as $method)
                                <option value="{{ $method }}">{{ $method }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input type="text" name="path" placeholder="Path" class="layui-input" >
                    </div>
                </div>
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input type="text" name="ip" placeholder="Ip" class="layui-input" >
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
                    @can('logs.access.show')
                        <a class="layui-btn layui-btn-sm" lay-event="show" title="查看"><i class="fa fa-eye"></i></a>
                    @endcan
                </div>
            </script>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['opTable','table','form','okLayer'],function () {
            let form = layui.form,
                opTable = layui.opTable,
                table = layui.table,
                okLayer = layui.okLayer,
                dataTable = opTable.render({
                    elem: '#dataTable'
                    ,url: "{{ route('admin.access.data') }}"
                    ,page: true
                    ,cols: [[
                        {field: 'ip', title: 'Ip地址'}
                        ,{field: 'method', title: '请求方式', width:90, templet: function (it) {
                                return '<span class="layui-btn layui-btn-xs" style="background-color: '+it.method_color+';">'+it.method+'</span>'
                            }}
                        ,{field: 'path', title: '请求地址'}
                        ,{field: 'platform_browser', title: '系统信息', width: 250, templet:function (d) {
                                return d.platform +' '+ d.browser;
                            }}
                        ,{field: 'created_at', title: '创建时间', width: 180}
                        ,{fixed: 'right', width: 100, align:'center', toolbar: '#options'}
                    ]]
                    , openCols: [{
                        field: 'input', title: '数据', templet: function (it) {
                            if (!it.input) { return "无数据" }
                            return "<pre class='layui-code' >" + JSON.stringify(JSON.parse(it.input), null, 2) + "</pre>";
                        }
                    }]
                });

            table.on('tool(dataTable)', function(obj){
                let data = obj.data, layEvent = obj.event;
                if(layEvent === 'show'){
                    okLayer.open('详情', getRouteUrl('admin/access/'+data.id+'/show'), {btn: false, width: '70%', height: '60%'});
                }
            });

            form.on('submit(searchBtn)', function(data){
                dataTable.reload({
                    where:data.field,
                    page:{curr:1}
                })
            });
        });
    </script>
@endsection
