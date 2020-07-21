<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Database;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{

    public function index()
    {
        return view('admin.database.index');
    }

    public function data()
    {
        $list = DB::select('SHOW TABLE STATUS');
        $list = array_map('array_change_key_case',array_map('get_object_vars', $list));
        $data = [
            'code' => 0,
            'msg' => '请求成功',
            'data' => $list,
        ];
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $PD = $request->only('tables', 'id', 'start');
        //表名
        $tables = isset($PD['tables'])?$PD['tables']:[];
        //表ID
        $id = isset($PD['id'])?$PD['id']:null;
        //起始行数
        $start = isset($PD['start'])?$PD['start']:null;
        if ( !empty($tables) && is_array($tables) ) {
            //读取备份配置
            $config = config('custom.DatabaseBackup');
            if (!is_dir($config['path'])) {
                mkdir($config['path'], 0755, true);
            }
            //检查是否有正在执行的任务
            $lock = "{$config['path']}backup.lock";
            if (!empty(Cache::get($lock))) {
                return response()->json(['status' => 'fail', 'message' => '检测到有一个备份任务正在执行，请稍后再试！']);
            } else {
                //创建锁文件
                Cache::put($lock, time(), Carbon::now()->addMinutes(2));
            }
            //检查备份目录是否可写
            if (!is_writeable($config['path'])) {
                return response()->json(['status' => 'fail', 'message' => '备份目录不存在或不可写，请检查后重试！']);
            }
            Cache::put('backup_config', $config, Carbon::now()->addMinutes(2));
            //生成备份文件信息
            $file = [
                'name' => date('Ymd-His', time()),
                'part' => 1,
            ];
            Cache::put('backup_file', $file, Carbon::now()->addMinutes(2));
            //缓存要备份的表
            Cache::put('backup_tables', $tables, Carbon::now()->addMinutes(2));
            //创建备份文件
            $Database = new Database($file, $config);
            if (false !== $Database->create()) {
                $tab = ['id' => 0, 'start' => 0];
                return response()->json(['status' => 'success', 'message' => '初始化成功！', 'rate' => '0%', 'data' => ['tables' => $tables, 'tab' => $tab]]);
            } else {
                return response()->json(['status' => 'fail', 'message' => '初始化失败，备份文件创建失败！']);
            }
        } elseif ( is_numeric($id) && is_numeric($start)) {
            //备份数据
            $tables = Cache::get('backup_tables');
            //备份指定表
            $Database = new Database(Cache::get('backup_file'), Cache::get('backup_config') );
            $start = $Database->backup($tables[$id], $start);
            if (false === $start) {
                //出错
                return response()->json(['status' => 'fail', 'message' => '备份出错！']);
            } elseif (0 === $start) {
                //下一表
                if (isset($tables[++$id])) {
                    $tab = ['id' => $id, 'start' => 0];
                    return response()->json(['status' => 'success', 'message' => "备份完成！", 'rate' => '100%', 'data' => ['tab' => $tab]]);
                } else {
                    //备份完成，清空缓存
                    Cache::forget(Cache::get('backup_config')['path'] . 'backup.lock');
                    Cache::forget('backup_tables');
                    Cache::forget('backup_file');
                    Cache::forget('backup_config');
                    return response()->json(['status' => 'success', 'message' => "备份完成！", 'rate' => '100%']);
                }
            } else {
                $tab = ['id' => $id, 'start' => $start[0]];
                $rate = floor(100 * ($start[0] / $start[1]));
                return response()->json(['status' => 'success', 'message' => "正在备份...({$rate}%)", 'rate' => $rate.'%', 'data' => ['tab' => $tab]]);
            }
        } else {
            return response()->json(['status' => 'fail', 'message' => '备份失败']);
        }
    }

    public function restore_index()
    {
        return view('admin.database.restore_index');
    }

    public function restore_data()
    {
        //列出备份文件列表
        $config = config('custom.DatabaseBackup');
        $path = $config['path'];
        $glob = glob($path . '*.gz', GLOB_BRACE);
        $list = [];
        foreach ($glob as $key => $file) {
            $fileInfo = pathinfo($file);
            //文件名
            $name = $fileInfo['basename'];
            if (preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql(?:\.gz)?$/', $name)) {
                $name = sscanf($name, '%4s%2s%2s-%2s%2s%2s-%d');

                $date = "{$name[0]}-{$name[1]}-{$name[2]}";
                $time = "{$name[3]}:{$name[4]}:{$name[5]}";
                $part = $name[6];

                if (isset($list["{$date} {$time}"])) {
                    $info = $list["{$date} {$time}"];
                    $info['part'] = max($info['part'], $part);
                    $info['size'] = $info['size'] + filesize($file);
                } else {
                    $info['part'] = $part;
                    $info['size'] = filesize($file);
                }

                $extension = strtoupper($fileInfo['extension']);
                $info['compress'] = ($extension === 'SQL') ? '-' : $extension;
                $info['date'] = date('Y-m-d H:i:s', strtotime("{$date} {$time}"));
                $info['time'] = strtotime("{$date} {$time}");
                $info['title'] = date('Ymd-His', strtotime("{$date} {$time}"));
                $list[$key] = $info;
            }
        }
        $list = collect($list)->sortByDesc('time')->values();
        $data = [
            'code' => 0,
            'msg' => '请求成功',
            'data' => $list,
        ];
        return response()->json($data);
    }

    public function download(Request $request)
    {
        $time = $id = $request->get('time');
        if ($time) {
            //备份数据库文件名
            $name = date('Ymd-His', $time) . '-*.sql*';
            $config = config('custom.DatabaseBackup');
            $path = $config['path'] . $name;
            $path = glob($path);
            if (empty($path)) {
                return response()->json(['status' => 'fail', 'message' => '下载文件不存在！']);
            }
            $file = $path[0];
            return response()->download($file);
        } else {
            return response()->json(['status' => 'fail', 'message' => '参数错误！']);
        }
    }

    public function optimize(Request $request)
    {
        $tables = $request->get('tables');
        if (is_array($tables)) {
            $tables = implode('`,`', $tables);
            $list = DB::statement("OPTIMIZE TABLE `{$tables}`");
            if ($list) {
                return response()->json(['code'=>0,'msg'=>'优化完成']);
            } else {
                return response()->json(['code'=>1,'msg'=>'优化出错请重试']);
            }
        } else {
            return response()->json(['code'=>1,'msg'=>'请指定要优化的表']);
        }
    }

    public function repair(Request $request)
    {
        //表名
        $tables = $request->get('tables');
        if (is_array($tables)) {
            $tables = implode('`,`', $tables);
            $list = DB::statement("REPAIR TABLE `{$tables}`");
            if ($list) {
                return response()->json(['code'=>0,'msg'=>'修复完成']);
            } else {
                return response()->json(['code'=>1,'msg'=>'修复出错请重试']);
            }
        } else {
            return response()->json(['code'=>1,'msg'=>'请指定要修复的表']);
        }
    }

    public function restore(Request $request)
    {
        //时间
        $time = $request->get('time', 0);
        $part = $request->get('part', null);
        //起始行数
        $start = $request->get('start', null);
        if (is_numeric($time) && is_null($part) && is_null($start)) {
            //获取备份文件信息
            $name = date('Ymd-His', $time) . '-*.sql*';
            $config = config('custom.DatabaseBackup');
            $path = $config['path'] . $name;
            $files = glob($path);
            $list = [];
            foreach ($files as $name) {
                $basename = basename($name);
                $match = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
                $gz = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
                $list[$match[6]] = array($match[6], $name, $gz);
            }
            ksort($list);
            //检测文件正确性
            $last = end($list);
            if (count($list) === $last[0]) {
                Cache::put('backup_list', $list, Carbon::now()->addMinutes(120));
                return response()->json(['code'=>1,'msg'=>'初始化完成', 'data'=>['part' => 1, 'start' => 0]]);
            } else {
                Cache::forget('backup_list');
                return response()->json(['code'=>-1, 'msg'=>'备份文件可能已经损坏，请检查']);
            }
        } elseif (is_numeric($part) && is_numeric($start)) {
            $list = Cache::get('backup_list');;
            $db = new Database($list[$part], array('path' => realpath(config('data_backup_path')) . DIRECTORY_SEPARATOR, 'compress' => $list[$part][2]));
            $start = $db->import($start);
            if (false === $start) {
                return response()->json(['code'=>-1, 'msg'=>'还原数据出错']);
            } elseif (0 === $start) {
                //下一卷
                if (isset($list[++$part])) {
                    $data = ['part' => $part, 'start' => 0];
                    return response()->json(['code'=>1, 'msg'=>'正在还原...#{$part}', 'data'=>$data]);
                } else {
                    Cache::forget('backup_list');
                    return response()->json(['code'=>1, 'msg'=>'还原完成']);
                }
            } else {
                $data = ['part' => $part, 'start' => $start[0]];
                if ($start[1]) {
                    $rate = floor(100 * ($start[0] / $start[1]));
                    return response()->json(['code'=>1, 'msg'=>"正在还原...#{$part} ({$rate}%)", 'data'=>$data]);
                } else {
                    $data['gz'] = 1;
                    return response()->json(['code'=>1, 'msg'=>"正在还原...#{$part}", 'data'=>$data]);
                }
            }
        } else {
            return response()->json(['code'=>-1, 'msg'=>'参数错误']);
        }
    }

    public function destroy(Request $request)
    {
        $time = $id = $request->get('time');
        if ($time) {
            $name = date('Ymd-His', $time) . '-*.sql*';
            $config = config('custom.DatabaseBackup');
            $path = $config['path'] . $name;
            array_map("unlink", glob($path));
            if (count(glob($path))) {
                return response()->json(['code'=>1,'msg'=>'备份文件删除失败，请检查权限']);
            } else {
                return response()->json(['code'=>0,'msg'=>'删除成功']);
            }
        } else {
            return response()->json(['code'=>1,'msg'=>'参数错误']);
        }
    }
}
