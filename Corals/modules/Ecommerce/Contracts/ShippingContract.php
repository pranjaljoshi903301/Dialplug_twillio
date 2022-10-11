<?php

namespace Corals\Modules\Ecommerce\Contracts;

/**
 * Interface ShippingContract.
 */
interface ShippingContract
{
    /**
     * ShippingContract constructor.
     *
     */
    public function __construct();


    /**
     * ShippingContract initialize.
     *
     */
    public function initialize($options = []);

    /**
     * Gets the Available Shipping methods.
     *
     * @return string
     */
    public function getAvailableShipping($to_address, $shippable_items, $shipping_rule, $user);


    /**
     * create tshipping Transaction
     *
     * @return double
     */
    public function createShippingTransaction($shipping_order_item);

    /**
     * Get provider Name
     *
     * @return string
     */
    public function providerName();


}
