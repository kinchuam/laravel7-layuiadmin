<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('users')->truncate();
        DB::table('users_login_log')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = [
            'username' => 'root',
            'password' => 'root123',
        ];
        //用户
        $user = \App\Models\User::create([
            'username' => $data['username'],
            'phone' => '12888888888',
            'name' => '超级管理员',
            'email' => 'root@xxx.com',
            'password' => bcrypt($data['password']),
            'uuid' => Str::uuid()
        ]);

        //角色
        $role = \App\Models\Role::create([
            'name' => 'root',
            'display_name' => '超级管理员'
        ]);

        //权限
        $permissions = $this->getPermissions();

        foreach ($permissions as $pem1) {
            //生成一级权限
            $p1 = $this->addPermissions(0, $pem1, $role);
            if (isset($pem1['child'])) {
                foreach ($pem1['child'] as $pem2) {
                    //生成二级权限
                    $p2 = $this->addPermissions($p1['id'], $pem2, $role);
                    if (isset($pem2['child'])) {
                        foreach ($pem2['child'] as $pem3) {
                            //生成三级权限
                            $this->addPermissions($p2['id'], $pem3, $role);
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
        echo "url: ".config('app.url')."/admin \n";
        echo 'username: '.$data['username']."\n";
        echo 'password: '.$data['password']."\n";
        echo '------------------------------'."\n";
    }

    protected function addPermissions($parent_id, $pem, $role)
    {
        $item = \App\Models\Permission::create([
            'name' => $pem['name'],
            'display_name' => $pem['display_name'],
            'parent_id' => intval($parent_id),
            'route' => $pem['route']?:'',
            'icon' => $pem['icon'] ?? '',
        ]);
        //为角色添加权限
        $role->givePermissionTo($item);
        return $item;
    }


    protected function getPermissions()
    {
        return [
            [
                'name' => 'content.manage',
                'display_name' => '内容管理',
                'route' => '',
                'icon' => 'layui-icon-read',
                'child' => [
                    [
                        'name' => 'content.files',
                        'display_name' => '附件管理',
                        'route' => 'admin.content.files',
                        'icon' => 'layui-icon-auz',
                        'genre' => 1,
                        'child' => [
                            ['name' => 'content.files.create', 'display_name' => '添加附件', 'route'=>'admin.content.files.create'],
                            ['name' => 'content.files.destroy', 'display_name' => '删除附件', 'route'=>'admin.content.files.destroy'],
                            ['name' => 'content.files.recycle', 'display_name' => '回收站', 'route'=>'admin.content.files.recycle'],
                            ['name' => 'content.files.recover', 'display_name' => '回收站恢复', 'route'=>'admin.content.files.recover'],
                            ['name' => 'content.files.expurgate', 'display_name' => '回收站删除', 'route'=>'admin.content.files.expurgate'],
                        ]
                    ],
                    [
                        'name' => 'content.files_group',
                        'display_name' => '附件分组',
                        'route' => 'admin.content.files_group',
                        'icon' => 'layui-icon-auz',
                        'genre' => 1,
                        'child' => [
                            ['name' => 'content.files_group.create', 'display_name' => '添加分组', 'route' => 'admin.content.files_group.create'],
                            ['name' => 'content.files_group.edit', 'display_name' => '编辑分组', 'route' => 'admin.content.files_group.edit'],
                            ['name' => 'content.files_group.destroy', 'display_name' => '删除分组', 'route' => 'admin.content.files_group.destroy'],
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
                        'display_name' => '账号管理',
                        'route' => 'admin.user',
                        'icon' => 'layui-icon-friends',
                        'genre' => 1,
                        'child' => [
                            ['name' => 'system.user.create', 'display_name' => '添加账号', 'route'=>'admin.user.create'],
                            ['name' => 'system.user.edit', 'display_name' => '编辑账号', 'route'=>'admin.user.edit'],
                            ['name' => 'system.user.destroy', 'display_name' => '删除账号', 'route'=>'admin.user.destroy'],
                            ['name' => 'system.user.role', 'display_name' => '分配角色', 'route'=>'admin.user.role'],
                            ['name' => 'system.user.permission', 'display_name' => '分配权限', 'route'=>'admin.user.permission'],
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
                        'genre' => 1,
                        'child' => [
                            ['name' => 'config.site.update', 'display_name' => '更新配置', 'route'=>'admin.site.update'],
                        ]
                    ],
                    [
                        'name' => 'config.attachment',
                        'display_name' => '上传配置',
                        'route' => 'admin.attachment',
                        'icon' => 'layui-icon-set-fill',
                        'genre' => 1,
                        'child' => [
                            ['name' => 'config.attachment.update', 'display_name' => '更新配置', 'route'=>'admin.attachment.update'],
                        ]
                    ],
                    [
                        'name' => 'config.dateCache',
                        'display_name' => '更新缓存',
                        'route' => 'admin.dateCache',
                        'icon' => 'layui-icon-set-fill',
                        'genre' => 1,
                        'child' => [
                            ['name' => 'config.clearCache', 'display_name' => '更新缓存', 'route'=>'admin.clearCache'],
                        ]
                    ],
                    [
                        'name' => 'config.optimize',
                        'display_name' => '配置信息',
                        'route' => 'admin.optimize',
                        'icon' => 'layui-icon-set-fill',
                        'genre' => 1,
                        'child' => []
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
                        'genre' => 1,
                        'child' => []
                    ],
                    [
                        'name' => 'logs.access',
                        'display_name' => '访问日志',
                        'route' => 'admin.access',
                        'icon' => 'layui-icon-set',
                        'genre' => 1,
                        'child' => [
                            ['name' => 'logs.access.show', 'display_name' => '查看日志', 'route'=>'admin.access.show'],
                        ]
                    ],
                    [
                        'name' => 'logs.loginLog',
                        'display_name' => '登录日志',
                        'route' => 'admin.loginLog',
                        'icon' => 'layui-icon-set',
                        'genre' => 1,
                        'child' => []
                    ],
                    [
                        'name' => 'logs.error',
                        'display_name' => '系统日志',
                        'route' => 'admin.logs',
                        'icon' => 'layui-icon-set',
                        'genre' => 1,
                        'child' => []
                    ],
                ]
            ],
        ];
    }

}
