<link rel="stylesheet" href="{{ asset('static/common/plugins/inputTags/inputTags.css') }}" media="all">
<script>
    layui.use(['laydate','tinymce','inputTags'],function () {
        var laydate = layui.laydate,tinymce = layui.tinymce,inputTags = layui.inputTags;

        inputTags.render({
            elem: '#keywords',
            name: 'tags',
            content: {!! isset($item->tags)?json_encode(explode('|',$item->tags)):json_encode([]) !!}
        });

        laydate.render({
            elem: '#created_at'
            ,trigger: 'click'
            ,type:'datetime'
        });

        tinymce.render({
            elem: "#content"
            , images_upload_url: '{{route('FileUpload')}}'
            , field: 'iFile'
            , height: 600
        });

        $("#formDemo").click(function () {
            tinyMCE.editors['content'].save();
        });
    })
</script>
