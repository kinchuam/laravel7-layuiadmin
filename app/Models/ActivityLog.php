<?php


namespace App\Models;


use DateTimeInterface;
use Illuminate\Support\Arr;

class ActivityLog extends \Spatie\Activitylog\Models\Activity
{
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }

    public static function addLog($desc = '', $properties = [], $model = null, $causedUser = null, $logName = 'Syslog')
    {
        $causedUser = !empty($causedUser) ? $causedUser : auth('admin')->user();
        $properties = Arr::except($properties, ['id', 'uuid', 'password', 'created_at', 'updated_at']);
        $res = activity()->inLog($logName);
        if (!empty($model)) {
            $res = $res->performedOn($model);
        }
        return $res->causedBy($causedUser)->withProperties($properties)->log($desc);
    }

    public static $tablesDesc = [
        "App\Models\System\User" => "管理员表",
        "App\Models\System\Permission" => "系统权限表",
        "App\Models\System\Role" => "系统角色表",
        "App\Models\Site" => "系统配置表",
        "App\Models\Content\Attachment" => "系统附件表",
        "App\Models\Content\AttachmentGroup" => "附件分组表",
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\User','causer_id');
    }
}
