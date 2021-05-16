<script>
    layui.use(['form', 'iconHhysFa', 'xmSelect'], function () {
        let $ = layui.$
            , form = layui.form
            , iconHhysFa = layui.iconHhysFa
            , xmSelect = layui.xmSelect
            , parent_id = $("#category").data('parent_id') || 0
            , icon = "{{isset($permission['icon'])?$permission['icon']:'layui-icon-home'}}";

        form.val('example', {
            "name": "{{isset($permission['name'])?$permission['name']:old('name')}}"
            ,"display_name": "{{isset($permission['display_name'])?$permission['display_name']:old('display_name')}}"
            ,"route": "{{isset($permission['route'])?$permission['route']:old('route')}}"
            ,"icon": icon
        });
        //图标选择器
        iconHhysFa.render({
            elem: '#icon',
            type: 'fontClass',
            search: true,
            page: true,
        });
        iconHhysFa.checkIcon("icon", icon, "fontClass");
        let demo = xmSelect.render({
            el: '#category',
            name: 'parent_id',
            model: { label: { type: 'text' } },
            prop: { name: 'display_name', value: 'id', },
            tips: '顶级权限',
            radio: true,
            clickClose: true,
            tree: { show: true, strict: false, },
        });
        $.get("{{route('admin.permission.list')}}", {parent_id}, function (res) {
            demo.update({
                data: res.data,
                autoRow: true,
            });
            demo.changeExpandedKeys(res.expandedKeys)
        });
    });
</script>
