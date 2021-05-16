<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ApiController extends BaseController
{
    use DispatchesJobs, ValidatesRequests;


    public function app_json($data = [],$status = 0): \Illuminate\Http\JsonResponse
    {
        $ret = [];
        if (!isset($data['error'])){
            $ret["error"] = 0;
        }
        if (!isset($data['message'])){
            $ret["message"] = 'succeed';
        }
        if ($status > 0){
            return response()->json(array_merge($ret, $data), $status);
        }
        return response()->json(array_merge($ret, $data));
    }

}
