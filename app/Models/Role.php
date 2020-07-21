<?php

namespace App\Models;


use DateTimeInterface;

class Role extends \Spatie\Permission\Models\Role
{
    //
    protected $guard_name = 'admin';
    public $desc = '角色表';
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
}
