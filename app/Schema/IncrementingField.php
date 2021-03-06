<?php

namespace App\Schema;

class IncrementingField extends SchemaField
{
    public const TYPE = 'incrementing';

    public function setDefaults()
    {
        $this->data['showInTableView'] = false;
        $this->data['showInRecordView'] = false;
        $this->data['search']->init(false);
        $this->data['edit']->init(false);
    }
}
