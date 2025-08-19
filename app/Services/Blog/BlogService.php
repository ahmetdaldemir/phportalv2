<?php

namespace App\Services\Blog;

use Illuminate\Database\Eloquent\Collection;


interface BlogService {


    public function all(): ?Collection;
    public function delete($id);
    public function find($id);
    public function create($data);
    public function update($id, array $data);
}
