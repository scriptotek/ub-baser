<?php

namespace App\Letras;

use App\Schema\Schema;

class LetrasSchema extends Schema
{
    public $prefix = 'letras';

    protected $schema = [
        'fields' => [

            // ID
            [
                'key' => 'id',
                'type' => 'incrementing',
            ],
        ],

        'groups' => [

            [
                'label' => 'Verket',
                'fields' => [

                    // Forfatter
                    [
                        'key' => 'forfatter',
                        'type' => 'autocomplete',
                    ],

                    // Land
                    [
                        'key' => 'land',
                        'type' => 'autocomplete',
                    ],

                    // Tittel
                    [
                        'key' => 'tittel',
                        'type' => 'simple',
                    ],

                    // Utgivelsesår
                    [
                        'key' => 'utgivelsesaar',
                        'type' => 'simple',
                    ],

                    // Sjanger
                    [
                        'key' => 'sjanger',
                        'type' => 'autocomplete',
                    ],
                ],
            ],

            [
                'label' => 'Oversettelsen',
                'fields' => [

                    // Oversetter
                    [
                        'key' => 'oversetter',
                        'type' => 'simple',
                    ],

                    // Tittel
                    [
                        'key' => 'tittel2',
                        'type' => 'simple',
                    ],

                    // Utgivelsessted
                    [
                        'key' => 'utgivelsessted',
                        'type' => 'simple',
                    ],


                    // Utgivelsesår
                    [
                        'key' => 'utgivelsesaar2',
                        'type' => 'simple',
                    ],

                    // Forlag
                    [
                        'key' => 'forlag',
                        'type' => 'simple',
                    ],

                    // Forord/etterord
                    [
                        'key' => 'foretterord',
                        'type' => 'simple',

                        'searchable' => 'advanced',
                    ],

                    // Språk
                    [
                        'key' => 'spraak',
                        'type' => 'simple',
                    ],
                ],
            ],
        ],
    ];

    public function __construct()
    {
        $this->schemaOptions['autocompleteUrl'] = action('LetrasController@autocomplete');

        parent::__construct();
    }
}
