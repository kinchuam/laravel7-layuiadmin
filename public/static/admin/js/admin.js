;!function (win) {

    var active = function () {};

    active.prototype.openLayerForm = function (url = '', title = '信息', data={} ,formId = "#layer-form") {
        var defaults = {full:false,btn:true,width:'55%',height:'65%'};
        data = $.extend({}, defaults, data);
        var d = {
            type: 2
            ,title: title
            ,anim: 2
            ,shadeClose: true
            ,content: url
            ,area:[data.width ,data.height]
        };
        if (data.btn){
            d.btn =  ['确认', '取消'];
            d.yes =  function (index, layero) {
                var submit = layero.find('iframe').contents().find("#formDemo");
                submit.click();
            };
        }
        var index = layer.open(d);
        if (data.full){
            layer.full(index);
        }
    };

    active.prototype.multi_image = function (that,url)
    {
        parent.layer.open({
            type: 2
            , title: '图片库'
            , area:  ['850px','585px']
            , offset: 'auto'
            , anim: 1
            , closeBtn: 1
            , shade: 0.3
            , shadeClose: true
            , btn: ['确定', '取消']
            , scrollbar: false
            , content: url
            , yes: function (index,layero) {
                var iframeWindow = (layero).find("iframe")[0].contentWindow
                    ,nopic = $(that).data('nopic');
                var data = iframeWindow.getdata()
                    ,multiple = $(that).data('multiple') || false
                    ,limit = $(that).data('limit')
                    ,$imagesList = $(that).next('.input-group').find('.layui-upload-box')
                    ,default_pic = $(that).data('default_pic')
                    ,html = '';
                // 新增图片列表
                var list = multiple ? data : [data[0]];
                for (i in list) {
                    html += '<li> ' +
                        '<img src="'+list[i].file_url+'" onerror="this.src=\''+nopic+'\'" alt="'+list[i].file_path+'">' +
                        '<i class="layui-icon layui-icon-close-fill" data-default_pic="'+default_pic+'"  onclick="active.del_image(this)"></i>' ;
                    if (multiple) {
                        html += '<input type="hidden" name="'+$(that).data('name')+'[]" value="'+list[i].file_path+'">';
                    }
                    html += '</li>';
                }
                $html = $(html);
                if (limit > 0 && $imagesList.find('li').length + list.length > limit) {
                    parent.layer.msg('图片数量不能大于' + limit + '张', {anim: 6});
                    return false;
                }
                $html.find('img').click(function(){
                    parent.layer.open({
                        type: 2,
                        title: false,
                        area: ['50%', '50%'],
                        shade: 0.8,
                        closeBtn: 0,
                        shadeClose: true,
                        content: $(this).attr('src')
                    });
                });
                // 渲染html
                multiple ? $imagesList.append($html) : ($imagesList.html($html),$(that).prev().val(data[0].file_path));
                parent.layer.close(index);
            }
        });
    };

    active.prototype.del_image = function (that)
    {
        var default_pic = $(that).data('default_pic');
        $(that).prev().attr("src", default_pic);
        $(that).parents('.layui-upload').find("input").val("");
    };

    win.active = new active();
}(window);


/**
 * 获取 blob
 * @param  {String} url 目标文件地址
 * @return {Promise}
 */
function getBlob(url) {
    return new Promise(resolve => {
        const xhr = new XMLHttpRequest();

        xhr.open('GET', url, true);
        xhr.responseType = 'blob';
        xhr.onload = () => {
            if (xhr.status === 200) {
                resolve(xhr.response);
            }
        };

        xhr.send();
    });
}

/**
 * 保存
 * @param  {Blob} blob
 * @param  {String} filename 想要保存的文件名称
 */
function saveAs(blob, filename) {
    if (window.navigator.msSaveOrOpenBlob) {
        navigator.msSaveBlob(blob, filename);
    } else {
        const link = document.createElement('a');
        const body = document.querySelector('body');

        link.href = window.URL.createObjectURL(blob);
        link.download = filename;

        // fix Firefox
        link.style.display = 'none';
        body.appendChild(link);

        link.click();
        body.removeChild(link);

        window.URL.revokeObjectURL(link.href);
    }
}

/**
 * 下载
 * @param  {String} url 目标文件地址
 * @param  {String} filename 想要保存的文件名称
 */
function download(url, filename) {
    getBlob(url).then(blob => {
        saveAs(blob, filename);
    });
}

function preview(url){
    layer.photos({
        photos: {
            title: "查看",
            data: [{
                src:  url
            }]
        },
        shade: .01,
        closeBtn: 1,
        anim: 5
    });
}
