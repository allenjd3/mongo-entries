<?php

namespace Allenjd3\Mongo\Entries;

use Allenjd3\Mongo\Entries\EntryModel as Model;
use Carbon\Carbon;
use Statamic\Entries\Collection;
use Statamic\Entries\Entry as FileEntry;
use Statamic\Facades\Blink;
use Statamic\Support\Str;

class Entry extends FileEntry
{
    protected $model;

    public static function fromModel(Model $model)
    {
        return (new static)
            ->locale($model->site)
            ->slug($model->slug)
            ->collection($model->statamic_collection)
            ->date($model->date)
            ->data($model->data)
            ->published($model->published)
            ->model($model);
    }

    public function toModel()
    {
        return Model::findOrNew($this->id())->fill([
            'id' => $this->id() ?? (string) Str::uuid(),
            'origin_id' => $this->originId(),
            'site' => $this->locale(),
            'slug' => $this->slug(),
            'uri' => $this->uri(),
            'statamic_collection' => $this->collectionHandle(),
            'data' => $this->data(),
            'published' => $this->published(),
            'status' => $this->status(),
            'date' => $this->date(),
        ]);
    }

    public function model($model = null): static
    {
        if (func_num_args() === 0) {
            return $this->model;
        }

        $this->model = $model;

        $this->id($model->id);

        return $this;
    }

    public function lastModified()
    {
        return $this->model->updated_at;
    }

    public function origin($origin = null)
    {
        if (func_num_args() > 0) {
            $this->origin = $origin;

            return $this;
        }

        if ($this->origin) {
            return $this->origin;
        }

        if (! $this->model->origin) {
            return null;
        }

        return self::fromModel($this->model->origin);
    }

    public function originId()
    {
        return $this->origin?->id() ?? $this->model?->origin_id;
    }

    public function hasOrigin(): bool
    {
        return $this->originId() !== null;
    }

    public function date($date = null)
    {
        return $this
            ->fluentlyGetOrSet('date')
            ->getter(function ($date) {
                if (! $this->collection()?->dated()) {
                    return null;
                }

                $date = $date ?? $this->lastModified();

                if (! $this->hasTime()) {
                    $date->startOfDay();
                }

                if (! $this->hasSeconds()) {
                    $date->startOfMinute();
                }

                return $date;
            })
            ->setter(function ($date) {
                if ($date === null) {
                    return null;
                }

                if ($date instanceof \Carbon\Carbon) {
                    return $date;
                }

                if (strlen($date) === 10) {
                    return Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
                }

                if (strlen($date) === 15) {
                    return Carbon::createFromFormat('Y-m-d-Hi', $date)->startOfMinute();
                }

                return Carbon::createFromFormat('Y-m-d-His', $date);
            })
            ->args(func_get_args());
    }

    public function collection($collection = null)
    {
        return $this
            ->fluentlyGetOrSet('collection')
            ->setter(function ($collection) {
                return $collection instanceof Collection ? $collection->handle() : $collection;
            })
            ->getter(function ($collection) {
                return $collection ? Blink::once("collection-{$collection}", function () use ($collection) {
                    return app(CollectionRepository::class)->findByHandle($collection);
                }) : null;
            })
            ->args(func_get_args());
    }
}
