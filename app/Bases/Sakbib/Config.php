<?php

namespace App\Bases\Sakbib;

use App\Bases\AutocompleteService;
use App\Bases\Config as BaseConfig;

class Config extends BaseConfig
{
    public $classBindings = [
        'Record' => Publication::class,
        'RecordView' => PublicationView::class,
    ];
}
