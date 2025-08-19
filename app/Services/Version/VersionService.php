<?php

namespace App\Services\Version;

use Illuminate\Database\Eloquent\Collection;


interface VersionService {

    public function all(): ?Collection;

    public function get(): ?Collection;
}
