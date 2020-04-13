<?php

namespace App\Http\Controllers\Admin;

use App\Models\Operation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Venturecraft\Revisionable\Revision;

class OperationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $users = User::query()->get();
        return view('admin.logs.operation.index',compact('users'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Request $request)
    {
        $model = Operation::query();

        $res = $model->with('user:id,name')->orderBy('id','desc')->paginate($request->get('limit',30))->toArray();
        foreach ($res['data'] as &$row) {
            $row['created_at'] = Carbon::parse($row['created_at'])->format('Y-m-d H:i:s');
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
