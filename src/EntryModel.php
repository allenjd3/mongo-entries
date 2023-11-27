<?php

namespace Allenjd3\Mongo\Entries;


use MongoDB\Laravel\Eloquent\Model;

class EntryModel extends Model
{
    protected $guarded = [];
    protected $fillable = [
        'id',
        'origin_id',
        'site',
        'slug',
        'uri',
        'date',
        'statamic_collection',
        'data',
        'published',
        'status',
    ];
    protected $collection = 'entries';

    public $incrementing = false;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    protected $casts = [
        'date' => 'datetime',
        'data' => 'json',
        'published' => 'bool',
    ];

    public function origin()
    {
        return $this->belongsTo(self::class);
    }
}
