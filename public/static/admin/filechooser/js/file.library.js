var FileLibrary = function(options){
    // 配置项
    var defaults = {
        type: options.type||'image'
        , layerId: 'file-library'
        , layerSkin: 'file-library'
    };

    this.options = $.extend({}, defaults, options);
    // 容器元素
    this.$element = null;
    this.uploadInst = null;
    this.init();
};

FileLibrary.prototype = {
    init: function () {
        var _this = this;
        _this.laytpl = _this.options.laytpl;
        // 打开文件库事件
        _this.triggerEvent();
    },
    triggerEvent: function () {
        var _this = this;
        // 点击开启文件库弹窗
        _this.showLibraryModal();
    },

    showLibraryModal: function () {
        var _this = this;
        _this.initModal($('body'));
    },

    initModal: function (element) {
        var _this = this;
        _this.$element = element;
        _this.renderFileList();
        // 注册分组下拉选择组件
        _this.selectDropdown();
        // 注册文件上传事件
        _this.uploadImagesEvent();
        // 注册文件点击选中事件
        _this.selectFilesEvent();
        // 注册分类切换事件
        _this.switchClassEvent();
        // 新增分组事件
        _this.addGroupEvent();
        // 编辑分组事件
        _this.editGroupEvent();
        // 删除分组事件
        _this.deleteGroupEvent();
        // 注册文件删除事件
        _this.deleteFilesEvent();
        // 注册文件移动事件
        _this.moveFilesEvent();
        // 注册网络图片上传事件
        _this.uploadNetworkImagesEvent();
    },

    /**
     * 注册分组下拉选择组件
     */
    selectDropdown: function () {
        this.$element.find('.group-select').dropdown();
    },
    /**
     * 新增分组事件
     */
    addGroupEvent: function () {
        var _this = this
            , $groupList = _this.$element.find('.file-group > ul');
        _this.$element.on('click', '.group-add', function () {
            layer.prompt({title: '请输入新分组名称'}, function (value, index) {
                var load = layer.load();
                $.post(_this.options.AddGroup, {
                    group_name: value,
                    group_type: _this.options.type
                }, function (result) {
                    layer.msg(result.msg);
                    if (result.code === 1) {
                        _this.laytpl(_this.$element.find('#tpl-group-item').html()).render(result.data, function(html){
                            $groupList.append(html);
                        });
                        var $groupSelectList = _this.$element.find('.group-select > .group-list');
                        $groupSelectList.append(
                            '<li>' +
                            '    <a class="move-file-group" data-group-id="' + result.data.group_id + '"' +
                            '       href="javascript:void(0);">' + result.data.group_name + '</a>' +
                            '</li>'
                        );
                    }
                    layer.close(load);
                });
                layer.close(index);
            });
        });
    },
    /**
     * 编辑分组事件
     */
    editGroupEvent: function () {
        var _this = this;
        _this.$element.find('.file-group').on('click', '.group-edit', function () {
            var $li = $(this).parent()
                , group_id = $li.data('group-id');
            layer.prompt({title: '修改分组名称', value: $li.attr('title')}, function (value, index) {
                var load = layer.load();
                $.post(_this.options.EditGroup, {
                    group_id: group_id
                    , group_name: value
                }, function (result) {
                    layer.msg(result.msg);
                    if (result.code === 1) {
                        $li.attr('title', value).find('.group-name').text(value);
                        var $groupSelectList = _this.$element.find('.group-select > .group-list');
                        $groupSelectList.find('[data-group-id="' + group_id + '"]').text(value);
                    }
                    layer.close(load);
                });
                layer.close(index);
            });
            return false;
        });
    },
    /**
     * 删除分组事件
     */
    deleteGroupEvent: function () {
        var _this = this;
        _this.$element.find('.file-group').on('click', '.group-delete', function () {
            var $li = $(this).parent()
                , group_id = $li.data('group-id');
            layer.confirm('确定删除该分组吗？', {title: '友情提示'}, function (index) {
                var load = layer.load();
                $.post(_this.options.DeleteGroup, {
                    group_id: group_id
                }, function (result) {
                    layer.msg(result.msg);
                    if (result.code === 1) {
                        $li.remove();
                        var $groupSelectList = _this.$element.find('.group-select > .group-list');
                        $groupSelectList.find('[data-group-id="' + group_id + '"]').remove();
                    }
                    layer.close(load);
                });
                layer.close(index);
            });
            return false;
        });
    },

    /**
     * 删除选中的文件
     */
    deleteFilesEvent: function () {
        var _this = this;
        _this.$element.on('click', '.file-delete', function () {
            var fileIds = _this.getSelectedFileIds(),that = this;
            if (fileIds.length === 0) {
                layer.msg('您还没有选择任何文件~', {offset: 't', anim: 6});
                return;
            }
            layer.confirm('确定删除选中的文件吗？', {title: '友情提示'}, function (index) {
                var load = layer.load();
                $.post(_this.options.DeleteFiles, {
                    "_method": "delete",
                    ids: fileIds
                }, function (result) {
                    layer.close(load);
                    if (result.code === 0) {
                        layer.msg(result.msg);
                        _this.$element.find('.file-list-item > li.active').each(function (index) {
                            $(this).remove();
                        });
                        //_this.renderFileList();
                    }
                });
                layer.close(index);
            });
        });
    },

    /**
     * 文件上传 (单、多文件)
     */
    uploadImagesEvent: function () {
        var _this = this, loadIndex = null;
        layui.use(['upload'],function () {
            _this.uploadInst = layui.upload.render({
                elem: '.j-upload'
                , url: _this.options.FileUpload
                , acceptMime: 'image/jpg,image/jpeg,image/png'
                , field: 'iFile'
                , data: {"filetype": 'image', "group_id": _this.getCurrentGroupId(), "_token": _this.options.csrftoken}
                , multiple: true
                , before: function (obj) {
                    loadIndex = layer.load();
                }
                , done: function (res) {
                    //如果上传失败
                    layer.close(loadIndex);
                    if (res.code > 0) {
                        layer.msg(res.msg);
                        return false;
                    }
                    var $list = _this.$element.find('ul.file-list-item');
                    _this.laytpl(_this.$element.find('#tpl-file-list-item').html()).render({
                        data: res.data,
                        choseimg: _this.options.choseimg
                    }, function (html) {
                        $list.prepend(html);
                    });

                    layer.msg(res.msg);
                }
            });
        });
    },
    /**
     * 文件移动事件
     */
    moveFilesEvent: function () {
        var _this = this
            , $groupSelect = _this.$element.find('.group-select');
        // 绑定文件选中事件
        $groupSelect.on('click', '.move-file-group', function () {
            $groupSelect.dropdown('close');
            var groupId = $(this).data('group-id')
                , fileIds = _this.getSelectedFileIds();
            if (fileIds.length === 0) {
                layer.msg('您还没有选择任何文件~', {offset: 't', anim: 6});
                return false;
            }
            layer.confirm('确定移动选中的文件吗？', {title: '友情提示'}, function (index) {
                var load = layer.load();
                $.post(_this.options.MoveFiles, {
                    group_id: groupId
                    , fileIds: fileIds
                }, function (result) {
                    layer.msg(result.msg);
                    if (result.code === 1) {
                        _this.renderFileList();
                    }
                    layer.close(load);
                });
                layer.close(index);
            });
        });
    },

    /**
     * 获取选中的文件的ID集
     * @returns {Array}
     */
    getSelectedFileIds: function () {
        var fileList = this.getSelectedFiles();
        var data = [];
        fileList.forEach(function (item) {
            data.push(item.file_id);
        });
        return data;
    },
    /**
     * 获取选中的文件列表
     * @returns {Array}
     */
    getSelectedFiles: function () {
        var selectedList = [];
        this.$element.find('.file-list-item > li.active').each(function (index) {
            var $this = $(this);
            selectedList[index] = {
                file_id: $this.data('file-id')
                , file_path: $this.data('file-path')
            };
        });
        return selectedList;
    },

    /**
     * 重新渲染文件列表
     * @param page
     */
    renderFileList: function (page) {
        var _this = this , groupId = this.getCurrentGroupId();
        // 重新渲染文件列表
        _this.getJsonData({type: _this.options.type,group_id: groupId, page: page || 1}, function (data) {
            _this.laytpl(_this.$element.find('#tpl-file-list').html()).render({data:data,choseimg:_this.options.choseimg}, function(html){
                _this.$element.find('#file-list-body').html(html);
            });
        });
    },

    getCurrentGroupId: function () {
        return this.$element.find('.file-group > ul > li.active').data('group-id');
    },

    /**
     * 注册文件选中事件
     */
    selectFilesEvent: function () {
        // 绑定文件选中事件
        this.$element.find('#file-list-body').on('click', '.file-list-item li', function () {
            $(this).toggleClass('active');
        });
    },

    /**
     * 获取文件库列表数据
     * @param params
     * @param success
     */
    getJsonData: function (params, success) {
        var _this = this,loadIndex = layer.load();
        typeof params === 'function' && (success = params);
        // 获取文件库列表
        $.get(this.options.FileList,params,function (result) {
            layer.close(loadIndex);
            if (result.code === 0) {
                typeof success === 'function' && success(result.data);
                // 注册文件列表分页事件
                _this.fileListPage(result);
            } else {
                layer.msg(result.msg, {anim: 6});
            }
        })
    },

    /**
     * 分类切换事件
     */
    switchClassEvent: function () {
        var _this = this;
        // 注册分类切换事件
        _this.$element.find('.file-group').on('click', 'li', function () {
            var $this = $(this);
            // 切换选中状态
            $this.addClass('active').siblings('.active').removeClass('active');
            // 重新渲染文件列表
            _this.renderFileList();
            _this.uploadInst.reload({
                data: {"filetype": 'image', "group_id": _this.getCurrentGroupId(), "_token": _this.options.csrftoken}
            })
        });
    },
    /**
     * 注册文件列表分页事件
     */
    fileListPage: function (data) {
        var _this = this;
        layui.use(['laypage'],function () {
            layui.laypage.render({
                limit: data.limit
                ,elem: 'imagepage'
                ,count: data.count
                ,curr: data.page
                ,jump: function(obj, first){
                    if(!first){
                        _this.renderFileList(obj.curr);
                    }
                }
            });
        });
    },

    uploadNetworkImagesEvent: function () {
        var _this = this;
        _this.$element.on('click', '.j-upload-network', function () {
            layer.prompt({title: '输入图片地址', formType: 2}, function(value, index, elem){
                layer.load(2);
                $.post(_this.options.FileUpload+'?upload_type=NetworkToLocal',{url:value,group_id:_this.getCurrentGroupId()},function (res) {
                    if (res.code > 0) {
                        layer.msg(res.msg);
                        return false;
                    }
                    var $list = _this.$element.find('ul.file-list-item');
                    _this.laytpl(_this.$element.find('#tpl-file-list-item').html()).render({
                        data: res.data,
                        choseimg: _this.options.choseimg
                    }, function (html) {
                        $list.prepend(html);
                    });

                    layer.msg(res.msg);
                    layer.close(index);
                    layer.closeAll('loading');
                });
            });
        });
    },

};

$.fileLibrary = function (options) {
    new FileLibrary(options);
};
