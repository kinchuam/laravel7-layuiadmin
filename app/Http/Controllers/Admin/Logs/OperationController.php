<?php

namespace App\Http\Controllers\Admin\Logs;

use App\Models\Activitylog;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OperationController extends Controller
{
    public function index()
    {
        $users = User::query()->get(['id','name'])->toArray();
        return view('admin.logs.operation.index', compact('users'));
    }

    public function data(Request $request): \Illuminate\Http\JsonResponse
    {
        $model = ActivityLog::query()->select(['id','log_name','description','causer_id','subject_type','properties','created_at']);
        if ($causer_id = $request->get('causer_id')) {
            $model->where('causer_id', intval($causer_id));
        }

        $res = $model->with('user:id,name')
            ->orderBy('id','desc')->paginate($request->get('limit',10))->toArray();

        $tables_desc = ActivityLog::$tablesDesc;
        $list = $res['data'];
        if (!empty($list)) {
            foreach ($list as $ke => $va) {
                $list[$ke]['subject_type'] = $tables_desc[$va['subject_type']] ?? $va['subject_type'];
            }
        }
        return response()->json([
            'code' => 0,
            'message'   => '正在请求中...',
            'count' => $res['total'],
            'data'  => $res['data']
        ]);
    }

}
