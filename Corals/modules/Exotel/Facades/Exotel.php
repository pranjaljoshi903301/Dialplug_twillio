<?php

namespace Corals\Modules\Exotel\Facades;

use Illuminate\Support\Facades\Facade;

class Exotel extends Facade
{
    /**
     * @return mixed
     */
    protected static function getFacadeAccessor()
    {
        return \Corals\Modules\Exotel\Classes\Exotel::class;
    }
}
