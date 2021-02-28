<?php

namespace App\Bases\Sakbib;

class RecordObserver
{
    protected function refreshView()
    {
        // Refresh view
        \DB::unprepared('REFRESH MATERIALIZED VIEW CONCURRENTLY sb_view');
    }

    /**
     * Handle the app bases opes record "created" event.
     *
     * @param Publication $record
     * @return void
     */
    public function created(Publication $record)
    {
        $this->refreshView();
    }

    /**
     * Handle the app bases opes record "updated" event.
     *
     * @param Publication $record
     * @return void
     */
    public function updated(Publication $record)
    {
        $this->refreshView();
    }

    /**
     * Handle the app bases opes record "deleted" event.
     *
     * @param Publication $record
     * @return void
     */
    public function deleted(Publication $record)
    {
        $this->refreshView();
    }

    /**
     * Handle the app bases opes record "restored" event.
     *
     * @param Publication $record
     * @return void
     */
    public function restored(Publication $record)
    {
        $this->refreshView();
    }

    /**
     * Handle the app bases opes record "force deleted" event.
     *
     * @param Publication $record
     * @return void
     */
    public function forceDeleted(Publication $record)
    {
        $this->refreshView();
    }
}
