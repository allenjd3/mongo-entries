<?php

namespace Allenjd3\Mongo\Providers;

use Allenjd3\Mongo\Auth\MongoUserProvider;
use Allenjd3\Mongo\Entries\CollectionRepository;
use Allenjd3\Mongo\Entries\EntryModel;
use Allenjd3\Mongo\Entries\EntryQueryBuilder;
use Allenjd3\Mongo\Entries\EntryRepository;
use Allenjd3\Mongo\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Statamic\Contracts\Entries\CollectionRepository as CollectionRepositoryContract;
use Statamic\Contracts\Entries\EntryRepository as EntryRepositoryContract;
use Statamic\Entries\Collection as StatamicCollection;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;

class ServiceProvider extends AddonServiceProvider
{
    public function register(): void
    {
        Statamic::repository(EntryRepositoryContract::class, EntryRepository::class);
        Statamic::repository(CollectionRepositoryContract::class, CollectionRepository::class);

        $this->app->bind(StatamicCollection::class, function () {
            return new Collection();
        });

        $this->app->bind(EntryQueryBuilder::class, function () {
            return new EntryQueryBuilder(EntryModel::query());
        });
    }

    public function boot()
    {
//        $this->publishes([
//            __DIR__ . './MongoAuthServiceProvider.php',
//            base_path('app/Providers/MongoAuthServiceProvider.php'),
//        ]);
    }
}
