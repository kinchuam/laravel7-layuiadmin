<?php

namespace App\Models;


use DateTimeInterface;

class Role extends \Spatie\Permission\Models\Role
{
    protected $guard_name = 'admin';

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
}
