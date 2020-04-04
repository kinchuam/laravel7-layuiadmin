<?php

namespace App\Models;


class Role extends \Spatie\Permission\Models\Role
{
    //
    protected $guard_name = 'admin';
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
