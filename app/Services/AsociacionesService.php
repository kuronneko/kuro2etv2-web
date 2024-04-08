<?php

namespace App\Services;

use Carbon\Carbon;

class AsociacionesService
{
    public static function tieneRelacionAsociada($parent, $relationship)
    {
        return $parent->{$relationship}()->withTrashed()->exists();
    }
}
