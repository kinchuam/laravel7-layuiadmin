<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $table = 'sites';
    protected $fillable = ['key', 'value'];

    /**
     * @param null $key
     * @return array|false|mixed
     */
    public static function getPluginSet($key = null)
    {
        if (empty($key)){ return false; }
        try {
            $set = cache()->remember($key, Carbon::now()->addMinutes(config('custom.config_cache_time')), function () use($key) {
                return self::where('key', $key)->first(['key','value']);
            });
            if (!empty($set['value'])){
                return json_decode($set['value'],true);
            }
        }catch (\Exception $e) {
            logger()->error('getPluginSet: '.$key.' error: '.$e->getMessage());
        }
        return [];
    }

    /**
     * @param null $key
     * @param array $values
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function updatePluginSet($key = null, $values = []): bool
    {
        if (empty($key)){ return false; }
        try {
            $model = self::updateOrCreate(["key" => $key], [ "value" => json_encode($values) ]);
            switch ($key) {
                case 'website':
                    $str = '站点配置';
                    break;
                case 'attachment.set':
                    $str = '附件设置';
                    break;
                default:
                    $str = '其他配置';
                    break;
            }
            if (cache()->has($key)) {
                cache()->forget($key);
            }
            return true;
        }catch (\Exception $e) {
            logger()->error('updatePluginSet: '.$key.' error: '.$e->getMessage());
        }
        return false;
    }

}
