<?php

namespace App\Models;


use DateTimeInterface;

class Permission extends \Spatie\Permission\Models\Permission
{
    protected $guard_name = 'admin';
    public $desc = '权限表';
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
    //子权限
    public function childs()
    {
        return $this->hasMany(self::class,'parent_id','id');
    }
}
