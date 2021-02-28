<?php

namespace App\Bases\Sakbib;

use App\Http\Controllers\BaseController;

class Controller extends BaseController
{
    protected $logGroup = 'sakbib';

    public static $defaultColumns = [
        'title',
        'creators',
        'year',
    ];

    public static $defaultSortOrder = [
        // ['key' => 'utgivelsesaar', 'direction' => 'desc'],
    ];
}
