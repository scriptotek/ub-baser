<?php

namespace App\Bases\Sakbib;

use App\Record as BaseRecord;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends BaseRecord
{
    use SoftDeletes;

    public static $schema = CategorySchema::class;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sb_category';

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
     * The records this category has been used with.
     */
    public function publications()
    {
        return $this->belongsToMany(
            Publication::class,
            'sb_category_publication',
            'category_id',
            'publication_id'
        );
    }

    /**
     * The parent category this category belongs to.
     */
    public function parent()
    {
        return $this->belongsTo(
            Category::class,
            'parent_category_id'
        );
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
