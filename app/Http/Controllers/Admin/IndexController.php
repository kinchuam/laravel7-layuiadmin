<?php

namespace App\Http\Controllers\Admin;

use App\Models\AccessLog;
use App\Models\LoginLog;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{

    public function layout()
    {
        return view('admin.layout');
    }

    public function index()
    {
        $data = [
            'shortcut' => [
                [
                    ['title' => '管理员', 'url' => route('admin.user'), 'icon' => 'layui-icon-user'],
                    ['title' => '角色管理', 'url' => route('admin.role'), 'icon' => 'layui-icon-group'],
                    ['title' =>'日志管理', 'url' => route('admin.operation'), 'icon' => 'layui-icon-log'],
                    ['title' => '上传设置', 'url' => route('admin.attachment'), 'icon' => 'layui-icon-set-fill'],
                ],
            ],

            'data_counts' =>[
                [
                    ['title'=>'日志 ', 'url'=>'', 'count'=> AccessLog::query()->count()],
                ],
            ],

            'widget_config' => [
                ['Laravel', 'Web', '操作系统'],
                [app()->version(), request()->server('SERVER_SOFTWARE'), PHP_OS ],
                ['上传限制', 'PHP', 'Mysql'],
                [ini_get('upload_max_filesize'), phpversion(), $this->mysqlVersion()],
                ['GD', 'PDO', 'CURL'],
                [extension_loaded('gd') ? 'YES' : 'NO', class_exists('pdo')?'YES':'NO', extension_loaded('curl') ? 'YES' : 'NO'],
            ]
        ];

        $user = [];
        if (auth('admin')->check()) {
            $user = auth('admin')->user();
            if (!empty($user->username)) {
                $loginLog = LoginLog::where('username', $user->username)->orderBy('id', 'desc')->first(['ip','ip_data']);
                $user['ip'] = $loginLog['ip'] ?? '127.0.0.1';
                if (!empty($loginLog['ip_data'])) {
                    if ($ip_data = json_decode($loginLog['ip_data'], true)) {
                        $user['ip'] = $loginLog['ip'].' '.trim($ip_data['region']);
                    }
                }
            }
        }

        return view('admin.index.index', compact('data','user'));
    }

    private function mysqlVersion()
    {
        $sql = DB::select('SELECT VERSION() as VERSION;');
        return $sql[0]->VERSION;
    }

    public function line_chart(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'code' => 0,
            'message' => '请求成功',
            'data' => [
                'platform' => $this->Get_platform(),
                'browser' => $this->Get_browser(),
            ],
        ]);
    }

    public function Get_platform($day = 6): array
    {
        $start = Carbon::now()->subDays($day);
        $end  = Carbon::today();
        $pvs = AccessLog::query()->whereBetween('created_at',  [$start, $end])->select(DB::raw('SQL_CACHE DATE_FORMAT(`created_at`, "%Y-%m-%d") as date_t, COUNT(*) as total'))
            ->groupBy(['date_t'])->get()->toArray();

        $subQuery = AccessLog::query()->whereBetween('created_at',  [$start, $end])->select(DB::raw('DATE_FORMAT(`created_at`, "%Y-%m-%d") as date_t, COUNT(*) as total'))
            ->groupBy(['date_t','ip']);
        $uvs = DB::table(DB::raw("({$subQuery->toSql()}) as sub"))->mergeBindings($subQuery->getQuery())
            ->select(DB::raw('SQL_CACHE date_t , COUNT(*) as total'))->groupBy('date_t')->get()->toArray();

        $i = 7;$transaction = [];
        while (1 <= $i) {
            $key = date('Y-m-d', time() - $i * 3600 * 24);
            $transaction['pv'][$key] = 0;
            $transaction['uv'][$key] = 0;
            --$i;
        }

        if (!empty($pvs)) {
            foreach ($pvs as $k => $v) {
                $transaction['pv'][$v['date_t']] = $v['total'];
            }
        }
        if (!empty($uvs)) {
            foreach ($uvs as $k => $v) {
                $transaction['uv'][$v->date_t] = $v->total;
            }
        }

        return [
            'title' => '访问量统计',
            'keys' => array_keys($transaction['pv']),
            'pv' => array_values($transaction['pv']),
            'uv' => array_values($transaction['uv'])
        ];
    }

    public function Get_browser($limit = 5): array
    {
        $keys = [];$data = [];
        $list = AccessLog::query()->select('browser', DB::raw('COUNT(`id`) as count'))
            ->groupBy(['browser'])->take($limit)->get()->toArray();

        if (!empty($list)) {
            foreach ($list as $row) {
                $browser = empty($row['browser'])?'Other':$row['browser'];
                $keys[] = $browser;
                $data[] = [
                    'name' => $browser,
                    'value' => $row['count']
                ];
            }
        }
        return [
            'title' => '浏览器分布图',
            'keys' => $keys,
            'data' => $data
        ];
    }

}
