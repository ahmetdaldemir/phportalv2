<?php

namespace App\Services\Modules\Elogo;

use App\Models\EInvoice;
use App\Models\EInvoiceDetail;
use Illuminate\Support\Facades\Auth;

class CreateInvoice
{
    public function __construct()
    {

    }

    public function store(array $x, $company = null, $user = null, $type)
    {
        $x = EInvoice::updateOrCreate(
            ['company_id' => $company ?? Auth::user()->company_id, 'Uuid' => $x['Uuid']],
            [
                'user_id' => $user,
                'InvoiceType' => $x['InvoiceType'],
                'IssueDate' => $x['IssueDate'],
                'ElementId' => $x['ElementId'],
                'InvoiceTotal' => $x['InvoiceTotal'],
                'SupplierVknTckn' => $x['SupplierVknTckn'],
                'SupplierPartyName' => $x['SupplierPartyName'],
                'CustomerPartyName' => $x['CustomerPartyName'],
                'CustomerVknTckn' => $x['CustomerVknTckn'],
                'Description' => $x['Description'],
                'ProfileID' => $x['ProfileID'],
                'CurrencyUnit' => $x['CurrencyUnit'],
                'TaxAmount' => $x['TaxAmount'],
                'PayableAmount' => $x['PayableAmount'],
                'AllowanceTotalAmount' => $x['AllowanceTotalAmount'],
                'TaxInclusiveAmount' => $x['TaxInclusiveAmount'],
                'TaxExclusiveAmount' => $x['TaxExclusiveAmount'],
                'LineExtensionAmount' => $x['LineExtensionAmount'],
                'PKAlias' => $x['PKAlias'],
                'GBAlias' => $x['GBAlias'],
                'EnvelopeId' => $x['EnvelopeId'],
                'CurrentDate' => $x['CurrentDate'],
                'saveType' => $type,
            ]
        );
        return $x->id;
    }

    public function store_detail(EInvoice $EInvoice, $array)
    {
        foreach ($array as $item) {
            $einvoiceDetail = new  EInvoiceDetail();
            $einvoiceDetail->e_invoice_id = $EInvoice->id;
            $einvoiceDetail->user_id = Auth::user()->id;
            $einvoiceDetail->company_id = Auth::user()->company_id;
            $einvoiceDetail->stock_card_id = $item->stock_card_id;
            $einvoiceDetail->name = $item->name;
            $einvoiceDetail->quantity = $item->quantity;
            $einvoiceDetail->price = $item->price;
            $einvoiceDetail->taxPrice = $item->taxPrice;
            $einvoiceDetail->tax = $item->tax;
            $einvoiceDetail->total_price = $item->total_price;
            $einvoiceDetail->save();
        }
    }
}
