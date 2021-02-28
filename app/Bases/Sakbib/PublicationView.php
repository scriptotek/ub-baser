<?php

namespace App\Bases\Sakbib;

use Illuminate\Support\Facades\Auth;

class PublicationView extends Publication
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sb_view';

    public static function refreshView()
    {
        \DB::unprepared('REFRESH MATERIALIZED VIEW sb_view');
    }

    public static function query()
    {
        if (!Auth::check()) {
            return parent::query()->where('public', '=', true);
        }
        return parent::query();
    }
}
