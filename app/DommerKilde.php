<?php

namespace App;

class DommerKilde extends \Eloquent
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dommer_kilder';

    /**
     * Get the records from this publication.
     */
    public function poster()
    {
        return $this->hasMany('App\DommerRecord');
    }

    public function __toString()
    {
        return $this->navn;
    }
}
