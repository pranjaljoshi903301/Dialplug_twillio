<?php

namespace Corals\Modules\Ecommerce\Classes;

use Corals\Modules\Ecommerce\Traits\CartOptionsMagicMethodsTrait;

/**
 * Class CartFee.
 */
class CartFee
{
    use CartOptionsMagicMethodsTrait;

    public $locale;
    public $amount;
    public $type;
    public $taxable;
    public $tax;
    public $internationalFormat;

    /**
     * CartFee constructor.
     *
     * @param $amount
     * @param $taxable
     * @param array $options
     */
    public function __construct($amount, $type, $taxable = false, $options = [])
    {
        $this->amount = floatval($amount);
        $this->taxable = $taxable;
        $this->type = $type;
        $this->tax = isset($options['tax']) ? $options['tax'] == 0 ? config('shoppingcart.tax') : $options['tax'] : config('shoppingcart.tax');
        $this->options = $options;
    }

    /**
     * Gets the formatted amount.
     *
     * @param bool $format
     * @param bool $withTax
     *
     * @return string
     */
    public function getAmount($format = true, $withTax = false)
    {
        $total = $this->amount;

        if ($withTax) {
            $total += $this->tax * $total;
        }

        return ShoppingCart::formatMoney($total, $this->locale, $this->internationalFormat, $format);
    }


    /**
     * Gets the discount of an item.
     *
     * @param bool $format
     *
     * @return string
     */
    public function getDiscount($format = true)
    {
        $amount = 0;

        if (app('shoppingcart')->findCoupon($this->code)) {
            $amount = $this->discount;
        }

        return ShoppingCart::formatMoney(
            $amount,
            $this->locale,
            $this->internationalFormat,
            $format
        );
    }
}
