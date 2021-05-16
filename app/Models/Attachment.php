<?php
namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use SoftDeletes;
    protected $table = 'attachment';
    protected $fillable = ['filename','path','suffix','group_id','type','size','uuid','storage','file_url'];

    const image_size = 2048;
    const image_type = ["jpg", "jpeg", "png", "gif", "webp", "avif"];
    const file_size = 5120;
    const file_type = ['mp3', 'mp4'];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
    //获取分组
    public function group(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\AttachmentGroup');
    }
    //批量移动文件分组
    public function moveGroup($group_id, $fileIds = []): bool
    {
        if (!is_numeric($group_id) || empty($fileIds) || !is_array($fileIds)) {
            return false;
        }
        $this->query()->whereIn('id', $fileIds)->update(['group_id' => $group_id]);
        $this['id'] = $group_id;
        return true;
    }

    /**
     * @param array $fileInfo
     * @param false $is_plural
     * @return Attachment|bool
     */
    public static function addUploadFile(array $fileInfo, $is_plural = false)
    {
        if (empty($fileInfo)) { return false; }
        try {
            if ($is_plural) {
                foreach ($fileInfo as $data) {
                    $res = self::create($data);
                    ActivityLog::addLog('添加附件 ID:'.$res['id'], $data, $res);
                }
                return true;
            }
            $data = [
                'group_id' => isset($fileInfo['group_id']) ? intval($fileInfo['group_id']) : 0,
                'storage' => $fileInfo['storage'] ?? 'local',
                'file_url' => $fileInfo['file_url'] ?? '',
                'path' => $fileInfo['file_path'],
                'filename' => $fileInfo['file_name'],
                'size' => $fileInfo['file_size'],
                'suffix' => $fileInfo['extension'],
                'type' => $fileInfo['file_type'],
                'uuid' => empty($fileInfo['uuid']) ? '' : $fileInfo['uuid'],
            ];
            $model = self::create($data);
            ActivityLog::addLog('添加附件 ID:'.$model['id'], $data, $model);
            return $model;
        }catch (\Exception $e) {
            logger()->error('addUploadFile: '.$e->getMessage());
        }
        return false;
    }
}
