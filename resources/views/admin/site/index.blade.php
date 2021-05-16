@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h3>站点配置</h3>
        </div>
        <div class="layui-card-body">
            <form class="layui-form layui-form-pane" action="{{route('admin.site.update')}}" method="post" lay-filter="example" >
                {{method_field('put')}}
                <input type="hidden" name="siteKey" value="{{$siteKey}}">
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">站点名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="webname" value="" placeholder="请输入标题" class="layui-input" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">站点标题</label>
                    <div class="layui-input-block">
                        <input type="text" name="title" value="" placeholder="请输入标题" class="layui-input" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">站点关键词</label>
                    <div class="layui-input-block">
                        <input type="text" name="keywords" value="" placeholder="请输入关键词" class="layui-input" >
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">站点描述</label>
                    <div class="layui-input-block">
                        <textarea class="layui-textarea" name="description"  rows="5"></textarea>
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">CopyRight</label>
                    <div class="layui-input-block">
                        <textarea class="layui-textarea" name="copyright"  rows="8"></textarea>
                    </div>
                </div>

                @can('config.site.update')
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

@section('script')
    <script>
        layui.use(['form'], function () {
            let  form = layui.form;
            form.val('example', {
                "webname": "{{isset($config['webname'])?$config['webname']:old('webname')}}"
                ,"title": "{{isset($config['title'])?$config['title']:old('title')}}"
                ,"keywords": "{{isset($config['keywords'])?$config['keywords']:old('keywords')}}"
                ,"description": "{{isset($config['description'])?$config['description']:old('description')}}"
                ,"copyright": "{{isset($config['copyright'])?$config['copyright']:old('copyright')}}"
            });
        })
    </script>
@endsection
