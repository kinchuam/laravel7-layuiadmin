"use strict";
layui.define(["layer",'flow'], function (exports) {
    let $ = layui.$, flow = layui.flow, nopic = layui.cache.base+'../admin/img/nopic.png';
    let okLayer = {

        multi_image: function(ob) {
            let that = ob.elem,
                name = ob.name,
                content = ob.content,
                multiple = ob.multiple,
                limit = ob.limit,
                is_page = $(".layui-fluid").hasClass('fromPage');

            let obj = {
                type: 2
                , title: '图片库'
                , area:  ['850px','584px']
                , offset: 'auto'
                , anim: 1
                , closeBtn: 1
                , shade: 0.3
                , shadeClose: true
                , btn: ['确定', '取消']
                , btnAlign: 'c'
                , scrollbar: false
                , resize: false
                , content: content
                , yes: function (index, layero) {
                    let body = layer.getChildFrame('body', index)
                        , iframeWindow = window[layero.find('iframe')[0]['name']]
                        , $imagesList = $(that).next('.input-group').find('.layui-upload-box');

                    if (is_page) {
                        iframeWindow = (layero).find("iframe")[0].contentWindow;
                    }
                    let data = iframeWindow.layui.Library.getSelFiles();
                    // 新增图片列表
                    if (data.length <= 0) {
                        if (is_page) {
                            parent.layer.msg('请选择图片');return true;
                        }
                        layer.msg('请选择图片');return true;
                    }
                    let list = multiple ? data : [data[0]], html = '';
                    if (limit > 0 && (parseInt($imagesList.find('li').length) + parseInt(data.length)) > limit) {
                        if (is_page) {
                            parent.layer.msg('图片数量不能大于' + limit + '张', {anim: 6});return true;
                        }
                        layer.msg('图片数量不能大于' + limit + '张', {anim: 6}); return false;
                    }
                    layui.each(list, function (index, item) {
                        html += '<li>\n  <img src="'+item.file_url+'" onerror="this.src=\''+nopic+'\'" alt="">\n  <i class="layui-icon layui-icon-delete icon-delete" id="picDelBtn" ></i>\n';
                        if (multiple) {
                            html += ' <input type="hidden" name="'+name+'[]" value="'+item.file_path+'">\n';
                        }else{
                            html += ' <input type="hidden" name="'+name+'" value="'+item.file_path+'">\n';
                        }
                        html += ' </li>\n';
                    })
                    let $html = $(html);
                    $html.find('img').on('click', function(){
                        layer.photos({
                            photos: { title: "查看", data: [{ src: $(this).attr('src') }] },
                            shade: .01,
                            closeBtn: 1,
                            anim: 5
                        });
                    });
                    $html.find('#picDelBtn').on('click', function () {
                        okLayer.del_image(this);
                    });
                    // 渲染html
                    if (multiple) {
                        $imagesList.append($html);
                        if (is_page) {
                            parent.layer.close(index);
                            return true;
                        }
                        layer.close(index);
                        return true;
                    }
                    $imagesList.html($html);
                    $(that).prev().val(data[0].file_path)
                    if (is_page) {
                        parent.layer.close(index);
                        return true;
                    }
                    layer.close(index);
                }
            }
            if (is_page) {
                parent.layer.open(obj);
                return true;
            }
            layer.open(obj);
        },

        UploadFileTpl: function (ob) {
            if (!ob.elem) { return; }
            let that = $(ob.elem),
                name = $(that).data('name'),
                multiple = $(that).data('multiple') || false,
                limit = $(that).data('limit') || 5,
                data = ob.value,
                content = ob.content || getRouteUrl('admin/files/getFiles'),
                htmlTpl;

            if (multiple) {
                htmlTpl = '<button type="button" class="layui-btn uploadBtn">多图片上传</button>\n' +
                    '      <blockquote class="input-group layui-elem-quote layui-quote-nm" style="margin-top: 10px;">\n' +
                    '            <ul class="layui-clear layui-upload-box"></ul>\n' +
                    '      </blockquote>';
            }else {
                htmlTpl = '<input type="text" class="layui-input" name="'+name+'"  value="'+data+'" style="width: 65%;float: left;">\n' +
                    '      <button type="button" class="layui-btn uploadBtn">上传图片</button>\n' +
                    '      <div class="input-group">\n' +
                    '           <ul class="layui-upload-box"></ul>\n' +
                    '      </div>';
            }
            let $htmlTpl = $(htmlTpl);
            $(that).append($htmlTpl);
            $(that).find(".uploadBtn").on('click', function () {
                okLayer.multi_image({elem:this, name, content, multiple, limit});
            });
            if (!data) { return; }
            let list = multiple ? JSON.parse(data) : [data], uploadTpl = '';
            layui.each(list, function (index, item) {
                let st_url = getRouteUrl('storage/') + item;
                if ((item.substr(0, 2) == '//') || (item.substr(0, 7) == 'http://') || (item.substr(0, 8) == 'https://')) {
                    st_url = item;
                }
                uploadTpl += ' <li>\n  <img lay-src="'+ st_url +'" onerror="this.src=\''+ nopic +'\'" alt="">\n <i class="layui-icon layui-icon-delete icon-delete" id="picDelBtn"></i>\n';
                if (multiple) {
                    uploadTpl += ' <input type="hidden" name="'+ name +'[]" value="'+ item +'">\n';
                }
                uploadTpl += ' </li>\n';
            })
            $(that).find('.input-group .layui-upload-box').append(uploadTpl);
            $(that).find('img').on('click', function(){
                layer.photos({
                    photos: { title: "查看", data: [{ src: $(this).attr('src') }] },
                    shade: .01,
                    closeBtn: 1,
                    anim: 5
                });
            });
            $(that).find("ul li #picDelBtn").on('click', function () {
                okLayer.del_image(this);
            });
            flow.lazyimg();
        },

        del_image: function(that) {
            $(that).parents(".layui-upload").find("input").val("");
            $(that).parent().remove();
        },
        /**
         * confirm()函数二次封装
         * @param content
         * @param yesFunction
         */
        confirm: function (content, yesFunction) {
            let options = {skin: okLayer.skinChoose(), icon: 3, title: "提示", anim: okLayer.animChoose()};
            layer.confirm(content, options, yesFunction);
        },
        /**
         * open()函数二次封装,支持在table页面和普通页面打开
         * @param title
         * @param content
         * @param data
         * @param successFunction
         * @param endFunction
         */
        open: function (title, content, data= {}, successFunction, endFunction) {
            let defaults = {full: false, btn: true, width: '55%', height: '55%'};
            data = Object.assign({}, defaults, data);
            let d = {
                title: title,
                type: 2,
                maxmin: true,
                shade: 0,
                shadeClose: false,
                anim: okLayer.animChoose(5),
                area: [data.width, data.height],
                content: content,
                //zIndex: layer.zIndex,
                //skin: okLayer.skinChoose(),
                success: successFunction,
                end: endFunction
            };
            if (data.id && data.id > 0) {
                d.id = data.id;
            }
            if (data.btn){
                d.btn =  ['确认', '取消'];
                d.btnAlign = 'c';
                d.yes =  function (index, layero) {
                    layero.find('iframe').contents().find("#formDemo").click();
                };
            }
            let index = layer.open(d);
            if (data.full){
                layer.full(index);
            }
        },
        /**
         * msg()函数二次封装
         */
        // msg弹窗默认消失时间
        time: 1000,
        // 绿色勾
        greenTickMsg: function (content, callbackFunction) {
            let options = {icon: 1, time: okLayer.time, anim: okLayer.animChoose()};
            layer.msg(content, options, callbackFunction);
        },
        // 红色叉
        redCrossMsg: function (content, callbackFunction) {
            let options = {icon: 2, time: okLayer.time, anim: okLayer.animChoose()};
            layer.msg(content, options, callbackFunction);
        },
        // 黄色问号
        yellowQuestionMsg: function (content, callbackFunction) {
            let options = {icon: 3, time: okLayer.time, anim: okLayer.animChoose()};
            layer.msg(content, options, callbackFunction);
        },
        // 灰色锁
        grayLockMsg: function (content, callbackFunction) {
            let options = {icon: 4, time: okLayer.time, anim: okLayer.animChoose()};
            layer.msg(content, options, callbackFunction);
        },
        // 红色哭脸
        redCryMsg: function (content, callbackFunction) {
            let options = {icon: 5, time: okLayer.time, anim: okLayer.animChoose()};
            layer.msg(content, options, callbackFunction);
        },
        // 绿色笑脸
        greenLaughMsg: function (content, callbackFunction) {
            let options = {icon: 6, time: okLayer.time, anim: okLayer.animChoose()};
            layer.msg(content, options, callbackFunction);
        },
        // 黄色感叹号
        yellowSighMsg: function (content, callbackFunction) {
            let options = {icon: 7, time: okLayer.time, anim: okLayer.animChoose()};
            layer.msg(content, options, callbackFunction);
        },
        /**
         * 皮肤选择
         * @returns {string}
         */
        skinChoose: function (kin) {
            let storage = window.localStorage;
            let skin = kin || storage.getItem("skin");
            if (skin == 1) {
                // 灰白色
                return "";
            } else if (skin == 2) {
                // 墨绿色
                return "layui-layer-molv";
            } else if (skin == 3) {
                // 蓝色
                return "layui-layer-lan";
            } else if (!skin || skin == 4) {
                // 随机颜色
                const skinArray = ["", "layui-layer-molv", "layui-layer-lan"];
                return skinArray[Math.floor(Math.random() * skinArray.length)];
            }
        },
        /**
         * 动画选择
         * @returns {number}
         */
        animChoose: function (nim) {
            let storage = window.localStorage;
            let anim = nim || storage.getItem("anim");
            let animArray = ["0", "1", "2", "3", "4", "5", "6"];
            if (animArray.indexOf(anim) > -1) {
                // 用户选择的动画
                return parseInt(anim);
            } else if (!anim || anim == 7) {
                // 随机动画
                return Math.floor(Math.random() * animArray.length);
            }
        }
    }
    exports("okLayer", okLayer);
});
