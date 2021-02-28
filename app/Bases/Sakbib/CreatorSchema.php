<?php

namespace App\Bases\Sakbib;

use App\Schema\Schema as BaseSchema;

class CreatorSchema extends BaseSchema
{
    protected $schema = [
        'id' => 'sakbib.creator',
        'fields' => [
            [
                'key' => 'name',
                'type' => 'simple',
                'edit' => [
                    'placeholder' => '',
                    'help' => 'Invertert form («Etternavn, fornavn») for personer.',
                ],
            ],
            [
                'key' => 'entity_type',
                'type' => 'select',
                'values' => [
                    ['value' => 'person', 'prefLabel' => 'Person'],
                    ['value' => 'institution', 'prefLabel' => 'Institusjon'],
                ],
            ],
            [
                'key' => 'noraf_id',
                'type' => 'simple',
                'edit' => [
                    'help' => 'Identifikator i Felles autoritetsregister.',
                    'placeholder' => '(For fremtidig bruk)',
                ],
            ],
        ],
    ];
}
