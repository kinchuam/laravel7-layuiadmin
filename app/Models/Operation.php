<?php


namespace App\Models;


class Operation extends \Spatie\Activitylog\Models\Activity
{

    public function user()
    {
        return $this->belongsTo('App\User','causer_id');
    }
}
