<script>
    layui.use(['IconFonts','xmSelect'],function () {

        var parent_id = $("#category").data('parent_id') || "";
        get_data(parent_id);
        var demo1 = layui.xmSelect.render({
            el: '#category',
            name: 'parent_id',
            model: { label: { type: 'text' } },
            radio: true,
            clickClose: true,
            tree: {
                show: true,
                strict: false,
                expandedKeys: [parent_id],
            },
            height: 'auto',
            show(){
                get_data(parent_id);
            },
            on: function(data){
                if (data.isAdd) {
                    parent_id = data.change[0].id
                    return data.change.slice(0, 1)
                }else {
                    parent_id = 0;
                }
            },
        })

        function get_data(parent_id) {
            $.get("{{route('admin.permission.list')}}",{
                parent_id,
                type:'permission',
                id:"{{isset($item->id)?$item->id:0}}"
            },function (res) {
                demo1.update({
                    data(){
                        return  res.data;
                    },
                    autoRow: true,
                })
            });
        }

        //图标选择器
        layui.IconFonts.render({
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

        layui.IconFonts.checkIcon("icon","{{isset($permission->icon)?$permission->icon:'layui-icon-set'}}","layui_icon");
    });
</script>
