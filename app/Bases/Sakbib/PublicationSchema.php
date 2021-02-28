<?php

namespace App\Bases\Sakbib;

use App\Schema\Operators;
use App\Schema\Schema as BaseSchema;

class PublicationSchema extends BaseSchema
{
    protected $schema = [
        "id" => "sakbib",
        "fields" => [

            // ID
            [
                "key" => "id",
                "type" => "incrementing",
            ],

            // Publikasjonstype
            [
                'key' => 'pub_type',
                'type' => 'select',
                'values' => [
                    ['value' => 'Mastersthesis', 'prefLabel' => 'Masteroppgave'],
                    ['value' => 'Inbook', 'prefLabel' => 'Bokkapittel'],
                    ['value' => 'Phdthesis', 'prefLabel' => 'PhD-avhandling'],
                    ['value' => 'Book', 'prefLabel' => 'Bok'],
                    ['value' => 'Misc', 'prefLabel' => 'Annet'],
                ],
            ],

            // Tittel
            [
                'key' => 'title',
                'type' => 'autocomplete',
            ],

            // Forfattere
            [
                'key' => 'creators',
                'type' => 'entities',
                'entityType' => Creator::class,
                'entitySchema' => CreatorSchema::class,
                'modelAttribute' => 'creators',
                'pivotTable' => 'sb_creator_publication',
                'pivotTableKey' => 'creator_id',
                'pivotFields' => [
                    [
                        'key' => 'role',
                        'type' => 'select',
                        'multiple' => true,
                        'defaultValue' => ['aut'],
                        'values' => [
                            ['value' => 'forfatter', 'prefLabel' => 'forfatter'],
                            ['value' => 'redaktør', 'prefLabel' => 'redaktør'],
                        ],
                        'edit' => [
                            'allow_new_values' => false,
                        ],
                    ],
                    [
                        'key' => 'posisjon',
                        'type' => 'simple',
                        'edit' => false,
                    ],
                ],
            ],

            // Utgivelsesår
            [
                'key' => 'year',
                'type' => 'simple',
            ],

            // Utgave
            [
                'key' => 'edition',
                'type' => 'simple',
            ],

            // Utgiver
            [
                'key' => 'publisher',
                'type' => 'autocomplete',
            ],

            // Utgivelsessted
            [
                'key' => 'publication_place',
                'type' => 'autocomplete',
            ],

            // ISBN
            [
                'key' => 'isbn',
                'type' => 'simple',
            ],

            // Serie
            [
                'key' => 'series',
                'type' => 'simple',
            ],

            // Bok-tittel (hvis referansen er til et kapittel / del av bok)
            [
                'key' => 'booktitle',
                'type' => 'simple',
            ],

            // Kapittel
            [
                'key' => 'chapter',
                'type' => 'simple',
            ],

            // Tidsskrift
            [
                'key' => 'journal',
                'type' => 'simple',
            ],

            // Tidsskrift - ISSN
            [
                'key' => 'issn',
                'type' => 'simple',
            ],

            // Bind
            [
                'key' => 'volume',
                'type' => 'simple',
            ],

            // Hefte-nummer
            [
                'key' => 'number',
                'type' => 'simple',
            ],

            // Sider
            [
                'key' => 'pages',
                'type' => 'simple',
            ],

            // Institusjon der avhandlingen/oppgaven ble forsvart for en akademisk grad
            [
                'key' => 'school',
                'type' => 'simple',
            ],

            // Publikasjonsnote?
            [
                'key' => 'note',
                'type' => 'simple',
            ],

            // Abstract
            [
                'key' => 'abstract',
                'type' => 'simple',
            ],

            // URL
            [
                'key' => 'url',
                'type' => 'url',
            ],

            // DOI
            [
                'key' => 'doi',
                'type' => 'simple',
            ],

            // Crossref
            [
                'key' => 'crossref',
                'type' => 'simple',
            ],

            // Offentlig note
            [
                'key' => 'public_note',
                'type' => 'simple',
                'multiline' => true,
            ],

            // Intern note
            [
                'key' => 'private_note',
                'type' => 'simple',
                'multiline' => true,
            ],

            // Nøkkelord
            [
                'key' => 'keywords',
                'type' => 'select',
                'multiple' => true,
                'defaultValue' => [],

                'search' => [
                    'type' => 'array',
                    'widget' => 'autocomplete',
                    // 'placeholder' => '',
                ],
            ],

            // Kategorier
            [
                'key' => 'categories',
                'type' => 'entities',
                'entityType' => Category::class,
                'entitySchema' => CategorySchema::class,
                'modelAttribute' => 'categories',
                'pivotTable' => 'sb_category_publication',
                'pivotTableKey' => 'category_id',
            ],

            // Kategori-søk
            [
                'key' => 'category',
                'type' => 'search_only',

                'search' => [
                    'placeholder' => 'Fornavn og/eller etternavn',
                    'type' => 'ts',
                    'ts_index' => 'category_ts',
                    'operators' => [Operators::CONTAINS, Operators::NOT_CONTAINS],
                ],
            ],

        ],
    ];
}
