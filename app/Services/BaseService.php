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
    
    public function get($columns = ['*'])
    {
        return $this->mainRepository->get($columns);
    }
    
    public function all()
    {
        return $this->mainRepository->all();
    }
    
    public function find($id)
    {
        return $this->mainRepository->find($id);
    }
    
    public function create($data)
    {
        return $this->mainRepository->create($data);
    }
    
    public function update($id, $data)
    {
        return $this->mainRepository->update($id, $data);
    }
    
    public function delete($id)
    {
        return $this->mainRepository->delete($id);
    }
}
