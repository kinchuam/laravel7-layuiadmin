<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Article extends Model
{
    use SoftDeletes,Searchable;
    protected $fillable = [
        'category_id','title','tags','keywords','description','content','thumb','click','author','status','sort','ishelp','ishome','created_at'
    ];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
    protected $attributes = [
        'ishelp' => 0,
        'ishome' => 0,
        'content' => '',
    ];
    public $asYouType = true;

    //文章所属分类
    public function category()
    {
        return $this->belongsTo('App\Models\Article\Category');
    }

    /**
     * 获取模型的可搜索数据
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'keywords' => $this->keywords,
            'content' => $this->content,
            'tags' => $this->tags,
            'created_at' => $this->created_at,
            'click' => $this->click,
            'thumb' => $this->thumb,
            'status' => $this->status,
            'author' => $this->author,
        ];
        return $data;
    }

    public function searchableAs()
    {
        return 'article_index';
    }

}
