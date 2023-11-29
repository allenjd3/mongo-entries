<?php

namespace Allenjd3\Mongo\Auth;

use Illuminate\Support\Carbon;
use Statamic\Auth\Eloquent\User as EloquentUser;

class MongoUser extends EloquentUser
{
    public function lastLogin()
    {
        if (! $date = $this->model()->last_login) {
            return null;
        }

        return $date instanceof Carbon ? $date : Carbon::createFromTimestamp($date)->format($this->model()->getDateFormat());
    }
}
