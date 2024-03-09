<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EInvoice extends BaseModel
{
    use HasFactory,SoftDeletes;


    protected $fillable = [
          'user_id',
          'company_id',
          'InvoiceType',
          'IssueDate',
          'ElementId',
          'InvoiceTotal',
          'SupplierVknTckn',
          'SupplierPartyName',
          'CustomerPartyName',
          'CustomerVknTckn',
          'Description',
          'ProfileID',
          'Uuid',
          'CurrencyUnit',
          'TaxAmount',
          'PayableAmount',
          'AllowanceTotalAmount',
          'TaxInclusiveAmount',
          'TaxExclusiveAmount',
          'LineExtensionAmount',
          'PKAlias',
          'GBAlias',
          'EnvelopeId',
          'CurrentDate',

    ];
}
