<?php

namespace App\Services\Refund;

use App\Repositories\Refund\RefundRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use LaravelEasyRepository\Service;

class RefundServiceImplement extends Service implements RefundService{
    /**
     * don't change $this->mainRepository variable name
     * because used in extends service class
     */
    protected RefundRepository $mainRepository;

    public function __construct(RefundRepository $mainRepository)
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
