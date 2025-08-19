<?php

namespace App\Repositories\Version;

use App\Repositories\BaseRepository;

interface VersionRepository extends BaseRepository{

    public function get();
    public function fileUpload($file);

}
