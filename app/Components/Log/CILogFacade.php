<?php

namespace App\Components\Log;

use Illuminate\Support\Facades\Facade;

class CILogFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'cilog';
    }
}

?>
