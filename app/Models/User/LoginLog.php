<?php


namespace App\Models\User;


use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{

    protected  $table = 'users_login_log';
    protected $fillable = ['id','uuid','agent','ip','message'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User','uuid','uuid');
    }
}
