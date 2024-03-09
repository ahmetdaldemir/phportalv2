<?php

namespace App\Services\Blog;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface BlogService extends BaseService{


    public function all(): ?Collection;
    public function delete($id);
    public function find($id);
    public function create($data);
    public function update($id, array $data);
}
