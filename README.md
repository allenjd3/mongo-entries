# Statamic Mongo Entries

## Installation

```bash
composer require allenjd3/mongo-entries --with-all-dependencies
```

Set up you database connection in `config/database.php`.

```php
'connections' => [
    'mongodb' => [
        'driver' => 'mongodb',
        'dsn' => env('DB_DSN'),
        'database' => env('DB_DATABASE'),
    ],
],
```

Add the following to your `.env` file.

```
DB_CONNECTION=mongodb
DB_DSN=mongodb://
```

## Usage

Start by creating a new Statamic site. If you are planning to use mongodb to store users, you can skip creating a user.

```bash
statamic new my-site
```

If you'd like to use authentication, you will need to change some config values.

First register MongoAuthServiceProvider in `config/app.php`.

```php
'providers' => [
    // ...
    \Allenjd3\Mongo\Providers\MongoAuthServiceProvider::class,
],
```

Then change the `repository` config in `config/statamic/users.php`.

```php
'repository' => 'mongo',
'repositories' => [
    'mongo' => [
        'driver' => 'mongo',
    ],
],
```

Add the following to the `providers` array in `config/auth.php`.

```php
        'users' => [
            'driver' => 'mongo',
            'model' => App\Models\User::class,
        ],
```


Then in your App\Model\User class, you will need to extend the Allenjd3\Mongo\Auth\User class as authenticatable
```php
use Allenjd3\Mongo\Auth\User as Authenticatable;
```

and use the following trait-

```php
//other traits
use HasUuids;
```

Cast the following in your App\Model\User class-

```php
    protected $casts = [
        'preferences' => 'json',
        'super' => 'boolean',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'email_verified_at',
    ];
```

Create a new user

```bash
php please make:user
```
