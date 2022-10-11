<?php

namespace Corals\Modules\BM\Facades;

use Illuminate\Support\Facades\Facade;

class BM extends Facade
{
    /**
     * @return mixed
     */
    protected static function getFacadeAccessor()
    {
        return \Corals\Modules\BM\Classes\BM::class;
    }
}