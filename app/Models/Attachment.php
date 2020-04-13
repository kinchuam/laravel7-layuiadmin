<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Venturecraft\Revisionable\RevisionableTrait;

class Attachment extends Model
{
    use SoftDeletes,RevisionableTrait;
    protected $table = 'attachment';
    protected $fillable = ['filename','path','suffix','group_id','type','size','uuid','storage','file_url'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

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
