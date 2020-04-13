<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Venturecraft\Revisionable\RevisionableTrait;

class AttachmentRoup extends Model
{
    use RevisionableTrait;
    protected $table = 'attachment_group';
    protected $fillable = ['name','sort'];
}
