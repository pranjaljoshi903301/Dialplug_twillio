<?php

namespace Corals\Modules\Ecommerce\Models;

use Corals\Foundation\Models\BaseModel;
use Corals\Foundation\Traits\ModelPropertiesTrait;
use Corals\Foundation\Transformers\PresentableTrait;
use Corals\Modules\CMS\Models\Content;
use Corals\Modules\Payment\Common\Models\Invoice;
use Corals\Modules\Payment\Common\Models\Transaction;
use Corals\User\Models\User;
use Spatie\Activitylog\Traits\LogsActivity;

class Order extends BaseModel
{
    use PresentableTrait, LogsActivity, ModelPropertiesTrait;

    protected $table = 'ecommerce_orders';
    /**
     *  Model configuration.
     * @var string
     */
    public $config = 'ecommerce.models.order';

    protected static $logAttributes = ['status', 'amount'];

    protected $guarded = ['id'];

    protected $casts = [
        'shipping' => 'array',
        'billing' => 'array',
        'properties' => 'json',
    ];

    public function scopeMyOrders($query)
    {
        return $query->where('user_id', user()->id);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'canceled');
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'sourcable');
    }

    public function getTransactionSource()
    {
        $order_number = $this->order_number;
        return "<a target='_blank' href='" . url('e-commerce/orders/' . $this->hashed_id) . "'>  $order_number </a>";

    }

    public function getInvoiceReference($target = "dashboard")
    {
        $order_number = $this->order_number;
        if ($target == "pdf") {
            return $order_number;
        } else {
            return "<a href='" . url('e-commerce/orders/' . $this->hashed_id) . "'>  $order_number </a>";

        }
    }

    public function getInvoicePayableTo()
    {
        return \Ecommerce::getInvoicePayableToCompanyDetails();
    }

    /**
     * Get all of the premuim posts for the order.
     */
    public function posts()
    {
        return $this->morphToMany(Content::class, 'sourcable', 'postables');
    }

    public function invoice()
    {
        return $this->morphOne(Invoice::class, 'invoicable');
    }

    public function getIdentifier($key = null)
    {
        if (!is_null($key)) {
            return parent::getIdentifier($key);
        }

        return $this->present('order_number');
    }

    public function getTaxAmount()
    {
        $discount_items = $this->items()->where('type', 'Tax')->get();
        return $discount_items ? $discount_items->sum('amount') : 0.0;

    }


    public function getShippingAmount()
    {
        $shipping_items = $this->items()->where('type', 'Shipping')->get();
        return $shipping_items ? $shipping_items->sum('amount') : 0.0;

    }


    public function getCouponCode()
    {
        $coupon_item = $this->items()->where('type', 'Discount')->first();
        return $coupon_item ? $coupon_item->getProperty('code') : '';

    }

    public function getPaymentRefundedAmount()
    {
        $refunded_amount = $this->transactions()
            ->where('type', 'order_refund')
            ->sum('amount');

        return $refunded_amount * -1;
    }
}
