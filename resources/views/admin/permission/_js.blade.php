<script>
    layui.use(['IconFonts'],function () {
        var IconFonts = layui.IconFonts;

        //图标选择器
        IconFonts.render({
            elem: '#icon',
            // 数据类型：fontClass/layui_icon，
            type: 'layui_icon',
            search: true,
            page: true,
            limit: 12,
            click: function (data) {
                //console.log(data);
            }
        });

        IconFonts.checkIcon("icon","{{isset($permission->icon)?$permission->icon:'layui-icon-set'}}","layui_icon");
    });
</script>