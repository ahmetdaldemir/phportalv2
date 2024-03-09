<?php

namespace App\Abstract;

use elogo_api\elogo_api;

class Elogo
{
    private mixed $environment;

    public elogo_api $elogo;
    public function __construct()
    {
        $this->environment = config('services.elogo');
        $this->elogo = new elogo_api($this->environment['username'], $this->environment['password'], false);
        $this->elogo->invoce_prefix = 'ERK';
    }
}
