<?php
namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class AttachmentGroup extends Model
{
    protected $table = 'attachment_group';
    protected $fillable = ['name','sort'];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('App\Models\Content\Attachment', 'group_id', 'id');
    }
}
