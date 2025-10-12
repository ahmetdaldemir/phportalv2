<?php

namespace App\Services\Company;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface CompanyService extends BaseService{

    public function all(): ?Collection;
    public function delete($id);
    public function find($id);
    public function create($data);
    public function update($id, array $data);

}
