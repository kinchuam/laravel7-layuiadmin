<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttachmentRoup extends Model
{
    protected $table = 'attachment_group';
    protected $fillable = ['name','sort'];
}