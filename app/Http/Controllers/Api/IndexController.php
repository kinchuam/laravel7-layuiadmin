<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class IndexController extends  Controller
{

    public function index()
    {
        return $this->app_json();
    }

}
