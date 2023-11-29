<?php

namespace Allenjd3\Mongo\Auth;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class MongoUserProvider implements UserProvider
{
    protected $hasher;
    protected $model;

    public function __construct(HasherContract $hasher, $model)
    {
        $this->model = $model;
        $this->hasher = $hasher;
    }

    public function retrieveById($identifier)
    {
        return User::where('id', $identifier)->first();
    }

    public function retrieveByToken($identifier, $token)
    {
        $user =  User::where('id', $identifier)->first();
        if (!$user) {
            return null;
        }
        $rememberToken = $user->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token) ? $user : null;
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);

        $timestamps = $user->timestamps;

        $user->timestamps = false;

        $user->save();

        $user->timestamps = $timestamps;
    }

    public function retrieveByCredentials(array $credentials)
    {
        return User::where('email', $credentials['email'])->first();
    }

    public function validateCredentials(UserContract $user, array $credentials)
    {
        if (is_null($plain = $credentials['password'])) {
            return false;
        }

        return $this->hasher->check($plain, $user->getAuthPassword());
    }
}
