<?php

namespace App\Http\Controllers\Admin;

use App\Models\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $sitekey = 'website';
        $config = (new Site)->getPluginset($sitekey);
        return view('admin.site.index',compact('config','sitekey'));
    }

    public function attachment()
    {
        $sitekey = 'attachment.set';
        $config = (new Site)->getPluginset($sitekey);

        $config['file_type'] = !empty($config['file_type'])?explode('|',$config['file_type']):["mp3","txt"];
        $config['image_type'] = !empty($config['image_type'])?explode('|',$config['image_type']):["png", "jpg", "gif","jpeg","bmp"];

        $config['file_size'] = empty($config['file_size'])?2*1024:$config['file_size'];
        $config['image_size'] = empty($config['image_size'])?2*1024:$config['image_size'];
        return view('admin.site.attachment',compact('sitekey','config'));
    }

    public function attachmentupdate(Request $request)
    {
        $data = $request->except(['_token','_method']);
        if (empty($data)){
            return response()->json(['status' => 'fail', 'message' => '无数据更新']);
        }

        $key = $data['sitekey'];
        unset($data['sitekey']);
        $rels = (new Site)->updatePluginset($key,$data);
        if ($rels){
            return response()->json(['status' => 'success', 'message' => '更新成功']);
        }
        return response()->json(['status' => 'fail', 'message' => '系统错误']);
    }

    public function optimize()
    {
        $json = file_get_contents(base_path('composer.json'));
        $res = json_decode($json, true);
        $dependencies = $res['require'];
        $dependencie_desvs = $res['require-dev'];

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
        if (config('cache.default')=='redis'){
            $status = Redis::info();
            if (!empty($status)) {
                $Memory = $status;
                $extras['redis']['extra'] = '消耗峰值：' . round($Memory['used_memory_peak'] / 1048576, 2) . ' M/ 内存总量：' . round($Memory['used_memory'] / 1048576, 2) . ' M';
            }
        }
        if (function_exists('memory_get_usage')){
            $extras['php']['extra'] = '内存量：' . round(memory_get_usage()/1024/1024, 2).' M';
        }

        return view('admin.site.optimize',compact('dependencies','envs','extras','dependencie_desvs'));
    }

    public function datecache()
    {
        return view('admin.site.datecache');
    }

    public function clearcache(Request $request)
    {
        $type = $request->post('type');
        if (!empty($type)) {

            if (isset($type['cache'])){
                Artisan::call('cache:clear');
            }
            if (isset($type['view'])){
                Artisan::call('view:clear');
            }
            if (isset($type['config'])){
                Artisan::call('config:clear');
            }
            return back()->with(['status'=>'更新缓存成功']);
        }
        return back()->with(['status'=>'请选择需要清除选项']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $data = $request->except(['_token','_method']);
        if (empty($data)){
            return back()->withErrors(['status'=>'无数据更新']);
        }

        $key = $data['sitekey'];
        unset($data['sitekey']);
        $rels = (new Site)->updatePluginset($key,$data);
        if ($rels){
            return back()->with(['status'=>'更新成功']);
        }
        return back()->withErrors('系统错误');

    }

}
