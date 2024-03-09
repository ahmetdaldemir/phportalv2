<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

 class Tax extends Enum
{
    public function taxes(): array
    {
       return ['0' => '%0','1' => '%1','8' => '%8','18' => '%18'];
    }
 }
