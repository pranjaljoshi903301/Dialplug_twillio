<?php

namespace Corals\Modules\BT\Facades;

use Illuminate\Support\Facades\Facade;

class BT extends Facade
{
    /**
     * @return mixed
     */
    protected static function getFacadeAccessor()
    {
        return \Corals\Modules\BT\Classes\BT::class;
    }
}