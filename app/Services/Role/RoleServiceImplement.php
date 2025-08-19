<?php

namespace App\Services\Role;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use App\Services\BaseService;
use App\Repositories\Role\RoleRepository;

class RoleServiceImplement extends BaseService implements RoleService{

     /**
     * don't change $this->mainRepository variable name
     * because used in extends service class
     */
     protected $mainRepository;

    public function __construct(RoleRepository $mainRepository)
    {
      $this->mainRepository = $mainRepository;
    }

    // Define your custom methods :)

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
