<?php

namespace Corals\Modules\Ecommerce\Classes\Coupons;

use Corals\Modules\Ecommerce\Contracts\CouponContract;
use Corals\Modules\Ecommerce\Classes\ShoppingCart;
use Corals\Modules\Ecommerce\Traits\CouponTrait;

/**
 * Class Percentage.
 */

/**
 * Class Percentage.
 */
class Percentage implements CouponContract
{
    use CouponTrait;

    public $code;
    public $value;

    /**
     * Percentage constructor.
     *
     * @param $code
     * @param $value
     * @param array $options
     */
    public function __construct($code, $value, $options = [])
    {
        $this->code = $code;
        $this->value = $value;

        $this->setOptions($options);
    }

    /**
     * Gets the discount amount.
     *
     * @param $throwErrors boolean this allows us to capture errors in our code if we wish,
     * that way we can spit out why the coupon has failed
     *
     * @return string
     */
    public function discount($throwErrors = false)
    {


        $subTotal = app(ShoppingCart::SERVICE)->subTotal(false);
        $total = $subTotal - $this->value;


        if (config('shoppingcart.discountOnFees', false)) {
            $total = $subTotal + app(ShoppingCart::SERVICE)->feeTotals(false) - $this->value;
        }

        return ShoppingCart::formatMoney(
            $total * $this->value,
            null,
            null,
            false
        );


    }

    /**
     * @return mixed
     */
    public function displayValue()
    {
        return ($this->value * 100) . '%';
    }

    public function getName()
    {
        return 'Percentage';
    }
}
