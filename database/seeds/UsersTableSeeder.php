<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //清空表
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \Illuminate\Support\Facades\DB::table('model_has_permissions')->truncate();
        \Illuminate\Support\Facades\DB::table('model_has_roles')->truncate();
        \Illuminate\Support\Facades\DB::table('role_has_permissions')->truncate();
        \Illuminate\Support\Facades\DB::table('users')->truncate();
        \Illuminate\Support\Facades\DB::table('users_login_log')->truncate();
        \Illuminate\Support\Facades\DB::table('roles')->truncate();
        \Illuminate\Support\Facades\DB::table('permissions')->truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $setdata = [
            'username' => 'root',
            'password' => Str::random(8)
        ];
        //用户
        $user = \App\Models\User::create([
            'username' => $setdata['username'],
            'phone' => '12345678910',
            'name' => '超级管理员',
            'email' => 'root@xxx.com',
            'password' => bcrypt($setdata['password']),
            'uuid' => \Faker\Provider\Uuid::uuid()
        ]);

        //角色
        $role = \App\Models\Role::create([
            'name' => 'root',
            'display_name' => '超级管理员'
        ]);

        //权限
        $permissions = [
            [
                'name' => 'content.manage',
                'display_name' => '内容管理',
                'route' => '',
                'icon' => 'layui-icon-read',
                'child' => [
                    [
                        'name' => 'content.files',
                        'display_name' => '附件管理',
                        'route' => 'admin.files',
                        'icon' => 'layui-icon-auz',
                        'child' => [
                            ['name' => 'content.files.create', 'display_name' => '添加附件','route'=>'admin.files.create'],
                            ['name' => 'content.files.destroy', 'display_name' => '删除附件','route'=>'admin.files.destroy'],
                            ['name' => 'content.files.recycle', 'display_name' => '回收站','route'=>'admin.files.recycle'],
                            ['name' => 'content.files.recover', 'display_name' => '回收站恢复','route'=>'admin.files.recover'],
                            ['name' => 'content.files.expurgate', 'display_name' => '回收站删除','route'=>'admin.files.expurgate'],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'system.manage',
                'display_name' => '系统管理',
                'route' => '',
                'icon' => 'layui-icon-util',
                'child' => [
                    [
                        'name' => 'system.user',
                        'display_name' => '用户管理',
                        'route' => 'admin.user',
                        'icon' => 'layui-icon-friends',
                        'child' => [
                            ['name' => 'system.user.create', 'display_name' => '添加用户','route'=>'admin.user.create'],
                            ['name' => 'system.user.edit', 'display_name' => '编辑用户','route'=>'admin.user.edit'],
                            ['name' => 'system.user.destroy', 'display_name' => '删除用户','route'=>'admin.user.destroy'],
                            ['name' => 'system.user.role', 'display_name' => '分配角色','route'=>'admin.user.role'],
                            ['name' => 'system.user.permission', 'display_name' => '分配权限','route'=>'admin.user.permission'],
                        ]
                    ],
                    [
                        'name' => 'system.role',
                        'display_name' => '角色管理',
                        'route' => 'admin.role',
                        'icon' => 'layui-icon-set-fill',
                        'child' => [
                            ['name' => 'system.role.create', 'display_name' => '添加角色','route'=>'admin.role.create'],
                            ['name' => 'system.role.edit', 'display_name' => '编辑角色','route'=>'admin.role.edit'],
                            ['name' => 'system.role.destroy', 'display_name' => '删除角色','route'=>'admin.role.destroy'],
                            ['name' => 'system.role.permission', 'display_name' => '分配权限','route'=>'admin.role.permission'],
                        ]
                    ],
                    [
                        'name' => 'system.permission',
                        'display_name' => '权限管理',
                        'route' => 'admin.permission',
                        'icon' => 'layui-icon-auz',
                        'child' => [
                            ['name' => 'system.permission.create', 'display_name' => '添加权限','route'=>'admin.permission.create'],
                            ['name' => 'system.permission.edit', 'display_name' => '编辑权限','route'=>'admin.permission.edit'],
                            ['name' => 'system.permission.destroy', 'display_name' => '删除权限','route'=>'admin.permission.destroy'],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'logs.manage',
                'display_name' => '日志管理',
                'route' => '',
                'icon' => 'layui-icon-log',
                'child' => [
                    [
                        'name' => 'logs.operation',
                        'display_name' => '操作日志',
                        'route' => 'admin.operation',
                        'icon' => 'layui-icon-set',
                        'child' => []
                    ],
                    [
                        'name' => 'logs.access',
                        'display_name' => '访问日志',
                        'route' => 'admin.access',
                        'icon' => 'layui-icon-set',
                        'child' => [
                            ['name' => 'logs.access.destroy', 'display_name' => '删除日志','route'=>'admin.access.destroy'],
                        ]
                    ],
                    [
                        'name' => 'logs.error',
                        'display_name' => '错误日志',
                        'route' => 'admin.logs',
                        'icon' => 'layui-icon-set',
                        'child' => []
                    ],
                ]
            ],
            [
                'name' => 'config.manage',
                'display_name' => '系统设置',
                'route' => '',
                'icon' => 'layui-icon-set',
                'child' => [
                    [
                        'name' => 'config.site',
                        'display_name' => '基础设置',
                        'route' => 'admin.site',
                        'icon' => 'layui-icon-website',
                        'child' => [
                            ['name' => 'config.site.update', 'display_name' => '更新配置','route'=>'admin.site.update'],
                        ]
                    ],
                    [
                        'name' => 'config.attachment',
                        'display_name' => '上传配置',
                        'route' => 'admin.attachment',
                        'icon' => 'layui-icon-set-fill',
                        'child' => [
                            ['name' => 'config.attachment.update', 'display_name' => '更新配置','route'=>'admin.attachment.update'],
                        ]
                    ],
                    [
                        'name' => 'config.optimize',
                        'display_name' => '配置信息',
                        'route' => 'admin.optimize',
                        'icon' => 'layui-icon-set-fill',
                        'child' => []
                    ],
                    [
                        'name' => 'config.datecache',
                        'display_name' => '更新缓存',
                        'route' => 'admin.datecache',
                        'icon' => 'layui-icon-set-fill',
                        'child' => [
                            ['name' => 'config.clearcache', 'display_name' => '更新缓存','route'=>'admin.clearcache'],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'database.manage',
                'display_name' => '数据管理',
                'route' => '',
                'icon' => 'layui-icon-app',
                'child' => [
                    [
                        'name' => 'database.backup',
                        'display_name' => '数据备份',
                        'route' => 'admin.database.backup',
                        'icon' => 'layui-icon-set',
                        'child' => [
                            ['name' => 'database.backup.create', 'display_name' => '添加数据备份','route'=>'admin.database.backup.create'],
                        ]
                    ],
                    [
                        'name' => 'database.restore',
                        'display_name' => '数据恢复',
                        'route' => 'admin.database.restore',
                        'icon' => 'layui-icon-set',
                        'child' => [
                            ['name' => 'database.restore.restore', 'display_name' => '恢复数据','route'=>'admin.database.restore.restore'],
                            ['name' => 'database.restore.download', 'display_name' => '下载数据','route'=>'admin.database.restore.download'],
                            ['name' => 'database.restore.destroy', 'display_name' => '删除数据','route'=>'admin.database.restore.destroy'],
                        ]
                    ],
                ]
            ],
        ];

        foreach ($permissions as $pem1) {
            //生成一级权限
            $p1 = \App\Models\Permission::create([
                'name' => $pem1['name'],
                'display_name' => $pem1['display_name'],
                'route' => $pem1['route']?:'',
                'icon' => $pem1['icon']?:'',
            ]);
            //为角色添加权限
            $role->givePermissionTo($p1);
            //为用户添加权限
            $user->givePermissionTo($p1);
            if (isset($pem1['child'])) {
                foreach ($pem1['child'] as $pem2) {
                    //生成二级权限
                    $p2 = \App\Models\Permission::create([
                        'name' => $pem2['name'],
                        'display_name' => $pem2['display_name'],
                        'parent_id' => $p1->id,
                        'route' => $pem2['route']?:1,
                        'icon' => $pem2['icon']?:'',
                    ]);
                    //为角色添加权限
                    $role->givePermissionTo($p2);
                    //为用户添加权限
                    $user->givePermissionTo($p2);
                    if (isset($pem2['child'])) {
                        foreach ($pem2['child'] as $pem3) {
                            //生成三级权限
                            $p3 = \App\Models\Permission::create([
                                'name' => $pem3['name'],
                                'display_name' => $pem3['display_name'],
                                'parent_id' => $p2->id,
                                'route' => $pem3['route']?:'',
                                'icon' => isset($pem3['icon'])?$pem3['icon']:'',
                            ]);
                            //为角色添加权限
                            $role->givePermissionTo($p3);
                            //为用户添加权限
                            $user->givePermissionTo($p3);
                        }
                    }

                }
            }
        }

        //为用户添加角色
        $user->assignRole($role);

        //初始化的角色
        $roles = [
            ['name' => 'editor', 'display_name' => '编辑人员'],
            ['name' => 'admin', 'display_name' => '管理员'],
        ];
        foreach ($roles as $role) {
            \App\Models\Role::create($role);
        }

        Artisan::call('cache:clear');

        echo '------------------------------'."\n";
        echo "url: ".env('APP_URL','http://localhost')."/admin \n";
        echo 'username: '.$setdata['username']."\n";
        echo 'password: '.$setdata['password']."\n";
        echo '------------------------------'."\n";
    }
}
