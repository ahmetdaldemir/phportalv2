<?php

namespace App\Repositories\Version;

use LaravelEasyRepository\Repository;

interface VersionRepository extends Repository{

    public function get();
    public function fileUpload($file);

}
