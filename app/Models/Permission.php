<?php

namespace App\Models;

use Venturecraft\Revisionable\RevisionableTrait;

class Permission extends \Spatie\Permission\Models\Permission
{
    use RevisionableTrait;
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
