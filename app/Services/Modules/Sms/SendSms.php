<?php

namespace App\Services\Modules\Sms;

use App\Abstract\HyperRequest;

class SendSms extends HyperRequest
{
    protected $base_uri;
    protected $path;
    protected string $method = 'get';
    protected $type = 're';
    protected $options = [
        'headers' => [
            'Authentication' => 'Authentication',
            'url' => 'http://tempuri.org/',
        ],
    ];

    public function __construct($array)
    {
        $this->path = "/api/v1/login";
        $this->base_uri = "https://www.goldmesaj.com.tr";
        $this->body = "";
        $this->array = $array;
        $this->uri = "";
        $this->options['body'] = json_encode($this->payload(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        parent::__construct();
    }


    public function payload(): array
    {
        return [
            "username" => "user",
            "password" => "test",
            "sdate" => "",
            "vperiod" => "48",
            "message" => [
                "sender" => "ALFANUMERIK",
                "text" => "Mesajmetni",
                "utf8" => "0|1",
                "gsm" => [
                    "905445554433",
                    "905445554434"
                ],
            ],
        ];

    }
}
