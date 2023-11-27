<?php

namespace Allenjd3\Mongo\Entries;

use Statamic\Contracts\Entries\QueryBuilder;
use Statamic\Entries\EntryCollection;
use Statamic\Stache\Query\QueriesTaxonomizedEntries;

class EntryQueryBuilder extends MongoQueryBuilder implements QueryBuilder
{
    use QueriesTaxonomizedEntries;

    protected $columns = [
        'id', 'site', 'origin_id', 'published', 'status', 'slug', 'uri',
        'date', 'statamic_collection', 'created_at', 'updated_at',
    ];

    protected function transform($items, $columns = [])
    {
        return EntryCollection::make($items)->map(function ($model) {
            return Entry::fromModel($model);
        });
    }

    protected function column($column)
    {
        if ($column == 'origin') {
            $column = 'origin_id';
        }

        if (! in_array($column, $this->columns)) {
            $column = 'data->'.$column;
        }

        return $column;
    }

    public function get($columns = ['*'])
    {
        $this->addTaxonomyWheres();

        return parent::get();
    }

    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $this->addTaxonomyWheres();

        return parent::paginate($perPage, $columns, $pageName, $page);
    }

    public function count()
    {
        $this->addTaxonomyWheres();

        return parent::count();
    }
}
