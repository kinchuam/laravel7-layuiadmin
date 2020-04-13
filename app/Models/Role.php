<?php

namespace App\Models;


use Venturecraft\Revisionable\RevisionableTrait;

class Role extends \Spatie\Permission\Models\Role
{
    //
    use RevisionableTrait;
    protected $guard_name = 'admin';
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
