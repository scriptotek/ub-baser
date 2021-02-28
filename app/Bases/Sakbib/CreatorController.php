<?php

namespace App\Bases\Sakbib;

use App\Http\Controllers\BaseController;

class CreatorController extends BaseController
{
    protected $logGroup = 'sakbib';
    protected $model = 'Creator';
    protected $showModel = 'Creator';

    protected $editView = 'creators.edit';
    protected $showView = 'creators.show';
}
