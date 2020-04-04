<?php


namespace App\Helpers;


use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CacheUser
{

    public static function user($id){
        if(!$id||$id<=0||!is_numeric($id))
        {
            return false;
        } // if $id is not a reasonable integer, return false instead of checking users table

        return Cache::remember('cachedUser.'.$id, 30, function() use($id) {
            return User::find($id); // cache user instance for 30 minutes
        });
    }

}
