<?php


namespace App\Library;


use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class CacheUser
{
    public static function user($id) {
        if(!$id || $id <= 0 || !is_numeric($id)){ return false; }
        return Cache::remember('cachedUser:'.$id, Carbon::now()->addMinutes(config('custom.config_cache_time')), function() use($id) {
            return User::query()->where('id', $id)->limit(1)->first();
        });
    }
}
