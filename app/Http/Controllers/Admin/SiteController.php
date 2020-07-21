<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Decomposer;
use App\Models\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        $config = Site::getPluginset($sitekey);
        return view('admin.site.index',compact('config','sitekey'));
    }

    public function attachment()
    {
        $sitekey = 'attachment.set';
        $config = Site::getPluginset($sitekey);

        $config['file_type'] = !empty($config['file_type'])?explode('|',$config['file_type']):["mp3","txt"];
        $config['image_type'] = !empty($config['image_type'])?explode('|',$config['image_type']):["png", "jpg", "gif","jpeg","bmp"];

        $config['file_size'] = empty($config['file_size'])?2*1024:$config['file_size'];
        $config['image_size'] = empty($config['image_size'])?2*1024:$config['image_size'];
        return view('admin.site.attachment',compact('sitekey','config'));
    }

    public function optimize()
    {
        $composerArray = Decomposer::getComposerArray();
        $packages = Decomposer::getPackagesAndDependencies($composerArray['require']);
        $laravelEnv = Decomposer::getLaravelEnv();
        $serverEnv = Decomposer::getServerEnv();
        $serverExtras = Decomposer::getServerExtras();
        $laravelExtras = Decomposer::getLaravelExtras();
        $extraStats = Decomposer::getExtraStats();

        return view('admin.site.optimize', compact('packages', 'laravelEnv', 'serverEnv', 'extraStats', 'serverExtras', 'laravelExtras'));
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
            if (extension_loaded('Zend OPcache')) {
                opcache_reset();
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
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $data = $request->except(['_token','_method']);
        if (empty($data)) {
            if ($request->ajax()) {
                return response()->json(['status' => 'fail', 'message' => '无数据更新']);
            }
            return back()->withErrors(['status'=>'无数据更新']);
        }

        $key = $data['sitekey'];
        unset($data['sitekey']);
        $rels = Site::updatePluginset($key,$data);
        if ($rels) {
            if ($request->ajax()) {
                return response()->json(['status' => 'success', 'message' => '更新成功']);
            }
            return back()->with(['status'=>'更新成功']);
        }
        if ($request->ajax()) {
            return response()->json(['status' => 'fail', 'message' => '系统错误']);
        }
        return back()->withErrors('系统错误');

    }

}
