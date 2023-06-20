<?php

namespace App\Observers;

use App\Constants\FineConstant;
use App\Models\Fine;
use App\Models\ReportFine;

class FineObserve
{
    /**
     * Handle the Fine "created" event.
     *
     * @param  \App\Models\Fine  $fine
     * @return void
     */
    public function created(Fine $fine)
    {
        ReportFine::query()->firstOrCreate(['organization_id' => getOrganization()])->increment(FineConstant::KEY_STATUS_PENDING);
    }

    /**
     * Handle the Fine "updated" event.
     *
     * @param  \App\Models\Fine  $fine
     * @return void
     */
    public function updated(Fine $fine)
    {
        $status = $fine->status;
        $report = ReportFine::query()->first();
        if (($oldStatus = $fine->getOriginal('status')) !== $status) {
            (clone $report)->increment($status);
            (clone $report)->decrement($oldStatus);
        }
    }

    /**
     * Handle the Fine "deleted" event.
     *
     * @param  \App\Models\Fine  $fine
     * @return void
     */
    public function deleted(Fine $fine)
    {
        //
    }

    /**
     * Handle the Fine "restored" event.
     *
     * @param  \App\Models\Fine  $fine
     * @return void
     */
    public function restored(Fine $fine)
    {
        //
    }

    /**
     * Handle the Fine "force deleted" event.
     *
     * @param  \App\Models\Fine  $fine
     * @return void
     */
    public function forceDeleted(Fine $fine)
    {
        //
    }
}
