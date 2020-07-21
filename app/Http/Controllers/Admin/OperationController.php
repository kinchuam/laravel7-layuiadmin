<?php

namespace App\Http\Controllers\Admin;

use App\Models\Activitylog;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OperationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $users = User::query()->get(['id','name']);
        return view('admin.logs.operation.index',compact('users'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $model = Activitylog::query()->select(['id','log_name','description','causer_id','subject_type','created_at']);

        $res = $model->with('user:id,name')
            ->orderBy('id','desc')->paginate($request->get('limit',30))->toArray();

        $data = [
            'code' => 0,
            'msg'   => '正在请求中...',
            'count' => $res['total'],
            'data'  => $res['data']
        ];
        return response()->json($data);
    }

}
