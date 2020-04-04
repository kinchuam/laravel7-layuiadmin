<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class DefaultLoggable
{
    use SerializesModels;

    public $title;
    public $content;
    public $user;
    public $model;

    public function __construct(string $title, array $content,$user = null, $model = null)
    {
        $this->title = $title;
        $this->content = $content;
        $this->user = $user ? $user : (Auth('admin')->check() ? Auth('admin')->user() : 0);
        $this->model = $model;
    }
}
