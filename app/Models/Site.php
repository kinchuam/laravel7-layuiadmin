<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Site extends Model
{
    protected $table = 'sites';
    protected $fillable = ['key', 'value'];
    //获取
    public function getPluginset($key = null)
    {
        if (empty($key)){
            return false;
        }
        $set = $this->getSetData($key);
        $allset = [];

        if (!empty($set['value'])){
            $allset = json_decode($set['value'],true);
        }

        return $allset;
    }
    //更新
    public function updatePluginset($key = null , $values)
    {
        if (empty($key)){
            return false;
        }
        $setdata = Site::where('key',$key)->first();
        if (empty($setdata)) {
            $res = Site::create(['key' => $key, 'value' => json_encode($values)]);
            $setdata = array('value' => $values);
        } else {
            $plugins = json_decode($setdata['value'],true);
            if(!is_array($plugins)){
                $plugins = array();
            }
            foreach ($values as $ke => $va) {
                if(!isset($plugins[$ke]) || !is_array($plugins[$ke])){
                    $plugins[$ke] = array();
                }
                $plugins[$ke] = $va;
            }

            $res = $setdata->update(['value' => json_encode($plugins)]);
            if ($res) {
                $setdata['value'] = $plugins;
            }
        }
        if (empty($res)) {
            $setdata = Site::where('key',$key)->first();
        }
        Cache::put($key, json_encode($setdata['value']), now()->addMinutes(120));
        return true;
    }

    public function getSetData($key = null)
    {
        if (empty($key)){
            return false;
        }
        $data = [];
        $set =  Cache::get($key);
        if (empty($set)) {
            $set = Site::select('key','value')->where('key',$key)->first();
            $set = empty($set)?[]:$set->toArray();
            if (!empty($set['value'])){
                Cache::put($key, $set['value'], now()->addMinutes(120));
            }
            $data = $set;
        } else if (!empty($set)){
            $data['key'] = $key;
            $data['value'] = $set;
        }
        return $data;
    }
}
