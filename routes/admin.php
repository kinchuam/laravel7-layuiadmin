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
    //全文搜索
    Route::get('/search','IndexController@search')->name('admin.search');
    Route::get('/line_chart','IndexController@line_chart')->name('admin.line_chart');
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
        Route::get('permission/list', 'PermissionController@get_lists')->name('admin.permission.list');
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
    //错误日志
    Route::group(['middleware'=>['permission:logs.error']],function () {
        Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('admin.logs');
    });
});


//设置管理
Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'permission:config.manage']], function () {
    //基础设置
    Route::group(['middleware' => 'permission:config.site'], function () {
        Route::get('site', 'SiteController@index')->name('admin.site')->middleware('permission:config.site');
        Route::put('site', 'SiteController@update')->name('admin.site.update')->middleware('permission:config.site.update');
        //上传配置
        Route::get('attachment', 'SiteController@attachment')->name('admin.attachment')->middleware('permission:config.attachment');
        Route::put('attachment/update', 'SiteController@update')->name('admin.attachment.update')->middleware('permission:config.attachment.update');
        //配置信息
        Route::get('optimize','SiteController@optimize')->name('admin.optimize')->middleware('permission:config.optimize');
        //更新缓存
        Route::get('datecache','SiteController@datecache')->name('admin.datecache')->middleware('permission:config.datecache');
        Route::put('clearcache','SiteController@clearcache')->name('admin.clearcache')->middleware('permission:config.clearcache');

    });
});

//数据库管理
Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'permission:database.manage']], function () {
    //数据备份
    Route::group(['middleware' => 'permission:database.backup'], function () {
        Route::get('database/backup', 'DatabaseController@index')->name('admin.database.backup');
        Route::get('database/backup/data','DatabaseController@data')->name('admin.database.backup.data');
        Route::post('database/backup/optimize','DatabaseController@optimize')->name('admin.database.backup.optimize');
        Route::post('database/backup/repair','DatabaseController@repair')->name('admin.database.backup.repair');
        //添加
        Route::match(['get', 'post'],'database/backup/store','DatabaseController@store')->name('admin.database.backup.store')->middleware('permission:database.backup.create');
    });
    //数据恢复
    Route::group(['middleware' => 'permission:database.restore'], function () {
        Route::get('database/restore', 'DatabaseController@restore_index')->name('admin.database.restore');
        Route::get('database/restore/data','DatabaseController@restore_data')->name('admin.database.restore.data');
        //恢复
        Route::get('database/restore/restore','DatabaseController@restore')->name('admin.database.restore.restore')->middleware('permission:database.restore.restore');
        //下载
        Route::get('database/restore/download','DatabaseController@download')->name('admin.database.restore.download')->middleware('permission:database.restore.download');
        //删除
        Route::delete('database/restore/destroy','DatabaseController@destroy')->name('admin.database.restore.destroy')->middleware('permission:database.restore.destroy');
    });
});

//内容管理
Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware'=>['auth:admin','permission:content.manage']],function (){
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
        Route::post('files/FileUpload', 'PublicController@FileUpload')->name('admin.FileUpload')->middleware('permission:content.files.create');
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
