<?php

namespace Corals\Modules\Twillio\Facades;

use Illuminate\Support\Facades\Facade;

class Twillio extends Facade
{
    /**
     * @return mixed
     */
    protected static function getFacadeAccessor()
    {
        return \Corals\Modules\Twillio\Classes\Twillio::class;
    }
}
