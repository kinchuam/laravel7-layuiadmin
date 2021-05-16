<?php

namespace App\Models;


use DateTimeInterface;

class Permission extends \Spatie\Permission\Models\Permission
{
    protected $guard_name = 'admin';

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
    //子权限
    public function childs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(self::class,'parent_id','id');
    }
}
