<?php

namespace App\Observers;

use App\Models\FinansTransaction;

class FinansTransactionObserver
{


    public function creating(FinansTransaction $FinansTransaction)
    {
        $FinansTransaction->company_id = auth()->user()->company_id;
    }
    /**
     * Handle the FinansTransaction "created" event.
     *
     * @param  \App\Models\FinansTransaction  $FinansTransaction
     * @return void
     */
    public function created(FinansTransaction $FinansTransaction)
    {
        //
    }

    /**
     * Handle the FinansTransaction "updated" event.
     *
     * @param  \App\Models\FinansTransaction  $FinansTransaction
     * @return void
     */
    public function updated(FinansTransaction $FinansTransaction)
    {
        //
    }

    /**
     * Handle the FinansTransaction "deleted" event.
     *
     * @param  \App\Models\FinansTransaction  $FinansTransaction
     * @return void
     */
    public function deleted(FinansTransaction $FinansTransaction)
    {
        //
    }

    /**
     * Handle the FinansTransaction "restored" event.
     *
     * @param  \App\Models\FinansTransaction  $FinansTransaction
     * @return void
     */
    public function restored(FinansTransaction $FinansTransaction)
    {
        //
    }

    /**
     * Handle the FinansTransaction "force deleted" event.
     *
     * @param  \App\Models\FinansTransaction  $FinansTransaction
     * @return void
     */
    public function forceDeleted(FinansTransaction $FinansTransaction)
    {
        //
    }
}
