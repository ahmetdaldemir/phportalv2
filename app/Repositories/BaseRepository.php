<?php

namespace App\Repositories;

/**
 * Base Repository Interface
 * Replaced LaravelEasyRepository\Repository
 */
interface BaseRepository
{
    public function all();
    public function get($columns = ['*']);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
