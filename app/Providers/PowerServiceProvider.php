<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use App\Observer\ModelObserver;
use Illuminate\Support\ServiceProvider;

class PowerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerEvents();
    }

    /**
     * 执行配置文件
     */
    public function registerEvents()
    {
        $allListeners = config('power.event.listeners');
        if (is_array($allListeners)) {
            foreach ($allListeners as $event => $listeners) {
                foreach ($listeners as $listener) {
                    Event::listen($event, $listener);
                }
            }
        }

        $subscribers = config('power.event.subscribers');
        if (is_array($subscribers)) {
            foreach ($subscribers as $subscriber) {
                Event::subscribe($subscriber);
            }
        }


        $observers = config('power.event.observers');
        if (is_array($observers)) {
            foreach ($observers as $observer) {
                $observer::observe(ModelObserver::class);
            }
        }
    }

}
