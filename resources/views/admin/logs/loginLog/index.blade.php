@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-form layui-card-body">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <select name="username">
                            <option value="">管理员</option>
                            @foreach($users as $ke => $na)
                                <option value="{{ $na }}">{{ $na }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input type="text" name="ip" placeholder="关键词搜索" class="layui-input" >
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
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['table','form'], function () {
            let table = layui.table, form = layui.form;
            let dataTable = table.render({
                elem: '#dataTable'
                ,url: "{{ route('admin.loginLog.data') }}"
                ,page: true
                ,cols: [[
                    {field: 'username', title: '管理员'}
                    ,{field: 'ip', title: 'Ip地址'}
                    ,{field: 'platform_browser', title: '系统信息', width:230, templet:function (d) {
                            return d.platform +' '+ d.browser;
                        }}
                    ,{field: 'message', title: '描述'}
                    ,{field: 'created_at', title: '创建时间', width:190}
                ]]
            });
            //搜索
            form.on('submit(searchBtn)', function(data){
                dataTable.reload({
                    where:data.field,
                    page:{curr:1}
                })
            });
        });
    </script>
@endsection
