@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h3>站点配置</h3>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('admin.site.update')}}" method="post">
                {{csrf_field()}}
                {{method_field('put')}}
                <input type="hidden" name="sitekey" value="{{$sitekey}}">
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">站点名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="webname" value="{{ isset($config['webname'])?$config['webname']:'' }}" lay-verify="required" lay-vertype="tips" placeholder="请输入标题" class="layui-input" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">站点标题</label>
                    <div class="layui-input-block">
                        <input type="text" name="title" value="{{ isset($config['title'])?$config['title']:'' }}" lay-verify="required" lay-vertype="tips" placeholder="请输入标题" class="layui-input" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">站点关键词</label>
                    <div class="layui-input-block">
                        <input type="text" name="keywords" value="{{ isset($config['keywords'])?$config['keywords']:'' }}" lay-verify="required" lay-vertype="tips" placeholder="请输入关键词" class="layui-input" >
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label for="" class="layui-form-label">站点描述</label>
                    <div class="layui-input-block">
                        <textarea class="layui-textarea" name="description"  rows="5">{{ isset($config['description'])?$config['description']:'' }}</textarea>
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label for="" class="layui-form-label">CopyRight</label>
                    <div class="layui-input-block">
                        <textarea class="layui-textarea" name="copyright"  rows="8">{{ isset($config['copyright'])?$config['copyright']:'' }}</textarea>
                    </div>
                </div>

                @can('config.site.update')
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="submit" class="layui-btn" lay-submit="" lay-filter="formDemo">确 认</button>
                    </div>
                </div>
                @endcan
            </form>
        </div>
    </div>
@endsection
