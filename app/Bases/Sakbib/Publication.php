<?php

namespace App\Bases\Sakbib;

use App\Record as BaseRecord;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publication extends BaseRecord
{
    use SoftDeletes;

    public static $schema = PublicationSchema::class;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sb_publication';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'keywords' => 'array',
    ];

    /**
     * The attributes that are mass assignable. This should include all the standard
     * attributes of this model, excluding auto-generated ones like 'id', 'created_at' etc.
     *
     * @var array
     */
    protected $fillable = [
    ];

    /**
     * Get a title for this record that can be used in the <title> element etc.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return sprintf('Post #%s', $this->id);
    }

    /**
     * The creators of this record.
     */
    public function creators()
    {
        return $this->belongsToMany(
            Creator::class,
            'sb_creator_publication',
            'publication_id',
            'creator_id'
        )
            ->using(PublicationCreator::class)
            ->withPivot('role', 'posisjon')
            ->orderBy('sb_creator_publication.posisjon', 'asc');
    }

    /**
     * The categories of this record.
     */
    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'sb_category_publication',
            'publication_id',
            'category_id'
        );
    }

    public function representation()
    {
        $repr = '<a href="' . action('\App\Bases\Sakbib\Controller@show', $this->id) . '">';

        // TODO: Format proper reference, not just title
        $repr .= $this->title;

        $repr .= '</a>';
        return $repr;
    }
}
