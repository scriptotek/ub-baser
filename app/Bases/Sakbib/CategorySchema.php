<?php

namespace App\Bases\Sakbib;

use App\Schema\EntitiesField;
use App\Schema\Schema as BaseSchema;

class CategorySchema extends BaseSchema
{
    protected $schema = [
        'id' => 'sakbib.category',
        'fields' => [
            [
                'key' => 'name',
                'type' => 'simple',
            ],
            [
                'key' => 'description',
                'type' => 'simple',
            ],
            [
                'key' => 'parent_category_id',
                'type' => 'entity',
                'entityType' => Category::class,
                'entitySchema' => CategorySchema::class,
                'modelAttribute' => 'parent',
            ],
        ],
    ];
}
