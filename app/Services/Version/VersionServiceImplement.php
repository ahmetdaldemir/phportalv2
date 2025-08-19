<?php

namespace App\Services\Version;

use Illuminate\Support\Facades\Log;
use App\Services\BaseService;
use App\Repositories\Version\VersionRepository;
use Illuminate\Database\Eloquent\Collection;

class VersionServiceImplement extends BaseService implements VersionService{

    /**
     * don't change $this->mainRepository variable name
     * because used in extends service class
     */
    protected $mainRepository;

    public function __construct(VersionRepository $mainRepository)
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
            $response =  $this->mainRepository->create($data);
            $image = $this->mainRepository->fileUpload($response->image);
            $this->update($response->id,['image' => $image]);
        } catch (\Exception $exception) {
            Log::debug($exception->getMessage());
            return [];
        }
    }
}
