<?php

namespace App\Http\Controllers\Admin;

use App\Models\AccessLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
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
                    ['title'=>'附件','url'=>route('admin.files'),'icon'=>'layui-icon-file'],
                    ['title'=>'设置','url'=>route('admin.site'),'icon'=>'layui-icon-set'],
                    ['title'=>'管理员', 'url'=>route('admin.user'), 'icon'=>'layui-icon-user'],
                    ['title'=>'日志管理', 'url'=>route('admin.operation'), 'icon'=>'layui-icon-log'],
                ],
            ],

            'data_counts' =>[
                [
                    ['title'=>'日志 ', 'url'=>'', 'count'=> AccessLog::query()->count()],
                ],
                [
                    ['title'=>'管理员 ', 'url'=>'', 'count'=> User::query()->count()],
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
        if (auth('admin')->user()) {
            $uuid = auth('admin')->user()->uuid;
            $loginlog = User\LoginLog::where('uuid', trim($uuid))->orderBy('id','desc')->first(['ip','ipData','created_at']);
            $loginlog['ipData'] = json_decode($loginlog['ipData'],true);
        }

        return view('admin.index.index', compact('data','loginlog'));
    }

    public function search(Request $request)
    {
        if ($request->ajax()) {
            $get = $request->only(['keywords']);
            $list = [];
            return response()->json([
                'code' => 0,
                'msg' => '请求成功',
                'data' => $list,
            ]);
        }
        return view('admin.index.sreach');
    }

    public function line_chart(Request $request)
    {
        $data1 = $this->Get_platform_count();
        $data2 = $this->Get_browser_count();

        return response()->json([
            'code' => 0,
            'msg' => '请求成功',
            'data1' => $data1,
            'data2' => $data2,
        ]);
    }

    public function Get_platform_count($limit=5)
    {
        $weekarray = ['周日','周一','周二','周三','周四','周五','周六'];

        $list  = AccessLog::whereBetween('created_at',[Carbon::today()->subDays(count($weekarray)), Carbon::now()])
            ->select('platform', DB::raw('DATE(created_at) as f_date'))->get()->toArray();

        $dates = [];$names = [];$new_arr = [];
        if (!empty($list)) {
            $arr = [];
            foreach ($list as $row) {

                $platform = $row['platform'] = empty($row['platform'])?'其他':$row['platform'];
                $f_date = $row['f_date'];
                $i = count($weekarray)-1;

                while (0 <= $i) {
                    $date = date('Y-m-d', strtotime('-' . $i . ' day'));
                    $dates[$i] = $date;
                    $count = $arr[$platform][$i]??0;

                    if ($date == $f_date) {
                        $count++;
                    }

                    $arr[$platform][$i] = $count;
                    --$i;
                }
            }

            $arr = array_slice($arr, 0 , $limit);
            foreach ($arr as $key => $row) {
                $names[] = $key;
                $new_arr[] = Arr::flatten($row);
            }
            $dates = Arr::flatten($dates);
        }

        return [
            'charttitle' => '操作系统统计图',
            'names' => $names,
            'dates' => $dates,
            'acounts' => $new_arr
        ];
    }

    public function Get_browser_count($limit = 5)
    {
        $names = [];$datas = [];
        $list  =  AccessLog::select('browser',DB::raw('COUNT(id) as count'))->groupBy('browser')->take($limit)->get()->toArray();

        if (!empty($list)) {
            foreach ($list as $row) {
                $browser = empty($row['browser'])?'其他':$row['browser'];
                $names[] = $browser;
                $datas[] = [
                    'name' => $browser,
                    'value' => $row['count']
                ];
            }
        }

        return [
            'charttitle' => '各浏览器分布图',
            'names' => $names,
            'datas' => $datas
        ];
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
                $query = User::query()->select(['id','username','name','phone','email','created_at','updated_at']);
                break;
            case 'role':
                $query = \App\Models\Role::query()->select(['id','name','display_name','created_at','updated_at']);
                break;
            case 'permission':
                $query = \App\Models\Permission::query();
                break;
            default:
                $query = User::query()->select(['id','username','name','phone','email','created_at','updated_at']);
                break;
        }

        if (strtolower($model) == 'permission') {
            $res = $query->get(['id','name','display_name','route','icon','parent_id','created_at','updated_at'])->toArray();
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'data' => $res
            ];
        }else{
            $res = $query->paginate($request->get('limit', 20))->toArray();
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res['total'],
                'data' => $res['data']
            ];
        }
        return response()->json($data);
    }

}
