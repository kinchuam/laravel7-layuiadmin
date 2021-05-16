@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header">
            <h3>清除缓存</h3>
        </div>

        <div class="layui-card-body" pad15="">
            <form class="layui-form layui-form-pane" action="{{route('admin.clearCache')}}" method="post">
                {{method_field('put')}}
                <div class="layui-form-item" pane="">
                    <label class="layui-form-label">类型</label>
                    <div class="layui-input-block">
                        <input type="checkbox" name="type[cache]" title="数据缓存">
                        <input type="checkbox" name="type[picture]" title="图片缓存">
                        <input type="checkbox" name="type[view]" title="视图缓存">
                        <input type="checkbox" name="type[route]" title="路由缓存">
                        <input type="checkbox" name="type[config]" title="配置缓存">
                    </div>
                </div>
                @can('config.clearCache')
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button type="button" class="layui-btn" lay-submit="" lay-filter="formDemo"><i class="layui-icon layui-icon-release"></i> 提 交</button>
                        </div>
                    </div>
                @endcan
            </form>
        </div>
    </div>
@endsection

