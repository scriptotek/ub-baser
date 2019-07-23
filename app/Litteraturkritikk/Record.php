<?php

namespace App\Litteraturkritikk;

use Illuminate\Database\Eloquent\SoftDeletes;

class Record extends \App\Record
{
    use SoftDeletes;

    public static function getColumns(): array {
        $grouped = [
            [
                'label' => 'Meta',
                'display' => false,
                'fields' => [
                    ['key' => 'id', 'label' => 'ID', 'display' => false, 'type' => 'incrementing'],
                ],
            ],
            [
                'label' => 'Verket',
                'fields' => [
                    ['key' => 'verk_tittel'],
                    ['key' => 'verk_aar'],
                    ['key' => 'verk_sjanger'],
                    ['key' => 'verk_spraak', 'type' => 'autocomplete'],
                    ['key' => 'verk_kommentar'],
                    ['key' => 'verk_utgivelsessted', 'type' => 'autocomplete'],

                    [
                        'key' => 'verk_forfatter',
                        'type' => 'persons',
                        'model_attribute' => 'forfattere',
                        'person_role' => 'forfatter',
                    ],
                    [
                        'key' => 'verk_forfatter_mfl',
                        'help' => 'Kryss av hvis det er flere personer som ikke er listet opp',
                        'display' => false,
                        'type' => 'boolean',
                        'default' => false,
                        ],
                ],
            ],
            [
                'label' => 'Kritikken',
                'fields' => [
                    ['key' => 'kritikktype', 'type' => 'tags', 'default' => []],
                    ['key' => 'spraak', 'type' => 'autocomplete'],
                    ['key' => 'tittel'],
                    ['key' => 'publikasjon', 'type' => 'autocomplete'],
                    ['key' => 'utgivelsessted', 'type' => 'autocomplete'],
                    ['key' => 'aar'],
                    // ['key' => 'aar_numeric'],
                    ['key' => 'dato'],
                    ['key' => 'aargang'],
                    ['key' => 'bind'],
                    ['key' => 'hefte'],
                    ['key' => 'nummer'],
                    ['key' => 'sidetall'],
                    ['key' => 'kommentar'],
                    ['key' => 'utgivelseskommentar'],
                    ['key' => 'fulltekst_url', 'type' => 'url'],

                    [
                        'key' => 'kritiker',
                        'type' => 'persons',
                        'model_attribute' => 'kritikere',
                        'person_role' => 'kritiker',
                    ],
                    [
                        'key' => 'kritiker_mfl',
                        'help' => 'Kryss av hvis det er flere personer som ikke er listet opp',
                        'display' => false,
                        'type' => 'boolean',
                        'default' => false
                    ],
                ],
            ],
        ];

        foreach ($grouped as &$group) {
            foreach ($group['fields'] as &$field) {
                $field['label'] = trans('litteraturkritikk.' . $field['key']);
                if (!isset($field['type'])) {
                    $field['type'] = 'simple';
                }
            }
        }

        return $grouped;
    }

    public static function getSearchFields(): array
    {
        $minYear = 1789;
        $maxYear = (int) strftime('%Y');

        return [
            'fields' => [
                [
                    'key' => 'q',
                    'type' => 'simple',
                    'label' => 'Alle felt',
                    'placeholder' => 'Forfatter, kritiker, ord i tittel, kommentar, etc...',
                    'options' => [],
                    'index' => [
                        'type' => 'ts',
                        'ts_column' => 'any_field_ts',
                    ],
                    'operators' => [
                        'eq',
                        'neq',
                    ],
                ],
                [
                    'key' => 'person',
                    'type' => 'autocomplete',
                    'label' => 'Forfatter eller kritiker',
                    'placeholder' => 'Fornavn og/eller etternavn',
                    'options' => [],
                    'index' => [
                        'type' => 'ts',
                        'ts_column' => 'person_ts',
                    ],
                    'operators' => [
                        'eq',
                        'neq'
                    ],
                ],
            ],
            'groups' => [
                [
                    'label' => 'Verket',
                    'fields' => [
                        [
                            'key' => 'forfatter',
                            'type' => 'autocomplete',
                            'label' => 'Forfatter',
                            'placeholder' => 'Fornavn og/eller etternavn',
                            'options' => [],
                            'index' => [
                                'type' => 'ts',
                                'column' => 'verk_forfatter',
                                'ts_column' => 'forfatter_ts',
                            ],
                            'operators' => [
                                'eq',
                                'neq',
                                'isnull',
                                'notnull',
                            ],
                        ],
                        [
                            'key' => 'verk_tittel',
                            'type' => 'autocomplete',
                            'label' => 'Verk',
                            'placeholder' => 'Tittel på omtalt verk',
                            'options' => [],
                            'index' => [
                                'type' => 'ts',
                                'column' => 'verk_tittel',
                                'ts_column' => 'verk_tittel_ts',
                            ],
                            'operators' => [
                                'eq',
                                'neq',
                                'isnull',
                                'notnull',
                            ],
                        ],
                        [
                            'key' => 'verk_sjanger',
                            'type' => 'autocomplete',
                            'label' => 'Sjanger',
                            'placeholder' => 'Sjanger til det omtalte verket. F.eks. lyrikk, roman, ...',
                            'options' => [],
                            'index' => [
                                'type' => 'simple',
                                'column' => 'verk_sjanger',
                            ],
                            'operators' => [
                                'eq',
                                'neq',
                                'isnull',
                                'notnull',
                            ],
                        ],
                        [
                            'key' => 'verk_aar',
                            'type' => 'rangeslkeyer',
                            'label' => 'Publisert',
                            'advanced' => true,
                            'options' => [
                                'minValue' => (int) $minYear,
                                'maxValue' => (int) $maxYear,
                            ],
                            'index' => [
                                'type' => 'range',
                                'column' => 'verk_aar',
                            ],
                            'operators' => [
                                'eq',
                                'neq',
                                'isnull',
                                'notnull',
                            ],
                        ],
                    ]
                ],
                [
                    'label' => 'Kritikken',
                    'fields' => [
                        [
                            'key' => 'kritiker',
                            'type' => 'autocomplete',
                            'label' => 'Kritiker',
                            'placeholder' => 'Fornavn og/eller etternavn',
                            'options' => [],
                            'index' => [
                                'type' => 'ts',
                                'column' => 'kritiker',
                                'ts_column' => 'kritiker_ts',
                            ],
                            'operators' => [
                                'eq',
                                'neq',
                                'isnull',
                                'notnull',
                            ],
                        ],
                        [
                            'key' => 'publikasjon',
                            'type' => 'autocomplete',
                            'label' => 'Publikasjon',
                            'placeholder' => 'Publikasjon',
                            'options' => [],
                            'index' => [
                                'type' => 'simple',
                                'column' => 'publikasjon',
                            ],
                            'operators' => [
                                'eq',
                                'neq',
                                'isnull',
                                'notnull',
                            ],
                        ],
                        [
                            'key' => 'aar',
                            'type' => 'rangeslider',
                            'label' => 'Publisert',
                            'options' => [
                                'minValue' => (int) $minYear,
                                'maxValue' => (int) $maxYear,
                            ],
                            'index' => [
                                'type' => 'range',
                                'column' => 'aar_numeric',
                            ],
                            'operators' => [
                                'eq',
                                'neq',
                                'isnull',
                                'notnull',
                            ],
                        ],
                        [
                            'key' => 'kritikktype',
                            'type' => 'autocomplete',
                            'label' => 'Type',
                            'placeholder' => 'F.eks. teaterkritikk, forfatterportrett, ...',
                            'options' => [],
                            'index' => [
                                'type' => 'array',
                                'column' => 'kritikktype',
                            ],
                            'operators' => [
                                'eq',
                                'isnull',
                                'notnull',
                            ]
                        ],
                    ]
                ],
            ],
        ];
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'litteraturkritikk_records';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'kritikktype' => 'array',
    ];

    public static function getColumnsFlatList()
    {
        $cols = [];
        foreach (self::getColumns() as $group) {
            foreach ($group['fields'] as $col) {
                $cols[] = $col;
            }
        }
        return $cols;
    }

    public static function getColumnKeys()
    {
        return array_map(function($col) {
            return $col['key'];
        }, self::getColumnsFlatList());
    }

    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }

    /**
     * The persons that are part of this record.
     */
    public function persons()
    {
        return $this->belongsToMany('App\Litteraturkritikk\Person', 'litteraturkritikk_record_person', 'record_id', 'person_id')
            ->withPivot('person_role', 'kommentar', 'pseudonym');
    }

    /**
     * The persons that are part of this record.
     */
    public function forfattere()
    {
        return $this->persons()
            ->where('person_role', '!=', 'kritiker');
    }

    /**
     * The persons that are part of this record.
     */
    public function kritikere()
    {
        return $this->persons()
            ->where('person_role', '=', 'kritiker');
    }

    public function formatKritikkType($name)
    {
        if ($name == 'debatt') {
            $name = 'Debatt';
        }
        return ucfirst($name);
    }

    public function preferredKritikktype($types)
    {
        $preferredTypes = [
            'forfatterportrett',
            'dagskritikk',
            'teaterkritikk',
            'debatt',
            'essay',
            'bokanmeldelse',
            'artikkel',
            'vitenskapelig artikkel',
            'oversiktsartikkel',
            'samtaleprogram',
        ];
        foreach ($preferredTypes as $key) {
            if (in_array($key, $types)) {
                return $key;
            }
        }

        return '';
    }

    public function kritikktypeSkilleord($preferredType)
    {
        $separators = [
            'forfatterportrett' => 'av',
            'dagskritikk' => 'av',
            'teaterkritikk' => 'av',
            'bokanmeldelse' => 'av',
            'debatt' => 'om',
            'artikkel' => 'om',
            'oversiktsartikkel' => 'om',
            'vitenskapelig artikkel' => 'om',
            'essay' => 'om',
            'samtaleprogram' => 'om',
        ];

        return isset($separators[$preferredType]) ? $separators[$preferredType] : 'av/om';
    }

    public function formatVerk()
    {
        $forfatter_delimiter = '; ';
        $forfatter_verk_delimiter = '. ';

        $repr = '';
        $forfattere = [];
        foreach ($this->forfattere as $person) {
            $forfattere[] = strval($person) . (($person->pivot->person_role != 'forfatter') ? ' (' . $person->pivot->person_role . ')' : '');
        }

        $forfattere = implode($forfatter_delimiter, $forfattere);

        $repr .= $forfattere;

        if ($forfattere && $this->verk_tittel) {
            $repr .= $forfatter_verk_delimiter;
        }
        if ($this->verk_tittel) {
            $repr .= '«' . $this->verk_tittel . '»';
        }
        if ($this->verk_aar) {
            $repr .= ' (' . $this->verk_aar . ')';
        }

        return $repr;
    }

    public function formatKritikk()
    {
        $repr = '';

        $kritikere = [];
        foreach ($this->kritikere as $person) {
            $kritikere[] = strval($person);
        }

        $kritikere = implode(', ', $kritikere);

        $repr .= $kritikere ?: 'ukjent';

        if ($this->aar) {
            $repr .= ' (' . $this->aar . ')';
        }

        if (strlen($repr) && $this->tittel) {
            $repr .= '. ';
        }

        if ($this->tittel) {
            $repr .= '«' . $this->tittel . '»';
        }

        if (strlen($repr) && $this->publikasjon) {
            $repr .= '. ';
        }

        if ($this->publikasjon) {
            $repr .= '<em>' . $this->publikasjon . '</em>';
        }
        if ($this->bind) {
            $repr .= ' bd. ' . $this->bind . '';
        }
        if ($this->nummer) {
            $repr .= ' nr. ' . $this->nummer . '';
        }

        if (strlen($repr)) {
            $repr .= '.';
        }

        return $repr;
    }

    public function representation()
    {
        $repr = '<a href="' . action('LitteraturkritikkController@show', $this->id) . '">';

        $repr .= $this->formatKritikk();
        $kritikktype = $this->preferredKritikktype($this->kritikktype);
        $verk = $this->formatVerk();

        $repr .= '</a><br>';

        $repr .= ' ' . $this->formatKritikkType($kritikktype);

        if ($kritikktype && $verk) {
            $repr .= ' ' . $this->kritikktypeSkilleord($kritikktype) . ': ';
        } elseif ($verk) {
            $repr .= 'Om: ';
        }

        $repr .= $verk;

        return $repr;
    }
}
