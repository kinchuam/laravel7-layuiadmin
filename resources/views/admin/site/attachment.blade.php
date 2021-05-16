@extends('admin.base')

@section('content')
    <style>
        .tag-item, .input-new-tag, .button-new-tag{
            margin: 4px 0 0 10px;
            margin-bottom: 4px !important;
        }
    </style>
    <div class="layui-card">
        <div class="layui-card-header">
            <h3>附件设置</h3>
        </div>
        <blockquote class="layui-elem-quote layui-quote-nm">
            <p><i class="layui-icon layui-icon-about"></i> 当前 PHP 环境允许最大单个上传文件大小为: {{ini_get('upload_max_filesize')}}</p>
            <p><i class="layui-icon layui-icon-about"></i> 当前 PHP 环境允许最大 POST 表单大小为: {{ini_get('post_max_size')}}</p>
        </blockquote>
        <div class="layui-card-body" pad15="">
            <form class="layui-form layui-form-pane" action="{{route('admin.site.update')}}" method="post" lay-filter="example">
                {{method_field('put')}}
                <input type="hidden" name="siteKey" value="{{$siteKey}}">
                <div class="layui-form-item">
                    <label class="layui-form-label">空间容量</label>
                    <div class="layui-input-inline" style="width: 150px;">
                        <input type="number" name="attachment_limit" lay-verify="number" value="" class="layui-input">
                    </div>
                    <div class="layui-input-inline layui-input-company">M 容量单位为M, 设置为 0 时不限制空间, 提示：1 M = 1024 KB</div>
                </div>

                <div class="layui-form-item" pane="">
                    <label class="layui-form-label">存储方式</label>
                    <div class="layui-input-block">
                        <input type="radio" name="storage" lay-filter="storage" value="local" title="默认本地" checked >
                        <input type="radio" name="storage" lay-filter="storage" value="qiniu" title="七牛云存储" >
                    </div>
                </div>

                <fieldset class="layui-elem-field">
                    <legend>图片附件设置</legend>
                    <div class="layui-field-box">
                        <div class="layui-form-item" pane="">
                            <label class="layui-form-label">文件后缀</label>
                            <div class="layui-input-block">
                                <div class="layui-btn-container tag" lay-filter="image_type" lay-allowclose="true" lay-newTag="true" id="image_type">
                                    @foreach($config['image_type'] as $k => $a)
                                        <button lay-id="{{ $k }}" data-text="{{ $a }}" type="button" class="tag-item">{{ $a }}</button>
                                    @endforeach
                                </div>
                                <input type="hidden" name="image_type" >
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">文件大小</label>
                            <div class="layui-input-inline" style="width: 150px;">
                                <input type="number" name="image_size" lay-verify="number" value="" class="layui-input">
                            </div>
                            <div class="layui-input-inline layui-input-company">KB 提示：1 M = 1024 KB</div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="layui-elem-field">
                    <legend>音频视频附件设置</legend>
                    <div class="layui-field-box">
                        <div class="layui-form-item" pane="">
                            <label class="layui-form-label">文件后缀</label>
                            <div class="layui-input-block">
                                <div class="layui-btn-container tag" lay-filter="file_type" lay-allowclose="true" lay-newTag="true" id="file_type">
                                    @foreach($config['file_type'] as $k => $a)
                                        <button lay-id="{{ $k }}" data-text="{{ $a }}" type="button" class="tag-item">{{ $a }}</button>
                                    @endforeach
                                </div>
                                <input type="hidden" name="file_type" >
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">文件大小</label>
                            <div class="layui-input-inline" style="width: 150px;">
                                <input type="number" name="file_size" lay-verify="number" value="" class="layui-input">
                            </div>
                            <div class="layui-input-inline layui-input-company">KB 提示：1 M = 1024 KB</div>
                        </div>
                    </div>
                </fieldset>

                @can('config.attachment.update')
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
        layui.use(['form','tag'], function(){
            let $ = layui.$, form = layui.form, tag = layui.tag,
                file_type = $("#file_type").find('.tag-item'),
                image_type = $("#image_type").find('.tag-item'),
                file_types = [],
                image_types = [];

            form.val('example', {
                "attachment_limit": "{{isset($config['attachment_limit'])?$config['attachment_limit']:old('attachment_limit',0)}}"
                ,"storage": "{{isset($config['storage'])?$config['storage']:old('storage')}}"
                ,"image_size": "{{isset($config['image_size'])?$config['image_size']:old('image_size',2048)}}"
                ,"file_size": "{{isset($config['file_size'])?$config['file_size']:old('file_size',2048)}}"
            });

            tag.set({
                skin: 'layui-btn layui-btn-primary layui-btn-sm',
                tagText: '<i class="layui-icon layui-icon-add-1"></i>添加后缀'
            });

            if (file_type.length > 0) {
                layui.each(file_type, function(index, item){
                    file_types.push(del_html_tags($(item).html()));
                });
                $("input[name=file_type]").val(file_types.join('|'));
            }
            if (image_type.length > 0) {
                layui.each(image_type, function(index, item){
                    image_types.push(del_html_tags($(item).html()));
                });
                $("input[name=image_type]").val(image_types.join('|'));
            }

            tag.on('add(file_type)', function (data) {
                let that = $(data.elem),othis = $(data.othis);
                file_types.push(del_html_tags(othis.text()));
                that.nextAll("input").val(file_types.join('|'));
            });
            tag.on('delete(file_type)', function (data) {
                let that = $(data.elem);
                file_types.splice(data.index,1);
                that.nextAll("input").val(file_types.join('|'));
            });
            tag.on('add(image_type)', function (data) {
                let that = $(data.elem),othis = $(data.othis);
                image_types.push(del_html_tags(othis.text()));
                that.nextAll("input").val(image_types.join('|'));
            });
            tag.on('delete(image_type)', function (data) {
                let that = $(data.elem);
                image_types.splice(data.index,1);
                that.nextAll("input").val(image_types.join('|'));
            });
        });
    </script>
@endsection
