<script>
    layui.use(['form'], function () {
        let $ = layui.$,
            form = layui.form,
            username = "{{isset($user['username'])?$user['username']:old('username')}}";
        form.val('example', {
            "username": username
            ,"name": "{{isset($user['name'])?$user['name']:old('name')}}"
            ,"email": "{{isset($user['email'])?$user['email']:old('email')}}"
            ,"phone": "{{isset($user['phone'])?$user['phone']:old('phone')}}"
        });
        if (username !== '') {
            $(".pass_text").removeClass('layui-hide').addClass('layui-show');
        }
        form.verify({
            username: [
                /^[\S]{4,14}$/
                ,'账号名必须至少4到14字符'
            ],
            pass: function (value, item) {
                if (username === '' && value === '') {
                    return '请输入密码';
                }
                if(value !== '' && !new RegExp(/^[\S]{6,14}$/).test(value)){
                    return '密码必须6到14位，且不能出现空格';
                }
            },
            pass_confirm: function (value, item) {
                if (username === '' && value === '') {
                    return '确认密码';
                }
                if(value !== '' && !new RegExp(/^[\S]{6,14}$/).test(value)){
                    return '密码必须6到14位，且不能出现空格';
                }
            },
        });
    });
</script>