<?php
namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use SoftDeletes;
    public $desc = '附件表';
    protected $table = 'attachment';
    protected $fillable = ['filename','path','suffix','group_id','type','size','uuid','storage','file_url'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
    //获取分组
    public function group()
    {
        return $this->belongsTo('App\Models\AttachmentRoup');
    }
    /**
     * 批量移动文件分组
     */
    public function moveGroup($group_id, $fileIds)
    {
        if (empty($group_id)) return false;
        if (empty($fileIds)) return false;
        return $this->whereIn('id', $fileIds)->update(['group_id'=>$group_id]);
    }
}
