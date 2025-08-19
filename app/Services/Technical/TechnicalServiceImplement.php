<?php

namespace App\Services\Technical;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use App\Services\BaseService;
use App\Repositories\Technical\TechnicalRepository;

class TechnicalServiceImplement extends BaseService implements TechnicalService{

    /**
     * don't change $this->mainRepository variable name
     * because used in extends service class
     */
    protected $mainRepository;

    public function __construct(TechnicalRepository $mainRepository)
    {
        $this->mainRepository = $mainRepository;
    }

    public function all(): ?Collection
    {
        try {
            return $this->mainRepository->all();
        } catch (\Exception $exception) {
            Log::debug($exception->getMessage());
            return [];
        }
    }

    public function get(): ?Collection
    {
        try {
            return $this->mainRepository->get();
        } catch (\Exception $exception) {
            Log::debug($exception->getMessage());
            return [];
        }
    }


    public function find($id)
    {
        try {
            return $this->mainRepository->find($id);
        } catch (\Exception $exception) {
            Log::debug($exception->getMessage());
            return [];
        }
    }

    public function delete($id)
    {
        try {
            return $this->mainRepository->delete($id);
        } catch (\Exception $exception) {
            Log::debug($exception->getMessage());
            return [];
        }
    }
    public function update($id,$data)
    {
        try {
            return $this->mainRepository->update($id,$data);
        } catch (\Exception $exception) {
            Log::debug($exception->getMessage());
            return [];
        }
    }
    public function create($data)
    {
        try {
            return $this->mainRepository->create($data);
        } catch (\Exception $exception) {
            Log::debug($exception->getMessage());
            return [];
        }
    }
}
