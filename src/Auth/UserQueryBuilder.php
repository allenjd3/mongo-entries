<?php

namespace Allenjd3\Mongo\Auth;

use Allenjd3\Mongo\Entries\MongoQueryBuilder;
use Statamic\Auth\UserCollection;
use Statamic\Facades\User;

class UserQueryBuilder extends MongoQueryBuilder
{
    protected function transform($items, $columns = ['*'])
    {
        return UserCollection::make($items)->map(function ($model) {
            return User::make()->model($model);
        });
    }

    protected function column($column)
    {
        if ($column === 'id') {
            return User::make()->model()->getKeyName();
        }

        return $column;
    }
}
