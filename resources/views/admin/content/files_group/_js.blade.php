<script>
    layui.use(['form'], function () {
        let form = layui.form;

        form.val('example', {
            "group_name": "{{isset($item['name'])?$item['name']:old('name')}}"
            ,"sort": "{{isset($item['sort'])?$item['sort']:old('phone',0)}}"
        });

    });
</script>