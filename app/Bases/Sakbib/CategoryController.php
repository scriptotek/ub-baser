<?php

namespace App\Bases\Sakbib;

use App\Http\Controllers\BaseController;

class CategoryController extends BaseController
{
    protected $logGroup = 'sakbib';
    protected $model = 'Category';
    protected $showModel = 'Category';
    protected $editView = 'categories.edit';
    protected $showView = 'categories.show';
}
