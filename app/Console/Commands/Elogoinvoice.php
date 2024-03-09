<?php

namespace App\Console\Commands;

use App\Abstract\Elogo;
use App\Models\EInvoice;
use App\Services\Modules\Elogo\CreateInvoice;
use elogo_api\elogo_api;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Elogoinvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     * php artisan invoice:elogo EINVOICEDETAIL OUT 1
     */
    protected $signature = 'invoice:elogo {type} {sort} {company}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    public $connector;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $type = $this->argument('type');
        $sort = $this->argument('sort');
        $company = $this->argument('company');

        $this->connector = new Elogo();
        $result = $this->connector->elogo->get_documents_list(null, null, $sort, $type);
        if (count((array)$result['message']) > 0) {
            //$result = $this->connector->elogo->get_documents_data("0fab21b4-82e1-4ea8-8d20-361300773dc6",'EINVOICE','XML');
            //$unzip = $this->connector->elogo->unzip("0fab21b4-82e1-4ea8-8d20-361300773dc6",$result['message']->binaryData->Value);
            $document = $result['message']->Document;
            foreach ($document as $item) {
                $x = json_decode($item->docInfo->string, true);
                $createinvoice = new CreateInvoice();
                $createinvoice->store($x,$company,1,$type);
            }
        } else {
            Log::info("Fatura Bulunamadı");
        }

    }
}
