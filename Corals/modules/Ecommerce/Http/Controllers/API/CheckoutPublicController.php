<?php

namespace Corals\Modules\Ecommerce\Http\Controllers\API;

use Corals\Foundation\Http\Controllers\APIPublicController;
use Corals\Modules\Ecommerce\Services\CheckoutService;
use Corals\Modules\Ecommerce\Traits\API\CheckoutControllerCommonFunctions;

class CheckoutPublicController extends APIPublicController
{
    use CheckoutControllerCommonFunctions;

    protected $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;

        parent::__construct();
    }
}
