<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(180);
        //左侧菜单
        view()->composer('admin.layout',function($view){
            $user = [];$uuid = 'guest';
            if (auth('admin')->check()) {
                $user = auth('admin')->user();
                $uuid = $user['uuid'];
            }
            $website = Site::getPluginSet('website');
            $menus = cache()->remember('adminMenus:'.$uuid, Carbon::now()->addMinutes(config('custom.config_cache_time')), function () {
                return Permission::query()->where('parent_id',0)->with(['childs' => function($q) {
                        $q->select(['name','display_name','route','icon','parent_id']);
                    }])->orderBy('sort','desc')->get(['id','name','display_name','route','icon'])->toArray();
            });
            $view->with('user', $user);
            $view->with('menus', $menus);
            $view->with('website', $website);
        });
    }
}
