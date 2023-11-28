<?php

namespace Allenjd3\Mongo\Entries;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'published' => 'bool',
    ];

    protected $dates = [
        'date',
        'created_at',
        'updated_at'
    ];

    public function origin()
    {
        return $this->belongsTo(self::class);
    }

    public function data(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return json_decode($value, true);
            },
            set: function ($value) {
                $value['updated_at'] = Carbon::parse($value['updated_at']);
                return json_encode($value);
            },
        );
    }
}
