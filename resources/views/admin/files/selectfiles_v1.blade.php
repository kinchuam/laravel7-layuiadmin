@extends('admin.base')

@section('content')
    <style>
        .layui-fluid{
            margin: 0 !important;
            padding: 0 !important;
        }
    </style>
    <link rel="stylesheet" href="{{asset('static/admin/filechooser/css/amazeui.min.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('static/admin/filechooser/css/app.css')}}" media="all">
    <!-- 文件库模板 -->
    <div class="file-library" >
        <div id="file-library" class="layui-layer-content" style="width: 835px;height: 485px;background: #fff;">
            <div class="row">
                <div class="file-group">
                    <ul class="nav-new">
                        <li class="ng-scope active" data-group-id="-1">
                            <a class="group-name am-text-truncate" href="javascript:void(0);" title="全部">全部</a>
                        </li>
                        <li class="ng-scope" data-group-id="0">
                            <a class="group-name am-text-truncate" href="javascript:void(0);" title="未分组">未分组</a>
                        </li>
                        @foreach($group_list as $item)
                        <li class="ng-scope" data-group-id="{{ $item['id'] }}" title="{{ $item['name'] }}">
                            <a class="group-edit" href="javascript:void(0);" title="编辑分组">
                                <i class="layui-icon layui-icon-edit"></i>
                            </a>
                            <a class="group-name am-text-truncate" href="javascript:void(0);">
                                {{ $item['name'] }}
                            </a>
                            <a class="group-delete" href="javascript:void(0);" title="删除分组">
                                <i class="layui-icon layui-icon-close"></i>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    <a class="group-add" href="javascript:void(0);">新增分组</a>
                </div>
                <div class="file-list">
                    <div class="v-box-header am-cf">
                        <div class="h-left am-fl am-cf">
                            <div class="am-fl">
                                <div class="group-select am-dropdown">
                                    <button type="button" class="am-btn am-btn-sm am-btn-secondary am-dropdown-toggle">
                                        移动至 <span class="layui-icon layui-icon-triangle-d" style="font-size: 1rem;"></span>
                                    </button>
                                    <ul class="group-list am-dropdown-content">
                                        <li class="am-dropdown-header">请选择分组</li>
                                        @foreach($group_list as $item)
                                        <li>
                                            <a class="move-file-group" data-group-id="{{ $item['id'] }}" href="javascript:void(0);">{{ $item['name'] }}</a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="am-fl tpl-table-black-operation">
                                <a href="javascript:void(0);" class="file-delete layui-btn layui-btn-sm layui-btn-normal" data-group-id="2">
                                    <i class="layui-icon layui-icon-delete"></i> 删除
                                </a>
                            </div>
                        </div>
                        <div class="h-rigth am-fr">
                            <button type="button" class="layui-btn layui-btn-sm layui-btn-normal j-upload">
                                <i class="layui-icon">&#xe608;</i> 上传
                            </button>
                            <button type="button" class="layui-btn layui-btn-sm j-upload-network">
                                <i class="layui-icon">&#xe608;</i> 上传网络图片
                            </button>
                        </div>
                    </div>
                    <div id="file-list-body" class="v-box-body"></div>
                    <div class="v-box-footer am-cf"></div>
                </div>
            </div>
        </div>

    </div>
    @verbatim
    <!-- 文件列表模板 -->
    <script id="tpl-file-list" type="text/template">
        <ul class="file-list-item">
            {{#  layui.each(d.data, function(index, item){ }}
            <li class="ng-scope" title="{{  item.filename }}" data-file-id="{{  item.id }}" data-file-path="{{  item.path }}">
                <div class="img-cover" style="background-image: url('{{  item.file_url }}')">
                </div>
                <p class="file-name am-text-center am-text-truncate">{{  item.filename }}</p>
                <div class="select-mask">
                    <img src="{{ d.choseimg }}" alt="">
                </div>
            </li>
            {{#  }); }}
        </ul>
        <div id="imagepage" style="float:right;"></div>
    </script>
    <!-- 文件列表模板 -->
    <script id="tpl-file-list-item" type="text/template">
        <li class="ng-scope" title="{{  d.data.file_name }}" data-file-id="{{  d.data.file_id }}" data-file-path="{{  d.data.file_path }}">
            <div class="img-cover" style="background-image: url('{{  d.data.file_url }}')">
            </div>
            <p class="file-name am-text-center am-text-truncate">{{  d.data.file_name }}</p>
            <div class="select-mask">
                <img src="{{ d.choseimg }}" alt="">
            </div>
        </li>
    </script>
    <!-- 分组元素-->
    <script id="tpl-group-item" type="text/template">
        <li class="ng-scope" data-group-id="{{ d.group_id }}" title="{{ d.group_name }}">
            <a class="group-edit" href="javascript:void(0);" title="编辑分组">
                <i class="iconfont icon-bianji"></i>
            </a>
            <a class="group-name am-text-truncate" href="javascript:void(0);">
                {{ d.group_name }}
            </a>
            <a class="group-delete" href="javascript:void(0);" title="删除分组">
                <i class="iconfont icon-shanchu1"></i>
            </a>
        </li>
    </script>
    @endverbatim

@endsection

@section('script')
    <script src="{{asset('static/admin/filechooser/js/amazeui.min.js')}}"></script>
    <script src="{{asset('static/admin/filechooser/js/file.library.js')}}"></script>

    <script>

        layui.use(['layer'],function () {
            $.fileLibrary({
                type : 'image',
                choseimg : "{{asset('static/admin/filechooser/img/chose.png')}}",
                csrftoken : $('meta[name="csrf-token"]').attr('content'),
                FileUpload : '{{route('FileUpload')}}',
                FileList : '{{route('admin.files.getFiles')}}',
                MoveFiles : '{{route('admin.files.moveFiles')}}',
                DeleteFiles : '{{route('admin.files.destroy')}}',
                AddGroup : '{{route('admin.files.addGroup')}}',
                EditGroup : '{{route('admin.files.editGroup')}}',
                DeleteGroup : '{{route('admin.files.deleteGroup')}}',
            });

        });

        function getdata() {
            var selectedList = [];
            $('body').find('.file-list-item > li.active').each(function (index) {
                var $this = $(this)
                    ,_bk = $this.find('.img-cover').css("background-image");

                selectedList[index] = {
                    file_id: $this.data('file-id')
                    , file_path: $this.data('file-path')
                    , file_url: _bk.split("\"")[1],
                };
            });
            return selectedList;
        }
    </script>
@endsection