<?php

namespace App\Listeners;

use App\Events\DefaultLoggable;

class DefaultLogger
{
    public function handle(DefaultLoggable $event)
    {
        $topic = $event->model;
        $causedUser = $event->user;

        if (request()->route()) {
            $position = request()->route()->getName();
            if (!empty($position))
            {
                activity()->inLog($position)->performedOn($topic)->causedBy($causedUser)->withProperties($event->content)->log($event->title);
            }
        }
    }

}
