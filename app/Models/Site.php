<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Site extends Model
{
    protected $table = 'sites';
    protected $fillable = ['key', 'value'];
    public $desc = '配置表';
    //获取设置
    public static function getPluginset($key = '')
    {
        if (empty($key)){ return false; }
        $set = Cache::remember($key, Carbon::now()->addMinutes(env('APP_CONFIG_CACHE',120)), function () use($key) {
            return Site::select('key', 'value')->where('key', $key)->first(['key','value']);
        });
        if (!empty($set['value'])){ return json_decode($set['value'],true); }
        return [];
    }

    //更新设置
    public static function updatePluginset($key = '', $values = [])
    {
        if (empty($key)){ return false; }
        $setdata = Site::where('key', $key)->first(['key','value']);
        if (empty($setdata)) {
            $a = ['key' => $key, 'value' => json_encode($values)];
            Site::create($a);
        } else {
            $plugins = json_decode($setdata['value'],true);
            if(!is_array($plugins)){ $plugins = []; }
            foreach ($values as $ke => $va) {
                if(!isset($plugins[$ke]) || !is_array($plugins[$ke])){
                    $plugins[$ke] = [];
                }
                $plugins[$ke] = $va;
            }
            $setdata->update(['value' => json_encode($plugins)]);
        }
        Cache::forget($key);
        return true;
    }

}
