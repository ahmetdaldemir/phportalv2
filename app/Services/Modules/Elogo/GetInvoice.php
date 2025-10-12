<?php

namespace App\Services\Modules\Elogo;

use App\Abstract\HyperRequest;

class GetInvoice extends HyperRequest
{

    protected $base_uri;
    protected $path;
    protected string $method = 'get';
    protected $type   = 'soap';
    protected $options = [
        'headers' => [
            'Authentication' => 'Authentication',
            'url' => 'http://tempuri.org/',
        ],
    ];

    public function __construct($array)
    {
        $this->path  = $array['path'];
        $this->base_uri = $array['base_url'];
        $this->body = "";
        $this->array = $array;
        $this->uri =  "";
        $this->options['auth'] = array('auth' => array("appKey" => $this->array['key'],"appSecret" => $this->array['password']));
        parent::__construct();
    }

}
