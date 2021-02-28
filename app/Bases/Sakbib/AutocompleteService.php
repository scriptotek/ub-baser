<?php

namespace App\Bases\Sakbib;

use App\Schema\SchemaField;

class AutocompleteService extends \App\Bases\AutocompleteService
{
    /**
     * The completer method to use if no field-specific completer was found.
     * If set to null, an error will be thrown if no completer was found.
     *
     * @var string
     */
    protected $defaultCompleter = 'simpleStringCompleter';

    /**
     * The lister method to use if no field-specific completer was found.
     * If set to null, an error will be thrown if no lister was found.
     *
     * @var string
     */
    protected $defaultLister = 'simpleLister';

    /**
     * Completer methods to use with each field.
     *
     * @var array
     */
    protected $completers = [
        'keywords' => 'jsonArrayCompleter',
        'category' => 'categoryCompleter',
    ];

    /**
     * Lister methods to use with each field.
     *
     * @var array
     */
    protected $listers = [
        'keywords' => 'jsonArrayLister',
    ];

    /**
     * @param SchemaField $field
     * @param string $term
     * @return array
     */
    protected function categoryCompleter(SchemaField $field, string $term): array
    {
        $query = Category::select(
            'id',
            'name',
        )->where('name', 'ilike', $term . '%');

        $data = [];
        foreach ($query->limit(25)->get() as $res) {
            $data[] = [
                'id' => $res->id,
                'prefLabel' => $res->name,
                'record' => $res,
            ];
        }

        return $data;
    }
}
