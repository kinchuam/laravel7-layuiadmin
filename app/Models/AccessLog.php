<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    protected $table = 'access_log';

    protected $fillable = ['path', 'method', 'input', 'type', 'ip', 'platform', 'browser', 'ip_data', 'agent'];

    protected $attributes = [
        'agent' => '',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }

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
