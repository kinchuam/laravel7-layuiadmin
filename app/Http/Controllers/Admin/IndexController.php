<?php

namespace App\Http\Controllers\Admin;

use App\Models\AccessLog;
use App\Models\Article;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    /**
     * 后台布局
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function layout()
    {
        return view('admin.layout');
    }

    private function mysqlversion()
    {
        $mysqlv = DB::select('SELECT VERSION() as VERSION;');
        return $mysqlv[0]->VERSION;
    }

    /**
     * 后台首页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data = [
            'shortcut' => [
                [
                    ['title'=>'管理员', 'url'=>route('admin.user'), 'icon'=>'layui-icon-user'],
                    ['title'=>'日志管理', 'url'=>route('admin.operation'), 'icon'=>'layui-icon-log'],
                ],
            ],

            'data_counts' =>[
                [
                    ['title'=>'管理员 ', 'url'=>'', 'count'=> User::count()],
                    ['title'=>'日志 ', 'url'=>'', 'count'=> AccessLog::count()],
                ],
            ],

            'widget_config' => [
                ['Laravel', 'Web', '操作系统'],
                [app()->version(), request()->server('SERVER_SOFTWARE'), PHP_OS ],
                ['上传限制', 'PHP', 'Mysql'],
                [ini_get('upload_max_filesize'), phpversion(), $this->mysqlversion()],
                ['GD', 'PDO', 'CURL'],
                [extension_loaded('gd') ? 'YES' : 'NO', class_exists('pdo')?'YES':'NO', extension_loaded('curl') ? 'YES' : 'NO'],
            ]
        ];

        $loginlog = [];
        if (auth('admin')->user())
        {
            $uuid = auth('admin')->user()->uuid;
            $loginlog = User\LoginLog::where('uuid', trim($uuid))->orderBy('id','desc')->first();
        }

        return view('admin.index.index',compact('data','loginlog'));
    }

    public function search(Request $request)
    {
        $t1 = microtime(true);
        $data = $request->only(['keywords']);
        $list = Article::search(trim($data['keywords']))->where('status',1)->paginate($request->get('limit', 10))->toArray();

        $t2 = microtime(true);
        $time = $t2-$t1;
        return view('admin.index.sreach',compact('list','time'));
    }

    public function line_chart(Request $request)
    {
        $data1 = $this->Get_platform_count();
        $data2 = $this->Get_browser_count();

        $data = [
            'code' => 0,
            'msg' => '请求成功',
            'data1' => $data1,
            'data2' => $data2,
        ];
        return response()->json($data);
    }

    public function Get_platform_count($limit=5)
    {
        $names = [];
        $acounts = [];
        $dates = [];
        $weekarray = ['周日','周一','周二','周三','周四','周五','周六'];

        $list  = AccessLog::distinct('platform')->limit($limit)->select('platform')->get()->toArray();

        if (!empty($list)){
            foreach ($list as $key => $row)
            {
                $names[] = empty($row['platform'])?'其他':$row['platform'];
                $i = 6;
                while (0 <= $i) {
                    $time = date('Y-m-d', strtotime('-' . $i . ' day'));
                    $start_time = $time . ' 00:00:00';
                    $end_time = $time . ' 23:59:59';
                    $dates[] = $weekarray[date("w",strtotime($time))];
                    $acounts[$key][] = AccessLog::where('platform',$row['platform'])->whereBetween('created_at',[$start_time,$end_time])->count();
                    --$i;
                }
            }
        }

        $data = [
            'charttitle' => '操作系统统计图',
            'names' => $names,
            'dates' => $dates,
            'acounts' => $acounts
        ];
        return $data;
    }

    public function Get_browser_count($limit = 5)
    {
        $names = [];
        $datas = [];

        $list  = AccessLog::distinct('browser')->limit($limit)->select('browser')->get()->toArray();
        if (!empty($list)){
            foreach ($list as $row)
            {
                $names[] = empty($row['browser'])?'其他':$row['browser'];
                $datas[] = [
                    'name' => empty($row['browser'])?'其他':$row['browser'],
                    'value' => AccessLog::where('browser',$row['browser'])->count()
                ];
            }
        }
        $data = [
            'charttitle' => '各浏览器分布图',
            'names' => $names,
            'datas' => $datas
        ];
        return $data;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 数据表格接口
     */
    public function data(Request $request)
    {
        $model = $request->get('model');
        switch (strtolower($model)) {
            case 'user':
                $query = new User();
                break;
            case 'role':
                $query = new Role();
                break;
            case 'permission':
                $query = new Permission();
                $query = $query->where('parent_id', $request->get('parent_id', 0));
                break;
            default:
                $query = new User();
                break;
        }
        $res = $query->paginate($request->get('limit', 20))->toArray();
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res['total'],
            'data' => $res['data']
        ];
        return response()->json($data);
    }

}
