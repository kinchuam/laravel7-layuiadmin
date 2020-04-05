
## 安装步骤
### 使用laravel7-layuiadmin
- git clone  https://github.com/kinchuam/laravel7-layuiadmin.git
- 复制.env.example为.env
- 配置.env里的数据库连接信息
- composer update
- php artisan migrate
- php artisan db:seed
- php artisan key:generate
- 后台地址： 域名/admin

#### 全文搜索需要在.env 添加这两行
- SCOUT_DRIVER=tntsearch
- SCOUT_QUEUE=true

## 图片展示
- 主页
![Image text](https://raw.githubusercontent.com/kinchuam/laravel7-layuiadmin/master/public/images/11.png?raw=true)
- 用户
![Image text](https://raw.githubusercontent.com/kinchuam/laravel7-layuiadmin/master/public/images/12.png?raw=true)
- 权限
![Image text](https://raw.githubusercontent.com/kinchuam/laravel7-layuiadmin/master/public/images/13.png?raw=true)
- 日志
![Image text](https://raw.githubusercontent.com/kinchuam/laravel7-layuiadmin/master/public/images/14.png?raw=true)
- 设置
![Image text](https://raw.githubusercontent.com/kinchuam/laravel7-layuiadmin/master/public/images/15.png?raw=true)
# laravel7-layuiadmin
