<?php

namespace App\Bases\Sakbib;

use App\Record as BaseRecord;
use Illuminate\Database\Eloquent\SoftDeletes;

class Creator extends BaseRecord
{
    use SoftDeletes;

    public static $schema = CreatorSchema::class;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sb_creator';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['string_representation'];

    /**
     * The records this person belongs to.
     */
    public function publications()
    {
        return $this->belongsToMany(
            'App\Bases\Sakbib\Publication',
            'sb_creator_publication',
            'creator_id',
            'publication_id'
        )
            ->using(PublicationCreator::class)
            ->withPivot('role', 'posisjon');
    }

    public function getStringRepresentationAttribute()
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Get a title for this record that can be used in the <title> element etc.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->name;
    }
}
