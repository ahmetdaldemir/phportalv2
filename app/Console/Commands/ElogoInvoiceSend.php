<?php

namespace App\Console\Commands;

use App\Jobs\ElogoCreateInvoice;
use App\Models\EInvoice;
use Illuminate\Console\Command;

class ElogoInvoiceSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'einvoice:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $invoices = EInvoice::where('invoiceStatus','Waiting')->get();
        foreach ($invoices as $invoice)
        {
            ElogoCreateInvoice::dispatch('create', $invoice)->delay(now()->addMinutes(1))->onQueue('einvoice');
        }
    }
}
