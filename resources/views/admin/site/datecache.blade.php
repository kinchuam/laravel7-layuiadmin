@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header">更新缓存</div>

        <div class="layui-card-body" pad15="">
            <form class="layui-form" action="{{route('admin.site.clearcache')}}" method="post">
                {{csrf_field()}}
                {{method_field('put')}}

                <div class="layui-form-item" pane="">
                    <label class="layui-form-label">缓存类型</label>
                    <div class="layui-input-block">
                        <input type="checkbox" name="type[cache]" checked title="数据缓存">
                        <input type="checkbox" name="type[config]" checked title="配置文件缓存">
                        <input type="checkbox" name="type[view]" checked title="视图缓存">
                    </div>
                </div>
                @can('config.site.clearcache')
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="submit" class="layui-btn" lay-submit="" lay-filter="formDemo">提 交</button>
                    </div>
                </div>
                @endcan
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form'], function(){
            var form = layui.form;
        });
    </script>
@endsection
