<?php

namespace App\Jobs;

use App\Abstract\Elogo;
use App\Models\City;
use App\Models\Company;
use App\Models\Customer;
use App\Models\StockCard;
use App\Models\Town;
use elogo_api\elogo_api;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class ElogoCreateInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Elogo $connector;
    protected $type;
    protected $array;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type, $array)
    {
        $this->connector = new Elogo();
        $this->type = $type;
        $this->array = $array;

        if ($this->type == 'create') {
            $this->create($this->array);
        }
    }

    public function handle()
    {
        $customer = Customer::find($this->array->customer_id);
        $componies = Company::find(Auth::user()->company_id);
        //FATURA KESEN FİRMA BİLGİLERİ
        $this->connector->my_company = [
            'websitesi' => $componies->web_sites,
            'ticari_sicil_no' => $componies->commercial_registration_number,
            'vergi_no' => $componies->tax_number,
            'vergi_dairesi' => $componies->tax_office,
            'mersis_no' => $componies->mersis_number,
            'unvan' => $componies->company_name,
            'tel' => $componies->phone,
            'mail' => $componies->email,
            'adres' => [
                'acik_adres' => $componies->email,
                'bina_adi' => '',
                'bina_no' => '',
                'mahalle_ilce' => Town::find($componies->district)->name,
                'il' => City::find($componies->city)->name,
                'posta_kodu' => $componies->postal_code,
                'ulke' => $componies->country,
                'ulke_kodu' => $componies->country_code,
            ],
        ];
        //FATURA KESEN FİRMA BİLGİLERİ

        //FATURA KESİLEN FİRMA BİLGİLERİ
        $this->connector->customer_company = [
            'yetkili_adi' => $customer->firstname,
            'yetkili_soyadi' => $customer->lastname,
            'unvan' => $customer->fullname,
            'websitesi' => '',
            'firma_turu' => $customer->company_type,
            'vergi_no_tckn' => $customer->tc,
            'vergi_dairesi' => '',
            'tel' => $customer->phone1,
            'fax' => '',
            'email' => $customer->email,
            'adres' => [
                'acik_adres' => $customer->address,
                'bina_adi' => '',
                'bina_no' => '',
                'mahalle_ilce' => Town::find($customer->district)->name,
                'il' => City::find($customer->city)->name,
                'posta_kodu' => '34600',
                'ulke' => 'Türkiye',
                'ulke_kodu' => 'TR',
            ],
        ];
        //FATURA KESİLEN FİRMA BİLGİLERİ
        $kdvtutar = 0;
        $geneltoplam = 0;
        $toplamtutar = 0;
        $products = $this->array->group_a;
        $a = [];
        foreach ($products as $product) {
            $a[] = array(
                'hizmet_adi' => StockCard::find($product['stock_card_id'])->name,
                'hizmet_aciklama' => 'Hizmet Açıklama',
                'adet' => $product['quantity'],
                'tutar' => $product['sale_price'] - (($product['sale_price'] * $product['tax']) / 100),
                'kdv_tutar' => ($product['sale_price'] * $product['tax']) / 100,
                'kdv_oran' => $product['tax'],
            );

            $kdvtutar += ($product['sale_price'] * $product['tax']) / 100;
            $geneltoplam += $product['sale_price'] + ($product['sale_price'] * $product['tax']) / 100;
            $toplamtutar += $product['sale_price'] - ($product['sale_price'] * $product['tax']) / 100;
        }

        $this->connector->invoice = [
            'urun_hizmet' => $a,
            'genel_toplam' => $geneltoplam,
            'toplam_tutar' => $toplamtutar,
            'kdv_tutar' => $kdvtutar,
            'kdv_oran' => 18.00,
            //'kdv_muhafiyet_kodu' => 223,
        ];

        $result = $this->connector->elogo->send_einvoice();
        print_r($result);
    }
}
