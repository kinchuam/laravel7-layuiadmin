<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    protected $table = 'access_log';
    protected $fillable = ['path', 'method', 'ip','type', 'input','agent','platform','browser','ipdata'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
    protected $attributes = [
        'ipdata' => '',
    ];

    public static $methodColors = [
        'GET'    => '#43d543',
        'POST'   => '#75751c',
        'PUT'    => 'blue',
        'DELETE' => 'red',
        'OPTIONS' => 'hotpink',
        'PATCH' => 'thistle',
        'LINK' => 'mintcream',
        'UNLINK' => 'firebrick',
        'COPY' => 'lightcyan',
        'HEAD' => 'gray',
        'PURGE' => 'copper',
    ];

    public static $methods = [
        'GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH','LINK', 'UNLINK', 'COPY', 'HEAD', 'PURGE'
    ];

}
