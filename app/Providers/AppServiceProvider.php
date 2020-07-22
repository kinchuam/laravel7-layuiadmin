<?php

namespace App\Providers;

use App\Models\User;
use App\Observer\UserObserver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
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

        User::observe(UserObserver::class);
        Schema::defaultStringLength(200);
        //左侧菜单
        view()->composer('admin.layout',function($view){
            $uuid = auth('admin')->user()?auth('admin')->user()->uuid:'none';
            $menus = Cache::remember('admin_menus:'.$uuid, Carbon::now()->addMinutes(env('APP_CONFIG_CACHE',120)), function () {
                return \App\Models\Permission::with(['childs'])->where('parent_id',0)->orderBy('sort','desc')->get(['id','name','display_name','route','icon','parent_id']);
            });
            $view->with('menus',$menus);

            $website = (new \App\Models\Site)->getPluginset('website');
            $view->with('website',$website);

            $unreadMessage = [];
            $view->with('unreadMessage',$unreadMessage);
        });
    }
}
