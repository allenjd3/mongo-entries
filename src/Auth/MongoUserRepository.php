<?php

namespace Allenjd3\Mongo\Auth;

use MongoDB\Laravel\Eloquent\Model;
use Statamic\Auth\Eloquent\RoleRepository;
use Statamic\Auth\Eloquent\UserGroupRepository;
use Statamic\Auth\UserCollection;
use Statamic\Auth\UserRepository;
use Statamic\Contracts\Auth\User as UserContract;
use App\Auth\MongoUser as User;
use Statamic\OAuth\Provider;

class MongoUserRepository extends UserRepository implements \Statamic\Contracts\Auth\UserRepository
{
    protected $config;
    protected $roleRepository = RoleRepository::class;
    protected $userGroupRepository = UserGroupRepository::class;

    public function model($method, ...$args)
    {
        $model = $this->config['model'];

        return call_user_func_array([$model, $method], $args);
    }

    public function query()
    {
        return new UserQueryBuilder($this->model('query'));
    }

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function all(): UserCollection
    {
        $users = $this->model('all')->keyBy('id')->map(function ($model) {
            return $this->make()->model($model);
        });

        return UserCollection::make($users);
    }

    public function find($id): ?UserContract
    {
        if ($model = $this->model('find', $id)) {
            return $this->make()->model($model);
        }

        return null;
    }

    public function findByEmail(string $email): ?UserContract
    {
        if (! $model = $this->model('where', 'email', $email)->first()) {
            return null;
        }

        return $this->make()->model($model);
    }

    public function save(UserContract $user)
    {
        $user->saveToDatabase();
    }

    public function delete(UserContract $user)
    {
        $user->model()->delete();
    }

    public function fromUser($user): ?UserContract
    {
        if (is_null($user)) {
            return null;
        }

        if ($user instanceof User) {
            return $user;
        }

        if (method_exists($user, 'toStatamicUser')) {
            return $user->toStatamicUser();
        }

        if ($user instanceof Model) {
            return User::make()->model($user);
        }

        return null;
    }

    public function make(): UserContract
    {
        return app(User::class)->model(new $this->config['model']);
    }

    public function findByOAuthId(string $provider, string $id): ?UserContract
    {
        return $this->find(
            (new Provider($provider))->getUserId($id)
        );
    }

    public function current(): ?UserContract
    {
        if (! $user = auth()->user()) {
            return null;
        }

        return $this->fromUser($user);
    }

    public static function bindings(): array
    {
        return [
            UserContract::class => User::class,
        ];
    }
}
