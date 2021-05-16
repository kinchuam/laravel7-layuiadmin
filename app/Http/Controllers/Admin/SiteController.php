<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;
use App\Models\Attachment;
use App\Models\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $siteKey = 'website';
        $config = Site::getPluginSet($siteKey);
        return view('admin.site.index', compact('config','siteKey'));
    }

    public function attachment()
    {
        $siteKey = 'attachment.set';
        $config = Site::getPluginSet($siteKey);
        $config['image_size'] = $config['image_size'] ?? Attachment::image_size;
        $config['image_type'] = isset($config['image_type']) ? explode('|', $config['image_type']) : Attachment::image_type;
        $config['file_size'] = $config['file_size'] ?? Attachment::file_size;
        $config['file_type'] = isset($config['file_type']) ? explode('|', $config['file_type']) : Attachment::file_type;
        return view('admin.site.attachment', compact('siteKey','config'));
    }

    public function optimize()
    {
        $json = file_get_contents(base_path('composer.json'));
        $dependencies = json_decode($json, true)['require'];
        $envs = [
            ['name' => 'PHP version', 'type'=>'php',  'value' => 'PHP/'.PHP_VERSION],
            ['name' => 'Laravel version',   'value' => app()->version()],
            ['name' => 'CGI',               'value' => php_sapi_name()],
            ['name' => 'Uname',             'value' => php_uname()],
            ['name' => 'Server',            'value' => $_SERVER['SERVER_SOFTWARE']],

            ['name' => 'Cache driver',      'value' => config('cache.default')],
            ['name' => 'Session driver',    'value' => config('session.driver')],
            ['name' => 'Queue driver',      'value' => config('queue.default')],

            ['name' => 'Timezone',          'value' => config('app.timezone')],
            ['name' => 'Locale',            'value' => config('app.locale')],
            ['name' => 'Env',               'value' => config('app.env')],
            ['name' => 'URL',               'value' => config('app.url')],
        ];
        $extras = [];
        if (strtolower(config('cache.default')) == 'redis') {
            if ($Memory = app('redis')->info()) {
                $extras['redis']['extra'] = '消耗峰值：' . round($Memory['used_memory_peak'] / 1048576, 2) . ' M/ 内存总量：' . round($Memory['used_memory'] / 1048576, 2) . ' M';
            }
        }
        if (function_exists('memory_get_usage')) {
            $extras['php']['extra'] = '内存量：' . round(memory_get_usage()/1024/1024, 2).' M';
        }
        return view('admin.site.optimize', compact('dependencies','envs','extras'));
    }

    public function dateCache()
    {
        return view('admin.site.dateCache');
    }

    public function clearCache(Request $request): \Illuminate\Http\JsonResponse
    {
        $type = $request->input('type');
        if (!empty($type)) {
            $default = config('cache.default');$redis = null;
            if (strtolower($default) == 'redis') {
                $redis = app('redis');
            }
            $str = [];
            if (isset($type['cache'])) {
                !empty($redis) ? $redis->connection('cache')->flushdb() : Artisan::call('cache:clear');
                array_push($str, '数据缓存');
            }
            if (isset($type['picture'])) {
                array_push($str, '图片缓存');
            }
            if (isset($type['view'])) {
                Artisan::call('view:clear');
                array_push($str, '视图缓存');
            }
            if (isset($type['route'])) {
                Artisan::call('route:clear');
                Artisan::call('route:cache');
                array_push($str, '路由缓存');
            }
            if (isset($type['config'])) {
                Artisan::call('config:clear');
                Artisan::call('config:cache');
                array_push($str, '配置缓存');
            }
            ActivityLog::addLog('清除'. implode('、', $str), $type);
            return response()->json(['code'=>0, 'message'=>'操作成功']);
        }
        return response()->json(['code'=>-2, 'message'=>'系统错误']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function update(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->except(['_token','_method']);
        if (empty($data)) {
            return response()->json(['status' => 'fail', 'message' => '无数据更新']);
        }

        $key = $data['siteKey'];
        unset($data['siteKey']);
        $reals = Site::updatePluginSet($key, $data);
        if ($reals) {
            return response()->json(['code' => 0, 'message' => '更新成功']);
        }
        return response()->json(['code' => -2, 'message' => '系统错误']);
    }

}
