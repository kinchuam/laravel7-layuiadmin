<?php


namespace App\Observer;


use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserObserver
{

    public function saved(User $user) // whenever there's update or create of user, renew cached instance
    {
        Cache::forget("cachedUser.".$user->id);
    }

}
