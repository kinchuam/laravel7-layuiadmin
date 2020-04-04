<?php
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| 后台公共路由部分
|
*/

use Illuminate\Support\Facades\Route;

Route::group(['namespace'=>'Admin','prefix'=>'admin'],function (){
    //登录、注销
    Route::get('login','LoginController@showLoginForm')->name('admin.loginForm');
    Route::post('login','LoginController@login')->name('admin.login');
    Route::get('logout','LoginController@logout')->name('admin.logout');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| 后台需要授权的路由 admins
|
*/
Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware'=>'auth:admin'],function (){
    //后台布局
    Route::get('/','IndexController@layout')->name('admin.layout');
    //后台首页
    Route::get('/index','IndexController@index')->name('admin.index');
    //全文搜索
    Route::get('/search','IndexController@search')->name('admin.search');
    //当前管理员设置
    Route::get('set/index','SetController@index')->name('admin.set.index');
    Route::post('set/setinfo','SetController@setinfo')->name('admin.set.setinfo');
    Route::get('set/password','SetController@password')->name('admin.set.password');
    Route::post('set/setpassword','SetController@setpassword')->name('admin.set.setpassword');
});

//系统管理
Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware'=>['auth:admin','permission:system.manage']],function (){
    //数据表格接口
    Route::get('data','IndexController@data')->name('admin.data')->middleware('permission:system.role|system.user|system.permission');
    Route::get('line_chart','IndexController@line_chart')->name('admin.line_chart');
    //用户管理
    Route::group(['middleware'=>['permission:system.user']],function (){
        Route::get('user','UserController@index')->name('admin.user');
        //登录记录
        Route::get('user/loginlog','UserController@LoginLog')->name('admin.user.loginlog');
        Route::get('user/loginlogdata','UserController@LoginLogDate')->name('admin.user.loginlogdata');
        //添加
        Route::get('user/create','UserController@create')->name('admin.user.create')->middleware('permission:system.user.create');
        Route::post('user/store','UserController@store')->name('admin.user.store')->middleware('permission:system.user.create');
        //编辑
        Route::get('user/{id}/edit','UserController@edit')->name('admin.user.edit')->middleware('permission:system.user.edit');
        Route::put('user/{id}/update','UserController@update')->name('admin.user.update')->middleware('permission:system.user.edit');
        //删除
        Route::delete('user/destroy','UserController@destroy')->name('admin.user.destroy')->middleware('permission:system.user.destroy');
        //分配角色
        Route::get('user/{id}/role','UserController@role')->name('admin.user.role')->middleware('permission:system.user.role');
        Route::put('user/{id}/assignRole','UserController@assignRole')->name('admin.user.assignRole')->middleware('permission:system.user.role');
        //分配权限
        Route::get('user/{id}/permission','UserController@permission')->name('admin.user.permission')->middleware('permission:system.user.permission');
        Route::put('user/{id}/assignPermission','UserController@assignPermission')->name('admin.user.assignPermission')->middleware('permission:system.user.permission');
    });
    //角色管理
    Route::group(['middleware'=>'permission:system.role'],function (){
        Route::get('role','RoleController@index')->name('admin.role');
        //添加
        Route::get('role/create','RoleController@create')->name('admin.role.create')->middleware('permission:system.role.create');
        Route::post('role/store','RoleController@store')->name('admin.role.store')->middleware('permission:system.role.create');
        //编辑
        Route::get('role/{id}/edit','RoleController@edit')->name('admin.role.edit')->middleware('permission:system.role.edit');
        Route::put('role/{id}/update','RoleController@update')->name('admin.role.update')->middleware('permission:system.role.edit');
        //删除
        Route::delete('role/destroy','RoleController@destroy')->name('admin.role.destroy')->middleware('permission:system.role.destroy');
        //分配权限
        Route::get('role/{id}/permission','RoleController@permission')->name('admin.role.permission')->middleware('permission:system.role.permission');
        Route::put('role/{id}/assignPermission','RoleController@assignPermission')->name('admin.role.assignPermission')->middleware('permission:system.role.permission');
    });
    //权限管理
    Route::group(['middleware'=>'permission:system.permission'],function (){
        Route::get('permission','PermissionController@index')->name('admin.permission');
        //添加
        Route::get('permission/create','PermissionController@create')->name('admin.permission.create')->middleware('permission:system.permission.create');
        Route::post('permission/store','PermissionController@store')->name('admin.permission.store')->middleware('permission:system.permission.create');
        //编辑
        Route::get('permission/{id}/edit','PermissionController@edit')->name('admin.permission.edit')->middleware('permission:system.permission.edit');
        Route::put('permission/{id}/update','PermissionController@update')->name('admin.permission.update')->middleware('permission:system.permission.edit');
        //删除
        Route::delete('permission/destroy','PermissionController@destroy')->name('admin.permission.destroy')->middleware('permission:system.permission.destroy');
    });

});

//日志管理
Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'permission:logs.manage']], function () {
    //访问日志
    Route::group(['middleware'=>['permission:logs.access']],function () {
        Route::get('access', 'AccessLogController@index')->name('admin.access');
        Route::get('access/data', 'AccessLogController@data')->name('admin.access.data');
        Route::get('access/{id}/show', 'AccessLogController@show')->name('admin.access.show');
        //删除
        Route::delete('access/destroy','AccessLogController@destroy')->name('admin.access.destroy')->middleware('permission:logs.access.destroy');
    });
    //操作日志
    Route::group(['middleware'=>['permission:logs.operation']],function () {
        Route::get('operation', 'OperationController@index')->name('admin.operation');
        Route::get('operation/data', 'OperationController@data')->name('admin.operation.data');
    });
    Route::group(['middleware'=>['permission:logs.error']],function () {
        Route::get('/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('admin.logs');
    });
});


//设置管理
Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'permission:config.manage']], function () {
    //基础设置
    Route::group(['middleware' => 'permission:config.site'], function () {
        Route::get('site', 'SiteController@index')->name('admin.site');
        //上传配置
        Route::get('site/attachment', 'SiteController@attachment')->name('admin.site.attachment');
        Route::put('site/attachmentupdate', 'SiteController@attachmentupdate')->name('admin.site.attachmentupdate')->middleware('permission:config.site.attachmentupdate');
        //配置信息
        Route::get('site/optimize','SiteController@optimize')->name('admin.site.optimize')->middleware('permission:config.site.optimize');
        //更新缓存
        Route::get('site/datecache','SiteController@datecache')->name('admin.site.datecache')->middleware('permission:config.site.datecache');
        Route::put('site/clearcache','SiteController@clearcache')->name('admin.site.clearcache')->middleware('permission:config.site.clearcache');
        //更新
        Route::put('site', 'SiteController@update')->name('admin.site.update')->middleware('permission:config.site.update');
    });
});


//数据库管理
Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'permission:database.manage']], function () {
    //数据备份
    Route::group(['middleware' => 'permission:database.backup'], function () {
        Route::get('databasebackup', 'DatabaseController@index')->name('admin.databasebackup');
        Route::get('databasebackup/data','DatabaseController@data')->name('admin.databasebackup.data');
        Route::post('databasebackup/optimize','DatabaseController@optimize')->name('admin.databasebackup.optimize');
        Route::post('databasebackup/repair','DatabaseController@repair')->name('admin.databasebackup.repair');
        //添加
        Route::match(['get', 'post'],'databasebackup/store','DatabaseController@store')->name('admin.databasebackup.store')->middleware('permission:database.databasebackup.create');

    });
    //数据恢复
    Route::group(['middleware' => 'permission:database.restore'], function () {
        Route::get('databaserestore', 'DatabaseController@restore_index')->name('admin.databaserestore');
        Route::get('databaserestore/data','DatabaseController@restore_data')->name('admin.databaserestore.data');
        //恢复
        Route::get('databaserestore/restore','DatabaseController@restore')->name('admin.databaserestore.restore')->middleware('permission:database.databaserestore.restore');
        //下载
        Route::get('databaserestore/download','DatabaseController@download')->name('admin.databaserestore.download')->middleware('permission:database.databaserestore.download');
        //删除
        Route::delete('databaserestore/destroy','DatabaseController@destroy')->name('admin.databaserestore.destroy')->middleware('permission:database.databaserestore.destroy');
    });
});

Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware'=>['auth:admin','permission:content.manage']],function (){

    //文章管理
    Route::group(['middleware' => 'permission:content.article'], function () {
        Route::get('article/data', 'Article\IndexController@data')->name('admin.article.data');
        Route::get('article', 'Article\IndexController@index')->name('admin.article');
        Route::post('article/status', 'Article\IndexController@status')->name('admin.article.status');
        //添加
        Route::get('article/create', 'Article\IndexController@create')->name('admin.article.create')->middleware('permission:content.article.create');
        Route::post('article/store', 'Article\IndexController@store')->name('admin.article.store')->middleware('permission:content.article.create');
        //编辑
        Route::get('article/{id}/edit', 'Article\IndexController@edit')->name('admin.article.edit')->middleware('permission:content.article.edit');
        Route::put('article/{id}/update', 'Article\IndexController@update')->name('admin.article.update')->middleware('permission:content.article.edit');
        //回收站
        Route::get('article/recycle', 'Article\IndexController@recycle')->name('admin.article.recycle')->middleware('permission:content.article.recycle');
        //恢复
        Route::post('article/recover', 'Article\IndexController@recover')->name('admin.article.recover')->middleware('permission:content.article.recover');
        //真实删除
        Route::delete('article/expurgate', 'Article\IndexController@expurgate')->name('admin.article.expurgate')->middleware('permission:content.article.expurgate');
        //删除
        Route::delete('article/destroy', 'Article\IndexController@destroy')->name('admin.article.destroy')->middleware('permission:content.article.destroy');
    });
    //分类管理
    Route::group(['middleware' => 'permission:content.category'], function () {
        Route::get('category/data', 'Article\CategoryController@data')->name('admin.article.category.data');
        Route::get('category', 'Article\CategoryController@index')->name('admin.article.category');
        //添加分类
        Route::get('category/create', 'Article\CategoryController@create')->name('admin.article.category.create')->middleware('permission:content.article.category.create');
        Route::post('category/store', 'Article\CategoryController@store')->name('admin.article.category.store')->middleware('permission:content.article.category.create');
        //编辑分类
        Route::get('category/{id}/edit', 'Article\CategoryController@edit')->name('admin.article.category.edit')->middleware('permission:content.article.category.edit');
        Route::put('category/{id}/update', 'Article\CategoryController@update')->name('admin.article.category.update')->middleware('permission:content.article.category.edit');
        //删除分类
        Route::delete('category/destroy', 'Article\CategoryController@destroy')->name('admin.article.category.destroy')->middleware('permission:content.article.category.destroy');
    });

    //附件管理
    Route::group(['middleware'=>['permission:content.files']],function () {
        Route::get('files', 'FilesController@index')->name('admin.files');
        Route::get('files/data', 'FilesController@data')->name('admin.files.data');
        Route::get('files/getFiles', 'FilesController@getFiles')->name('admin.files.getFiles');
        Route::get('files/download', 'FilesController@download')->name('admin.files.download');
        //分组
        Route::post('files/addGroup', 'FilesController@addGroup')->name('admin.files.addGroup');
        Route::post('files/editGroup', 'FilesController@editGroup')->name('admin.files.editGroup');
        Route::post('files/deleteGroup', 'FilesController@deleteGroup')->name('admin.files.deleteGroup');
        //移动
        Route::post('files/moveFiles', 'FilesController@moveFiles')->name('admin.files.moveFiles')->middleware('permission:content.files.create');
        //上传
        Route::get('files/create','FilesController@create')->name('admin.files.create')->middleware('permission:content.files.create');
        //回收站
        Route::get('files/recycle', 'FilesController@recycle')->name('admin.files.recycle')->middleware('permission:content.files.recycle');
        //恢复
        Route::post('files/recover', 'FilesController@recover')->name('admin.files.recover')->middleware('permission:content.files.recover');
        //真实删除
        Route::delete('files/expurgate', 'FilesController@expurgate')->name('admin.files.expurgate')->middleware('permission:content.files.expurgate');
        //删除
        Route::delete('files/destroy','FilesController@destroy')->name('admin.files.destroy')->middleware('permission:content.files.destroy');
    });
});

//消息管理
/*Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'permission:message.manage']], function () {
    //消息管理
    Route::group(['middleware' => 'permission:message.message'], function () {
        Route::get('message/data', 'MessageController@data')->name('admin.message.data');
        Route::get('message/getUser', 'MessageController@getUser')->name('admin.message.getUser');
        Route::get('message', 'MessageController@index')->name('admin.message');

        Route::get('message/count', 'MessageController@getMessageCount')->name('admin.message.get_count');
        //添加
        Route::get('message/create', 'MessageController@create')->name('admin.message.create')->middleware('permission:message.message.create');
        Route::post('message/store', 'MessageController@store')->name('admin.message.store')->middleware('permission:message.message.create');
        //删除
        Route::delete('message/destroy', 'MessageController@destroy')->name('admin.message.destroy')->middleware('permission:message.message.destroy');
        //我的消息
        Route::get('mine/message', 'MessageController@mine')->name('admin.message.mine')->middleware('permission:message.message.mine');
        Route::post('message/{id}/read', 'MessageController@read')->name('admin.message.read')->middleware('permission:message.message.mine');
    });

});*/

