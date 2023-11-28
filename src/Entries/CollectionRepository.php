<?php

namespace Allenjd3\Mongo\Entries;

use Statamic\Stache\Repositories\CollectionRepository as StacheRepository;

class CollectionRepository extends StacheRepository
{
    public function updateEntryUris($collection, $ids = null): void
    {
        $collection
            ->queryEntries()
            ->get()->each(function ($entry) {
                EntryModel::where('id', $entry->id())->update(['uri' => $entry->uri()]);
            });
    }
}
