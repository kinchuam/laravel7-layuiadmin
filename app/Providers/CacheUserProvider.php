<?php


namespace App\Providers;


use App\Library\CacheUser;
use App\Models\User;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Auth\EloquentUserProvider;

class CacheUserProvider extends EloquentUserProvider
{
    /**
     * CacheUserProvider constructor.
     * @param HasherContract $hasher
     */
    public function __construct(HasherContract $hasher)
    {
        parent::__construct($hasher, User::class);
    }
    /**
     * @param mixed $identifier
     * @return false
     */
    public function retrieveById($identifier)
    {
        return CacheUser::user($identifier);
    }

    public function retrieveByToken($identifier, $token)
    {
        $model = CacheUser::user($identifier);
        if (! $model) { return null; }
        $rememberToken = $model->getRememberToken();
        return $rememberToken && hash_equals($rememberToken, $token) ? $model : null;
    }
}
