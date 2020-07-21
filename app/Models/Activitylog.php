<?php


namespace App\Models;


use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Activitylog extends Model
{

    protected $table = 'activity_log';
    protected $fillable = [
        'log_name',
        'description',
        'subject_id',
        'subject_type',
        'causer_id',
        'causer_type',
        'properties',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }

    public static function addlog($event = [])
    {
        if (request()->route()) {
            $arr = request()->route()->gatherMiddleware();
            if (is_array($arr)) {
                $a = end($arr);
                $b = explode(':',$a);
                $user = auth('admin')->check()?auth('admin')->user():[];
                if (is_array($b) && isset($b[0]) && $b[0] == 'permission') {
                    $logname = $b[1];
                    activitylog::create([
                        'log_name' => $logname,
                        'description' => $event['title'],
                        'subject_id' => $event['model']->id,
                        'subject_type' => get_class($event['model']),
                        'causer_id' => !empty($user->id)?$user->id:0,
                        'causer_type' => !empty($user)?get_class($user):'',
                        'properties' => json_encode($event['content']),
                    ]);
                }
            }
        }

    }

    public function user()
    {
        return $this->hasOne('App\Models\User','id','causer_id');
    }
}
