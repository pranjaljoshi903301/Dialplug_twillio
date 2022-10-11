<?php

namespace Corals\Modules\Ecommerce\Policies;

use Corals\Foundation\Policies\BasePolicy;
use Corals\Modules\Ecommerce\Models\Order;
use Corals\User\Models\User;

class OrderPolicy extends BasePolicy
{
    protected $administrationPermission = 'Administrations::admin.ecommerce';

    protected $skippedAbilities = [
        'payOrder', 'update', 'refundOrder'
    ];

    /**
     * @param User $user
     * @param null $order
     * @return bool
     */
    public function view(User $user, $order = null)
    {
        if ($user->can('Ecommerce::orders.access')) {
            return true;
        }

        if ($user->can('Ecommerce::my_orders.access') && $order && $order->user->id == $user->id) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('Ecommerce::order.create');
    }

    /**
     * @param User $user
     * @param Order $order
     * @return bool
     */
    public function update(User $user, Order $order)
    {
        if (($user->can('Ecommerce::order.update') || isSuperUser()) && $order->status == 'pending') {
            return true;
        }
        return false;
    }

    public function update_payment(User $user, Order $order)
    {
        return $user->can('Ecommerce::order.update');
    }

    public function update_shipping(User $user, Order $order)
    {
        return $user->can('Ecommerce::order.update');
    }

    public function update_status(User $user, Order $order)
    {
        return $user->can('Ecommerce::order.update');
    }

    public function notify_buyer(User $user, Order $order)
    {
        return $user->can('Ecommerce::order.update') || isSuperUser();
    }

    /**
     * @param User $user
     * @param Order $order
     * @return bool
     */
    public function destroy(User $user, Order $order)
    {
        if ($user->can('Ecommerce::order.delete')) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @param Order $order
     * @return bool
     */
    public function refundOrder(User $user, Order $order)
    {
        $payment_status = $order->billing['payment_status'] ?? '';

        if ($payment_status && $payment_status != 'refunded' && $order->status != 'canceled') {
            if ($this->update($user, $order)) {
                return true;
            }
        }

        return false;
    }

    public function payOrder(User $user, Order $order)
    {
        $payment_status = $order->billing['payment_status'] ?? '';

        return $user->hasPermissionTo('Ecommerce::my_orders.access') && $order->user
            && $order->user->id == $user->id
            && ($order->status == "pending")
            && $payment_status != 'paid';
    }
}
