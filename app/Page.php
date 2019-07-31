<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    /**
     * Get the user that last updated the page.
     */
    public function updatedBy()
    {
        return $this->belongsTo('App\User', 'updated_by', 'id');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
