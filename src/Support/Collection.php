<?php

namespace Allenjd3\Mongo\Entries\Support;

use Statamic\Entries\Collection as StatamicCollection;
use Statamic\Facades;

class Collection extends StatamicCollection
{
    public function queryEntries()
    {
        return Facades\Entry::query()->where('statamic_collection', $this->handle());
    }
}
