<?php

namespace App\Services;

/**
 * Base Service Class
 * Replaced LaravelEasyRepository\Service
 */
abstract class BaseService
{
    protected $mainRepository;

    public function __construct($mainRepository = null)
    {
        if ($mainRepository) {
            $this->mainRepository = $mainRepository;
        }
    }

    public function getRepository()
    {
        return $this->mainRepository;
    }
}
