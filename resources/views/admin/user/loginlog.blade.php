@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-form  layui-card-header layuiadmin-card-header-auto" lay-filter="layadmin-userfront-formlist">
            <div class="layui-form-item">

                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <select name="uuid">
                            <option value="">用户</option>
                            @foreach($users as $user)
                                <option value="{{$user->uuid}}">{{$user->name}}</option>
                            @endforeach
                        </select>
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
        </div>
    </div>
@endsection

@section('script')
    @can('system.user')
        <script>
            layui.use(['table','form'],function () {
                var table = layui.table,form = layui.form;

                var dataTable = table.render({
                    elem: '#dataTable'
                    ,url: "{{ route('admin.user.loginlogdata') }}"
                    ,page: true
                    ,cols: [[
                        {field: 'name', title: '用户',templet: function (d) {return (d.user?d.user.name:'');}}
                        ,{field: 'ip', title: 'Ip地址'}
                        ,{field: 'system_browser', title: '信息'}
                        ,{field: 'message', title: '说明'}
                        ,{field: 'created_at', title: '创建时间'}
                    ]]
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
