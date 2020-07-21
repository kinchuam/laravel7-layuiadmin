<?php


namespace App\Models;


use DateTimeInterface;

class Activitylog extends \Spatie\Activitylog\Models\Activity
{

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User','causer_id');
    }
}
