<script>
    layui.use(['form'], function () {
        let form = layui.form;
        form.val('example', {
            "name": "{{isset($role['name'])?$role['name']:old('name')}}"
            ,"display_name": "{{isset($role['display_name'])?$role['display_name']:old('display_name')}}"
        });
    })
</script>