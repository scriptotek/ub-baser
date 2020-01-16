<?php

namespace App\Bases\Litteraturkritikk;

use App\Schema\SchemaField;
use Illuminate\Support\Arr;

class AutocompleteService extends \App\Services\AutocompleteService
{
    /**
     * The completer method to use if no field-specific completer was found.
     * If set to null, an error will be thrown if no completer was found.
     *
     * @var string
     */
    protected $defaultCompleter = null;

    /**
     * The lister method to use if no field-specific completer was found.
     * If set to null, an error will be thrown if no lister was found.
     *
     * @var string
     */
    protected $defaultLister = null;

    /**
     * Completer methods to use with each field.
     *
     * @var array
     */
    protected $completers = [
        'publikasjon' => 'simpleStringLister',
        'spraak' => 'simpleStringLister',
        'verk_spraak' => 'simpleStringLister',
        'verk_sjanger' => 'simpleStringLister',
        'utgivelsessted' => 'simpleStringLister',
        'verk_utgivelsessted' => 'simpleStringLister',

        'kritikktype' => 'jsonArrayCompleter',
        'tags' => 'jsonArrayCompleter',

        'verk_tittel' => 'textSearchCompleter',

        'person' => 'personCompleter',
        'verk_forfatter' => 'personCompleter',
        'kritiker' => 'personCompleter',
    ];

    /**
     * Lister methods to use with each field.
     *
     * @var array
     */
    protected $listers = [
        'kritikktype' => 'jsonArrayLister',
        'tags' => 'jsonArrayLister',
    ];

    protected function personCompleter(SchemaField $field, string $term): array
    {
        if ($this->noLetterChars($term)) {
            return [];
        }

        $query = PersonView::select(
            'id',
            'etternavn_fornavn',
            'etternavn',
            'fornavn',
            'kjonn',
            'fodt',
            'dod',
            'bibsys_id',
            'wikidata_id'
        )
            ->whereRaw(
                "any_field_ts @@ (phraseto_tsquery('simple', ?)::text || ':*')::tsquery",
                [$term]
            );

        $personRole = Arr::get($field, 'person_role');
        if ($personRole) {
            $query->whereRaw('? = ANY(roller)', [$personRole]);
        } else {
            // Skjul personer som ikke er i bruk
            $query->whereRaw('CARDINALITY(roller) != 0');
        }

        $data = [];
        foreach ($query->limit(25)->get() as $res) {
            $data[] = [
                'id' => $res->id,
                'value' => $res->etternavn_fornavn,
                'record' => $res,
            ];
        }

        return $data;
    }
}