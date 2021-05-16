<?php


namespace App\Http\Controllers\Admin\Logs;


use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;

class LoginLogController extends Controller
{
    public function index()
    {
        $users = User::query()->get(['id','username as name'])->pluck('name','id');
        return view('admin.logs.loginLog.index', compact('users'));
    }

    public function data(Request $request): \Illuminate\Http\JsonResponse
    {
        $model = LoginLog::query()->select(['id','uuid','ip','agent','message','ipData','created_at']);

        if ($username = trim($request->get('username'))){
            $model->where('username', trim($username));
        }
        if ($ip = $request->get('ip')){
            $model->where('ip','like',$ip.'%');
        }
        $res = $model->orderBy('id','desc')->paginate($request->get('limit', 10))->toArray();
        $list = $res['data'];
        if (!empty($list)) {
            foreach ($list as $ke => $row) {
                if (!empty($row['ip_data'])) {
                    $ip_data = json_decode($row['ip_data'], true);
                    $list[$ke]['ip_data'] = explode('|', $ip_data['region']);
                }
            }
        }
        return response()->json([
            'code' => 0,
            'message' => '正在请求中...',
            'count' => $res['total'],
            'data' => $res['data']
        ]);
    }

}
