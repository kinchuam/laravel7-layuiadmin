<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;

class IndexController extends ApiController
{

    public function index(): \Illuminate\Http\JsonResponse
    {
        return $this->app_json();
    }

}
