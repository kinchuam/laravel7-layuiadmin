<?php

namespace App\Models\Article;

use Illuminate\Database\Eloquent\Model;
use Venturecraft\Revisionable\RevisionableTrait;

class Category extends Model
{
    use RevisionableTrait;
    protected $table = 'article_category';
    protected $fillable = ['name','sort','parent_id'];
    protected $attributes = [
        'parent_id' => 0
    ];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    //子分类
    public function childs()
    {
        return $this->hasMany(self::class,'parent_id','id');
    }

    //所有子类
    public function allChilds()
    {
        return $this->childs()->with('allChilds');
    }

    //分类下所有的文章
    public function articles()
    {
        return $this->hasMany('App\Models\Article');
    }

}
