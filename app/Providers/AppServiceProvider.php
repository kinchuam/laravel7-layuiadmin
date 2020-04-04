<?php

namespace App\Providers;

use App\Models\User;
use App\Observer\UserObserver;
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
            $menus = \App\Models\Permission::with(['childs'])->where('parent_id',0)->orderBy('sort','desc')->get();
            $view->with('menus',$menus);
            $unreadMessage = [];
            $view->with('unreadMessage',$unreadMessage);
        });
    }
}
