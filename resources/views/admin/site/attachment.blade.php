@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header">附件设置</div>
        <blockquote class="layui-elem-quote layui-quote-nm">
            <p><i class="layui-icon layui-icon-about"></i> 当前 PHP 环境允许最大单个上传文件大小为: {{ini_get('upload_max_filesize')}}</p>
            <p><i class="layui-icon layui-icon-about"></i> 当前 PHP 环境允许最大 POST 表单大小为: {{ini_get('post_max_size')}}</p>
        </blockquote>
        <div class="layui-card-body" pad15="">
            <form class="layui-form" action="{{route('admin.site.attachmentupdate')}}" method="post">
                {{csrf_field()}}
                {{method_field('put')}}
                <input type="hidden" name="sitekey" value="{{$sitekey}}">

                <div class="layui-form-item">
                    <label class="layui-form-label">空间容量</label>
                    <div class="layui-input-inline" style="width: 100px;">
                        <input type="text" name="capacity" lay-verify="number" value="{{isset($config['capacity'])?$config['capacity']:0}}" class="layui-input">
                    </div>
                    <div class="layui-input-inline layui-input-company">M</div>
                    <div class="layui-form-mid layui-word-aux">容量单位为M, 设置为 0 时不限制空间, 提示：1 M = 1024 KB</div>
                </div>

                <div class="layui-form-item" pane="">
                    <label class="layui-form-label">存储方式</label>
                    <div class="layui-input-block">
                        <input type="radio" name="storage" lay-filter="storage" value="local" title="默认本地" @if(empty($config['storage'])||$config['storage']=='local') checked @endif>
                        <input type="radio" name="storage" lay-filter="storage" value="qiniu" title="七牛云存储" @if(isset($config['storage'])&&$config['storage']=='qiniu') checked @endif>
                    </div>
                </div>

                <fieldset class="layui-elem-field @if(empty($config['storage'])||$config['storage']=='local') layui-hide @endif" id="storage_type">
                    <legend>参数设置</legend>
                    <div class="layui-field-box @if(isset($config['storage'])&&$config['storage']=='qiniu') layui-show @else layui-hide @endif" id="storage_qiniu">
                        <div class="layui-form-item">
                            <label class="layui-form-label">Accesskey</label>
                            <div class="layui-input-block">
                                <input type="text" name="qiniu[accesskey]" value="{{isset($config['qiniu']['accesskey'])?$config['qiniu']['accesskey']:''}}" placeholder="请输入Accesskey" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">Secretkey</label>
                            <div class="layui-input-block">
                                <input type="text" name="qiniu[secretkey]" value="{{isset($config['qiniu']['secretkey'])?$config['qiniu']['secretkey']:''}}" placeholder="请输入Secretkey" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">Bucket</label>
                            <div class="layui-input-block">
                                <input type="text" name="qiniu[bucket]" value="{{isset($config['qiniu']['bucket'])?$config['qiniu']['bucket']:''}}" placeholder="请输入Bucket" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">Url</label>
                            <div class="layui-input-block">
                                <input type="text" name="qiniu[url]" value="{{isset($config['qiniu']['url'])?$config['qiniu']['url']:''}}" placeholder="请输入Url" class="layui-input">
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="layui-elem-field">
                    <legend>图片附件设置</legend>
                    <div class="layui-field-box">
                        <div class="layui-form-item">
                            <label class="layui-form-label">文件后缀</label>
                            <div class="layui-input-block">
                                {{--<div class="tags" id="tags">
                                    <input type="text" placeholder="空格生成属性"  id="image_type" value="" class="layui-input">
                                </div>--}}
                                <div class="layui-btn-container tag" lay-allowclose="true" lay-newTag="true" id="image_type">
                                    @foreach($config['image_type'] as $k => $a)
                                        <button lay-id="{{ $k }}" data-text="{{ $a }}" type="button"  class="tag-item">{{ $a }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">文件大小</label>
                            <div class="layui-input-inline" style="width: 100px;">
                                <input type="text" name="image_size" lay-verify="number" value="{{isset($config['image_size'])?$config['image_size']:old('image_size')}}" class="layui-input">
                            </div>
                            <div class="layui-input-inline layui-input-company">KB</div>
                            <div class="layui-form-mid layui-word-aux">提示：1 M = 1024 KB</div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="layui-elem-field">
                    <legend>音频视频附件设置</legend>
                    <div class="layui-field-box">
                        <div class="layui-form-item">
                            <label class="layui-form-label">文件后缀</label>
                            <div class="layui-input-block">
                                {{--<div class="tags" id="tags">
                                    <input type="text" value="" id="file_type" class="layui-input" placeholder="空格生成属性"  >
                                </div>--}}
                                <div class="layui-btn-container tag" lay-allowclose="true" lay-newTag="true" id="file_type">
                                    @foreach($config['file_type'] as $k => $a)
                                        <button lay-id="{{ $k }}" data-text="{{ $a }}" type="button"  class="tag-item">{{ $a }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">文件大小</label>
                            <div class="layui-input-inline" style="width: 100px;">
                                <input type="text" name="file_size" lay-verify="number" value="{{isset($config['file_size'])?$config['file_size']:old('file_size')}}" class="layui-input">
                            </div>
                            <div class="layui-input-inline layui-input-company">KB</div>
                            <div class="layui-form-mid layui-word-aux">提示：1 M = 1024 KB</div>
                        </div>
                    </div>
                </fieldset>

                @can('config.site.attachmentupdate')
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button type="button" class="layui-btn" lay-submit="" lay-filter="formDemo">提 交</button>
                        </div>
                    </div>
                @endcan
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form','tag'], function(){
            var form = layui.form ,tag = layui.tag;

            form.on('radio(storage)', function(data){
                if(data.value==='qiniu'){
                    $("#storage_type").removeClass('layui-hide').addClass('layui-show');
                    $("#storage_qiniu").removeClass('layui-hide').addClass('layui-show');
                }else if(data.value==='local'){
                    $("#storage_type").removeClass('layui-show').addClass('layui-hide');
                    $("#storage_qiniu").removeClass('layui-show').addClass('layui-hide');
                }
            });

            tag.set({
                tagText: '<i class="layui-icon layui-icon-add-1"></i>添加后缀' //标签添加按钮提示文本
            });
            var ftype = false;
            form.on('submit(formDemo)', function(data){

                var image_type = [],file_type = [],field = data.field; //获取提交的字段

                $("#image_type .tag-item").each(function(r,v) {
                    var str = $(v).text(),str1 = $(v).find('i').text();
                    image_type.push(str.replace(str1,''));
                });
                field.image_type = image_type.join('|');

                $("#file_type .tag-item").each(function(r,v) {
                    var str = $(v).text(),str1 = $(v).find('i').text();
                    file_type.push(str.replace(str1,''))
                });
                field.file_type = file_type.join('|');

                if(ftype){return false;}
                ftype = true;

                //提交 Ajax 成功后
                $.post($(this).parents("form").attr('action'),field,function (result) {
                    if (result.status === 'success') {
                        layer.msg(result.message, {time: 2000, icon: 6},function () {
                            window.location.reload()
                        })
                    } else {
                        layer.msg(result.message, {time: 3000, icon: 5})
                    }
                    ftype = false;
                });

            });

        });
    </script>
@endsection
