<?php

namespace Corals\Modules\Demo\Facades;

use Illuminate\Support\Facades\Facade;

class Demos extends Facade
{
    /**
     * @return mixed
     */
    protected static function getFacadeAccessor()
    {
        return \Corals\Modules\Demo\Classes\Demos::class;
    }
}
