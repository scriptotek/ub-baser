<?php

namespace App\Bases\Sakbib;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PublicationCreator extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sb_creator_publication';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'role' => 'array',
    ];
}
