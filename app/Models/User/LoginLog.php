<?php


namespace App\Models\User;


use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{

    protected  $table = 'users_login_log';
    protected $fillable = ['id','uuid','ip','agent','message','ipData'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User','uuid','uuid');
    }
}
