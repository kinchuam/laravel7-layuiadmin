<script>
    layui.use(['element','upload','jquery'],function () {
        var $ = layui.$,element = layui.element,upload = layui.upload;
        //多文件列表示例
        var demoListView = $('#demoList')
            , uploadListIns = upload.render({
            elem: '#testList'
            , exts: '{{$exts}}'
            , url: '{{ route("FileUpload") }}'
            , field:'iFile'
            , accept: 'file'
            , multiple: true
            , auto: false
            , bindAction: '#testListAction'
            ,progress: function(n,e,i,j){
                var percent = n + '%';//获取进度百分比
                element.progress('progress_' + j , percent);
            }
            , choose: function (obj) {
                var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列
                //读取本地文件
                obj.preview(function (index, file, result) {
                    var tr = $(['<tr id="upload-' + index + '">'
                        , '<td>' + file.name + '</td>'
                        , '<td>' + (file.size / 1014).toFixed(1) + 'kb</td>'
                        , '<td><div class="layui-progress layui-progress-big" lay-showpercent="true" lay-filter="progress_'+index+'" ><div class="layui-progress-bar" lay-percent="20%"><span class="layui-progress-text">0%</span></div></div></td>'
                        , '<td>等待上传</td>'
                        , '<td>'
                        , '<button class="layui-btn layui-btn-xs demo-reload layui-hide">重传</button>'
                        , '<button class="layui-btn layui-btn-xs layui-btn-danger demo-delete">删除</button>'
                        , '</td>'
                        , '</tr>'].join(''));

                    //单个重传
                    tr.find('.demo-reload').on('click', function () {
                        obj.upload(index, file);
                    });

                    //删除
                    tr.find('.demo-delete').on('click', function () {
                        delete files[index]; //删除对应的文件
                        tr.remove();
                        uploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
                    });

                    demoListView.append(tr);
                });
            }
            ,
            before: function (obj) {
                this.data = {'filetype':'image',"_token":"{{ csrf_token() }}"};
            }
            , done: function (res, index, upload) {
                if (res.code == 0) { //上传成功
                    var tr = demoListView.find('tr#upload-' + index)
                        , tds = tr.children();
                    tds.eq(3).html('<span style="color: #5FB878;">上传成功</span>');
                    tds.eq(4).html(''); //清空操作
                    parent.layui.table.reload('dataTable');
                    return delete this.files[index]; //删除文件队列已经上传成功的文件
                }

                this.error(index, upload,res);
            }
            , error: function (index, upload,res) {
                var tr = demoListView.find('tr#upload-' + index)
                    , tds = tr.children();
                tds.eq(3).html('<span style="color: #FF5722;">上传失败：'+res.msg+'</span>');
                tds.eq(4).find('.demo-reload').removeClass('layui-hide'); //显示重传
            }
        });
    });
</script>
