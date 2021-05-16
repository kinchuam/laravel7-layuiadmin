
## 安装步骤
### 使用laravel7-layuiadmin
- git clone  https://github.com/kinchuam/laravel7-layuiadmin.git
- 复制.env.example为.env
- 配置.env里的数据库连接信息，APP_URL域名连接
- composer update
- php artisan migrate
- php artisan db:seed
- php artisan key:generate
- php artisan storage:link
- 后台地址： 域名/admin;  账号：username  密码：password

![输入图片说明](https://gitee.com/kinchuam/laravel7-layuiadmin/raw/master/public/images/1.png "1.png")

#### 全文搜索建议使用拓展 matchish/laravel-scout-elasticsearch

## 图片展示
- 主页
![Image text](https://gitee.com/kinchuam/laravel7-layuiadmin/raw/master/public/images/11.png "11.png")
- 内容
![Image text](https://gitee.com/kinchuam/laravel7-layuiadmin/raw/master/public/images/12.png "12.png")
- 系统管理
![Image text](https://gitee.com/kinchuam/laravel7-layuiadmin/raw/master/public/images/13.png "13.png")
- 系统设置
![Image text](https://gitee.com/kinchuam/laravel7-layuiadmin/raw/master/public/images/14.png "1.png")
- 日志
![Image text](https://gitee.com/kinchuam/laravel7-layuiadmin/raw/master/public/images/15.png "15.png")
# laravel7-layuiadmin
