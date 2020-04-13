@extends('admin.base')

@section('content')
    <style>
        .opTable-open-item-div{
            margin-right: 20px;
        }
    </style>
    <div class="layui-card">
        <div class="layui-form  layui-card-header layuiadmin-card-header-auto" lay-filter="layadmin-userfront-formlist">
            <div class="layui-form-item">

                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <select name="method" >
                            <option value="">用户</option>
                            @if(isset($users))
                                @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->name}}</option>
                                @endforeach
                            @endif
                        </select>
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
    @can('logs.operation')
        <script>
            layui.use(['opTable','form'],function () {
                var opTable = layui.opTable,form = layui.form;

                var dataTable = opTable.render({
                    elem: '#dataTable'
                    ,url: "{{ route('admin.operation.data') }}"
                    ,page: true
                    ,cols: [[
                        {field: 'username', title: '用户', templet: function (d) {
                                return (d.user.name||'未知用户');
                            }}
                        ,{field: 'log_name', title: '位置'}
                        ,{field: 'description', title: '描述'}
                        ,{field: 'subject_type',title:'操作模型'}
                        ,{field: 'created_at', title: '创建时间'}
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
