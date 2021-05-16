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

Route::group(['namespace'=>'Admin','prefix'=>'admin'],function ($route){
    //登录、注销
    $route->get('login','LoginController@showLoginForm')->name('admin.loginForm');
    $route->post('login','LoginController@login')->name('admin.login');
    $route->get('logout','LoginController@logout')->name('admin.logout');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| 后台需要授权的路由 admins
|
*/
Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware'=>'auth:admin'],function ($route){
    $route->get('/','IndexController@layout')->name('admin.layout');
    $route->get('index','IndexController@index')->name('admin.index');
    //数据接口
    $route->get('line_chart','IndexController@line_chart')->name('admin.line_chart');
    //当前管理员设置
    $route->get('basic/index','BasicController@index')->name('admin.basic.index');
    $route->post('basic/setInfo','BasicController@setInfo')->name('admin.basic.setInfo');
    $route->get('basic/password','BasicController@password')->name('admin.basic.password');
    $route->post('basic/setPassword','BasicController@setPassword')->name('admin.basic.setPassword');
});

//内容管理
Route::group(['namespace'=>'Admin\Content','prefix'=>'admin','middleware'=>['auth:admin','permission:content.manage']],function ($route){
    $route->post('fileUpload', 'PublicController@FileUpload')->name('admin.FileUpload');
    $route->group(['middleware' => ['permission:content.files']], function ($route) {
        $route->get('files', 'FilesController@index')->name('admin.content.files');
        $route->get('files/data', 'FilesController@data')->name('admin.content.files.data');
        $route->get('files/getFiles', 'FilesController@getFiles')->name('admin.content.files.getFiles');
        $route->get('files/download', 'FilesController@download')->name('admin.content.files.download');
        $route->get('files/create', 'FilesController@create')->name('admin.content.files.create')->middleware('permission:content.files.create');
        $route->get('files/recycle', 'FilesController@recycle')->name('admin.content.files.recycle')->middleware('permission:content.files.recycle');
        $route->post('files/recover', 'FilesController@recover')->name('admin.content.files.recover')->middleware('permission:content.files.recover');
        $route->delete('files/expurgate', 'FilesController@expurgate')->name('admin.content.files.expurgate')->middleware('permission:content.files.expurgate');
        $route->delete('files/destroy', 'FilesController@destroy')->name('admin.content.files.destroy')->middleware('permission:content.files.destroy');
    });
    //附件分组管理
    $route->group(['middleware'=>['permission:content.files_group']], function ($route){
        $route->get('files_group', 'FilesGroupController@index')->name('admin.content.files_group');
        $route->get('files_group/data', 'FilesGroupController@data')->name('admin.content.files_group.data');
        $route->post('files/moveFiles', 'FilesGroupController@moveFiles')->name('admin.content.files_group.moveFiles');
        $route->get('files_group/create','FilesGroupController@create')->name('admin.content.files_group.create')->middleware('permission:content.files_group.create');
        $route->post('files_group/store','FilesGroupController@store')->name('admin.content.files_group.store')->middleware('permission:content.files_group.create');
        $route->get('files_group/edit','FilesGroupController@edit')->name('admin.content.files_group.edit')->middleware('permission:content.files_group.edit')->where(['id' => '[0-9]+']);
        $route->put('files_group/update','FilesGroupController@update')->name('admin.content.files_group.update')->middleware('permission:content.files_group.edit')->where(['id' => '[0-9]+']);
        $route->delete('files_group/destroy','FilesGroupController@destroy')->name('admin.content.files_group.destroy')->middleware('permission:content.files_group.destroy');
    });
});

//系统管理
Route::group(['namespace'=>'Admin\System','prefix'=>'admin','middleware'=>['auth:admin','permission:system.manage']],function ($route){
    //用户管理
    $route->group(['middleware'=>['permission:system.user']],function ($route){
        $route->get('user','UserController@index')->name('admin.user');
        $route->get('user/data','UserController@data')->name('admin.user.data');
        $route->get('user/create','UserController@create')->name('admin.user.create')->middleware('permission:system.user.create');
        $route->post('user/store','UserController@store')->name('admin.user.store')->middleware('permission:system.user.create');
        $route->get('user/{id}/edit','UserController@edit')->name('admin.user.edit')->middleware('permission:system.user.edit')->where(['id' => '[0-9]+']);
        $route->put('user/{id}/update','UserController@update')->name('admin.user.update')->middleware('permission:system.user.edit')->where(['id' => '[0-9]+']);
        $route->delete('user/destroy','UserController@destroy')->name('admin.user.destroy')->middleware('permission:system.user.destroy');
        $route->get('user/{id}/role','UserController@role')->name('admin.user.role')->middleware('permission:system.user.role')->where(['id' => '[0-9]+']);
        $route->put('user/{id}/assignRole','UserController@assignRole')->name('admin.user.assignRole')->middleware('permission:system.user.role')->where(['id' => '[0-9]+']);
        $route->get('user/{id}/permission','UserController@permission')->name('admin.user.permission')->middleware('permission:system.user.permission')->where(['id' => '[0-9]+']);
        $route->put('user/{id}/assignPermission','UserController@assignPermission')->name('admin.user.assignPermission')->middleware('permission:system.user.permission')->where(['id' => '[0-9]+']);
    });
    //角色管理
    $route->group(['middleware'=>'permission:system.role'],function ($route){
        $route->get('role','RoleController@index')->name('admin.role');
        $route->get('role/data','RoleController@data')->name('admin.role.data');
        $route->get('role/create','RoleController@create')->name('admin.role.create')->middleware('permission:system.role.create');
        $route->post('role/store','RoleController@store')->name('admin.role.store')->middleware('permission:system.role.create');
        $route->get('role/{id}/edit','RoleController@edit')->name('admin.role.edit')->middleware('permission:system.role.edit')->where(['id' => '[0-9]+']);
        $route->put('role/{id}/update','RoleController@update')->name('admin.role.update')->middleware('permission:system.role.edit')->where(['id' => '[0-9]+']);
        $route->delete('role/destroy','RoleController@destroy')->name('admin.role.destroy')->middleware('permission:system.role.destroy');
        $route->get('role/{id}/permission','RoleController@permission')->name('admin.role.permission')->middleware('permission:system.role.permission')->where(['id' => '[0-9]+']);
        $route->put('role/{id}/assignPermission','RoleController@assignPermission')->name('admin.role.assignPermission')->middleware('permission:system.role.permission')->where(['id' => '[0-9]+']);
    });
    //权限管理
    $route->group(['middleware'=>'permission:system.permission'],function ($route){
        $route->get('permission','PermissionController@index')->name('admin.permission');
        $route->get('permission/data','PermissionController@data')->name('admin.permission.data');
        $route->get('permission/list', 'PermissionController@get_lists')->name('admin.permission.list');
        $route->get('permission/create','PermissionController@create')->name('admin.permission.create')->middleware('permission:system.permission.create');
        $route->post('permission/store','PermissionController@store')->name('admin.permission.store')->middleware('permission:system.permission.create');
        $route->get('permission/{id}/edit','PermissionController@edit')->name('admin.permission.edit')->middleware('permission:system.permission.edit')->where(['id' => '[0-9]+']);
        $route->put('permission/{id}/update','PermissionController@update')->name('admin.permission.update')->middleware('permission:system.permission.edit')->where(['id' => '[0-9]+']);
        $route->delete('permission/destroy','PermissionController@destroy')->name('admin.permission.destroy')->middleware('permission:system.permission.destroy');
    });

});


//设置管理
Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'permission:config.manage']], function ($route) {
    //基础设置
    $route->group(['middleware' => 'permission:config.site'], function ($route) {
        $route->get('site', 'SiteController@index')->name('admin.site')->middleware('permission:config.site');
        $route->put('site', 'SiteController@update')->name('admin.site.update')->middleware('permission:config.site.update');
        //上传配置
        $route->get('attachment', 'SiteController@attachment')->name('admin.attachment')->middleware('permission:config.attachment');
        $route->put('attachment/update', 'SiteController@update')->name('admin.attachment.update')->middleware('permission:config.attachment.update');
        //配置信息
        $route->get('optimize','SiteController@optimize')->name('admin.optimize')->middleware('permission:config.optimize');
        //更新缓存
        $route->get('dateCache','SiteController@dateCache')->name('admin.dateCache')->middleware('permission:config.dateCache');
        $route->put('clearCache','SiteController@clearCache')->name('admin.clearCache')->middleware('permission:config.clearCache');

    });
});

//日志管理
Route::group(['namespace' => 'Admin\Logs', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'permission:logs.manage']], function ($route) {
    //访问日志
    $route->group(['middleware'=>['permission:logs.access']],function ($route) {
        $route->get('access', 'AccessLogController@index')->name('admin.access');
        $route->get('access/data', 'AccessLogController@data')->name('admin.access.data');
        $route->get('access/{id}/show', 'AccessLogController@show')->name('admin.access.show')->where(['id' => '[0-9]+']);
        $route->delete('access/destroy','AccessLogController@destroy')->name('admin.access.destroy')->middleware('permission:logs.access.destroy');
    });
    //操作日志
    $route->group(['middleware'=>['permission:logs.operation']],function ($route) {
        $route->get('operation', 'OperationController@index')->name('admin.operation');
        $route->get('operation/data', 'OperationController@data')->name('admin.operation.data');
    });
    //登录记录
    $route->group(['middleware'=>['permission:logs.loginLog']], function ($route) {
        $route->get('loginLog', 'LoginLogController@index')->name('admin.loginLog');
        $route->get('loginLog/data', 'LoginLogController@data')->name('admin.loginLog.data');
    });
    //错误日志
    $route->group(['middleware'=>['permission:logs.error']],function ($route) {
        $route->get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('admin.logs');
    });
});

