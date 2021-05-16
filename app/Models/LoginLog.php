<?php


namespace App\Models;


use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    protected  $table = 'users_login_log';

    protected $fillable = ['username', 'ip', 'message', 'platform', 'browser', 'ip_data', 'agent'];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }

}
