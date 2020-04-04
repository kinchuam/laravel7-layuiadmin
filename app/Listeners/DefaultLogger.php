<?php

namespace App\Listeners;

use App\Events\DefaultLoggable;

class DefaultLogger
{
    public function handle(DefaultLoggable $event)
    {
        $topic = $event->model;
        $causedUser = $event->user;
        if (request()->route())
        {
            $arr = request()->route()->gatherMiddleware();
            if (is_array($arr)) {
                $a = end($arr);
                $b = explode(':',$a);
                if (is_array($b) && isset($b[0]) && $b[0] == 'permission') {
                    $logname = $b[1];
                    activity()->inLog($logname)->performedOn($topic)->causedBy($causedUser)->withProperties($event->content)->log($event->title);
                }
            }
        }

    }

}
