<?php

namespace App\Services\Version;

use Illuminate\Database\Eloquent\Collection;
use LaravelEasyRepository\BaseService;

interface VersionService extends BaseService{

    public function all(): ?Collection;

    public function get(): ?Collection;
}
