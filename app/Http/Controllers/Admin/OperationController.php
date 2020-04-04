<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class OperationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $users = User::select()->get();
        return view('admin.logs.operation.index',compact('users'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $model = Activity::query();

        $res = $model->orderBy('id','desc')->paginate($request->get('limit',30))->toArray();
        foreach ($res['data'] as &$row) {
            $user = User::select('name')->where('id',$row['causer_id'])->first();
            $row['created_at'] = Carbon::parse($row['created_at'])->format('Y-m-d H:i:s');
            $row['username'] = $user['name']??"未知用户";
            $a = json_encode($row['properties'],JSON_UNESCAPED_UNICODE);
            $row['code'] = "{$a}";
        }
        unset($row);
        $data = [
            'code' => 0,
            'msg'   => '正在请求中...',
            'count' => $res['total'],
            'data'  => $res['data']
        ];
        return response()->json($data);
    }

}
