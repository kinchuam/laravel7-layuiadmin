<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttachmentRoup extends Model
{
    protected $table = 'attachment_group';
    public $desc = '附件分组表';
    protected $fillable = ['name','sort'];
}
