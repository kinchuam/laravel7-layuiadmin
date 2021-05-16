@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-form layui-card-body">
            <div class="layui-form-item">

                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <select name="causer_id" >
                            <option value="">管理员</option>
                            @if(!empty($users))
                                @foreach($users as $user)
                                    <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
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
    <script>
        layui.use(['opTable','form'],function () {
            let form = layui.form
                , opTable = layui.opTable
                , dataTable = opTable.render({
                elem: '#dataTable'
                ,url: "{{ route('admin.operation.data') }}"
                ,page: true
                ,cols: [[
                    {field: 'username', title: '管理员', width: 180, templet: function (d) { return (d.user?d.user.name:'未知'); }}
                    ,{field: 'description', title: '描述'}
                    ,{field: 'subject_type',title: '模型', width: 180}
                    ,{field: 'created_at', title: '创建时间', width: 190}
                ]]
                ,openCols: [
                    {field: 'properties', title: '数据', templet: function (it) {
                            if (!it.properties) {  return "无数据"  }
                            return "<pre class='layui-code'>" + JSON.stringify(JSON.parse(it.properties), null, 2) + "</pre>";
                        }}
                ]
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
