<?php

namespace App\Models;

class Permission extends \Spatie\Permission\Models\Permission
{
    protected $guard_name = 'admin';
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
    //子权限
    public function childs()
    {
        return $this->hasMany(self::class,'parent_id','id');
    }
}
