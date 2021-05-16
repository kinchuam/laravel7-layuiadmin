"use strict";
layui.link(layui.cache.base+'plugins/fileLibrary/fileLibrary.css');
layui.define(["layer","laytpl","upload","laypage","dropdown"], function (exports) {
    let $ = layui.$, laytpl = layui.laytpl, upload = layui.upload, laypage = layui.laypage, dropdown = layui.dropdown
        , choseimg = layui.cache.base+'plugins/fileLibrary/img/chose.png'
        , pageLimit = 12
        , Library = function () { };

    let tpl_library = '<div id="file-library" class="layui-layer-content">\n' +
        '                <div class="file-group layui-panel">\n' +
        '                    <ul class="nav-new layui-menu">\n' +
        '                        <li class="ng-scope layui-menu-item-checked" data-group-id="-1">\n' +
        '                            <div class="layui-menu-body-title" >全部</div>\n' +
        '                        </li>\n' +
        '                        <li class="ng-scope" data-group-id="0">\n' +
        '                            <div class="layui-menu-body-title" >未分组</div>\n' +
        '                        </li>\n' +
        '                        {{#  layui.each(d.data, function(index, item){ }}' +
        '                            <li class="ng-scope" data-group-id="{{item.id}}">\n' +
        '                                <div class="group-edit"> <i class="layui-icon layui-icon-edit"></i></div>\n' +
        '                                <div class="group-name layui-elip layui-menu-body-title">{{item.title}}</div>\n' +
        '                                <div class="group-delete"><i class="layui-icon layui-icon-close"></i></div>\n' +
        '                            </li>\n' +
        '                        {{#  }); }}' +
        '                    </ul>\n' +
        '                    <a class="group-add layui-btn layui-btn-sm" >新增分组</a>\n' +
        '                </div>\n' +
        '                <div class="file-list">\n' +
        '                    <div class="layui-card-body">\n' +
        '                        <div class="layui-btn-group">\n' +
        '                            <button type="button" class="child-file-group layui-btn layui-btn-sm layui-btn-normal" >\n' +
        '                                移动至 <span class="layui-icon layui-icon-triangle-d" ></span>\n' +
        '                            </button>\n' +
        '                            <button type="button" class="file-delete layui-btn layui-btn-sm layui-btn-danger" >\n' +
        '                                <i class="layui-icon layui-icon-delete"></i> 删除\n' +
        '                            </button>\n' +
        '                        </div>\n' +
        '                        <div class="layui-btn-group" style="float:right;">\n' +
        '                            <button type="button" class="layui-btn layui-btn-sm layui-btn-normal j-upload">\n' +
        '                                <i class="layui-icon layui-icon-add-circle-fine"></i> 上传\n' +
        '                            </button>\n' +
        '                            <button type="button" class="layui-btn layui-btn-sm j-upload-network">\n' +
        '                                <i class="layui-icon layui-icon-add-circle-fine"></i> 网络图片\n' +
        '                            </button>\n' +
        '                        </div>\n' +
        '                    </div>\n' +
        '                    <div id="file-list-body" class="v-box-body">\n' +
'                                 <ul class="file-list-item"> </ul>\n' +
        '                         <div id="imagepage"></div>\n' +
        '                    </div>\n' +
        '                </div>\n' +
        '            </div>'
        , tpl_file_list = '{{#  layui.each(d.data, function(index, item){ }}' +
        '                <li class="ng-scope" title="{{item.filename}}" data-file-id="{{item.id}}" data-file-path="{{item.file_path}}">\n' +
        '                    <div class="img-cover" style="background-image: url(\'{{item.file_url}}\')"> </div>\n' +
        '                    <p class="file-name layui-elip">{{item.filename}}</p>\n' +
        '                    <div class="select-mask"> <img src="{{d.choseimg}}" alt=""> </div>\n' +
        '                </li>\n' +
        '                {{#  }); }}'
        , tpl_file_list_item = '<li class="ng-scope" title="{{d.file_name}}" data-file-id="{{d.file_id}}" data-file-path="{{d.file_path}}">\n' +
        '                <div class="img-cover" style="background-image: url(\'{{d.file_url}}\')"> </div>\n' +
        '                <p class="file-name layui-elip">{{d.file_name}}</p>\n' +
        '                <div class="select-mask"> <img src="{{d.choseimg}}" alt=""> </div>\n' +
        '            </li>'
        , tpl_group_item = '<li class="ng-scope" data-group-id="{{d.group_id}}">\n' +
        '        <div class="group-edit"> <i class="layui-icon layui-icon-edit"></i></div>\n' +
        '        <div class="group-name layui-elip layui-menu-body-title">{{d.group_name}}</div>\n' +
        '        <div class="group-delete"><i class="layui-icon layui-icon-close"></i></div>\n' +
        '    </li>';

    Library.prototype.render = function(options){
        if (!options.elem) {return;}
        let defaults = {
                type: 'image', csrftoken: $('meta[name="csrf-token"]').attr('content')
            }, uploadInst;
        options = $.extend({}, defaults, options);
        options.GroupList = JSON.parse(options.GroupList) || [];
        let $element = $(options.elem);
        let annex = {
            init: function () {
                laytpl(tpl_library).render({
                    data: options.GroupList
                }, function(html){
                    $element.html(html);
                });
                // 注册列表事件
                annex.renderFileList();
                // 注册分组下拉选择组件
                annex.selectDropdown();
                // 注册文件上传事件
                annex.uploadImagesEvent();
                // 注册文件点击选中事件
                annex.selectFilesEvent();
                // 注册分类切换事件
                annex.switchClassEvent();
                // 新增分组事件
                annex.addGroupEvent();
                // 编辑分组事件
                annex.editGroupEvent();
                // 删除分组事件
                annex.deleteGroupEvent();
                // 注册文件删除事件
                annex.deleteFilesEvent();
                // 注册网络图片上传事件
                annex.uploadNetworkImagesEvent();
            },
            renderFileList: function (page) {
                // 重新渲染文件列表
                annex.getJsonData({
                    type: options.type,
                    group_id: annex.getCurrentGroupId(),
                    page: page || 1,
                }, function (data) {
                    laytpl(tpl_file_list).render({
                        data, choseimg
                    }, function(html){
                        $element.find('#file-list-body .file-list-item').html(html);
                    });
                });
            },
            getCurrentGroupId: function () {
                return $element.find('.file-group > ul > li.active').data('group-id');
            },
            selectDropdown: function () {
                dropdown.render({
                    elem: '.child-file-group'
                    ,data: options.GroupList
                    ,style: 'overflow:auto;overflow-x: hidden; max-height: 243px;'
                    ,click: function(obj){
                        let groupId = obj.id, fileIds = annex.getSelectedFileIds();
                        if (fileIds.length <= 0) {
                            layer.msg('您还没有选择任何文件~', {offset: 't', anim: 6});
                            return false;
                        }
                        layer.confirm('确定移动选中的文件吗？', {title: '友情提示'}, function (index) {
                            layer.load();
                            $.post(options.MoveFiles, { group_id: groupId, fileIds }, function (result) {
                                layer.msg(result.message);
                                if (result.code == 1) {
                                    annex.renderFileList();
                                }
                            });
                            layer.closeAll();
                        });
                    }
                });
            },
            uploadImagesEvent: function () {
                let loadIndex, group_id = annex.getCurrentGroupId();
                group_id = group_id < 0 ? 0 : group_id;
                uploadInst = upload.render({
                    elem: '.j-upload'
                    , url: options.FileUpload
                    , acceptMime: 'image/*'
                    , field: 'iFile'
                    , data: {"filetype": options.type, group_id, "_token": options.csrftoken}
                    , multiple: true
                    , before: function (obj) {
                        loadIndex = layer.load(2);
                    }
                    , done: function (res) {
                        //如果上传失败
                        layer.close(loadIndex);
                        if (res.code > 0) {
                            layer.msg(res.message); return false;
                        }
                        annex.renderFileList();
                        layer.msg(res.message);
                    }
                });
            },
            selectFilesEvent: function () {
                // 绑定文件选中事件
                $element.find('#file-list-body').on('click', '.file-list-item li', function () {
                    $(this).toggleClass('active');
                });
            },
            switchClassEvent: function () {
                let group_id = annex.getCurrentGroupId();
                // 注册分类切换事件
                $element.find('.file-group').on('click', 'li', function () {
                    let $this = $(this);
                    // 切换选中状态
                    $this.addClass('active').siblings('.active').removeClass('active');
                    // 重新渲染文件列表
                    annex.renderFileList();
                    uploadInst.reload({
                        data: {"filetype": options.type, group_id, "_token": options.csrftoken}
                    })
                });
            },
            addGroupEvent: function () {
                let $groupList = $element.find('.file-group > ul');
                $element.on('click', '.group-add', function () {
                    layer.prompt({title: '请输入分组名称'}, function (value, index) {
                        let load = layer.load(2);
                        $.post(options.AddGroup, {
                            group_name: value, group_type: options.type
                        }, function (result) {
                            layer.msg(result.message);
                            if (result.status == 'success') {
                                laytpl(tpl_group_item).render(result.data, function(html){
                                    $groupList.append(html);
                                });
                                options.GroupList.push({id: result.data.group_id, title: result.data.group_name});
                            }
                        });
                        layer.close(load);
                        layer.close(index);
                    });
                });
            },
            editGroupEvent: function () {
                $element.find('.file-group').on('click', '.group-edit', function () {
                    let $li = $(this).parent() , group_id = $li.data('group-id');
                    layer.prompt({title: '修改分组名称', value: $li.find('.group-name').text()}, function (value, index) {
                        let load = layer.load(2);
                        $.post(options.EditGroup, {
                            id: group_id, group_name: value, _method: 'put'
                        }, function (result) {
                            layer.msg(result.message);
                            if (result.status == 'success') {
                                $li.attr('title', value).find('.group-name').text(value);
                                layui.each(options.GroupList, function (index, item) {
                                    if (item.id == group_id) {
                                        options.GroupList[index].title = value;
                                        return true;
                                    }
                                });
                            }
                        });
                        layer.close(load);
                        layer.close(index);
                    });
                    return false;
                });
            },
            deleteGroupEvent: function () {
                $element.find('.file-group').on('click', '.group-delete', function () {
                    let $li = $(this).parent(), group_id = $li.data('group-id');
                    layer.confirm('确定删除该分组吗？', {title: '友情提示'}, function (index) {
                        let load = layer.load(2);
                        $.post(options.DeleteGroup, { _method:'delete', ids: [group_id] }, function (result) {
                            layer.msg(result.message);
                            if (result.code == 0) {
                                $li.remove();
                                layui.each(options.GroupList, function (index, item) {
                                    if (item.id == group_id) {
                                        options.GroupList.splice(index, 1);
                                        return true;
                                    }
                                });
                            }
                        });
                        layer.close(load);
                        layer.close(index);
                    });
                    return false;
                });
            },
            deleteFilesEvent: function () {
                $element.on('click', '.file-delete', function () {
                    let fileIds = annex.getSelectedFileIds();
                    if (fileIds.length == 0) {
                        layer.msg('您还没有选择任何文件~', {offset: 't', anim: 6}); return false;
                    }
                    layer.confirm('确定删除选中的文件吗？', {title: '友情提示'}, function (index) {
                        let load = layer.load(2);
                        $.post(options.DeleteFiles, {
                            "_method": "delete", ids: fileIds
                        }, function (result) {
                            layer.close(load);
                            if (result.code == 0) {
                                layer.msg(result.message);
                                annex.renderFileList();
                            }
                        });
                        layer.close(index);
                    });
                });
            },
            uploadNetworkImagesEvent: function () {
                let group_id = annex.getCurrentGroupId();
                $element.on('click', '.j-upload-network', function () {
                    layer.prompt({title: '输入图片地址', formType: 2}, function(value, index, elem){
                        let la = layer.load(2);
                        $.post(options.FileUpload+'?upload_type=NetworkToLocal', { url : value, group_id}, function (res) {
                            if (res.code > 0) {
                                layer.msg(res.message);return false;
                            }
                            annex.renderFileList();
                            layer.msg(res.message);layer.close(la);layer.close(index);
                        });
                    });
                });
            },
            fileListPage: function (data) {
                laypage.render({
                    limit: pageLimit
                    ,elem: 'imagepage'
                    ,count: data.count
                    ,curr: data.page
                    ,jump: function(obj, first){
                        if(!first){
                            annex.renderFileList(obj.curr);
                        }
                    }
                });
            },
            getJsonData: function (params, success) {
                let loadIndex = layer.load(2);
                typeof params === 'function' && (success = params);
                // 获取文件库列表
                params.limit = pageLimit;
                $.get(options.FileList, params, function (result) {
                    layer.close(loadIndex);
                    if (result.code == 0) {
                        typeof success === 'function' && success(result.data);
                        // 注册文件列表分页事件
                        result.page = params.page;
                        annex.fileListPage(result);return true;
                    }
                    layer.msg(result.message, {anim: 6});
                })
            },
            getSelectedFileIds: function () {
                let fileList = annex.getSelectedFiles(), data = [];
                fileList.forEach(function (item) {
                    data.push(item.file_id);
                });
                return data;
            },
            getSelectedFiles: function () {
                let selectedList = [];
                $element.find('.file-list-item > li.active').each(function (index) {
                    selectedList[index] = { file_id: $(this).data('file-id'), file_path: $(this).data('file-path') };
                });
                return selectedList;
            },
        };
        annex.init();
        return new Library();
    };

    Library.prototype.getSelFiles = function () {
        let selectedList = [];
        $(document).find('.file-list-item > li.active').each(function (index) {
            let $this = $(this), _bk = $this.find('.img-cover').css("background-image");
            selectedList[index] = {
                file_id: $this.data('file-id'),
                file_path: $this.data('file-path'),
                file_url: _bk.split("\"")[1],
            };
        });
        return selectedList;
    };

    exports("Library", new Library());
});
