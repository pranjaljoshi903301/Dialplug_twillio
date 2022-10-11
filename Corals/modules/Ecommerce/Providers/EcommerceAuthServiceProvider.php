<?php

namespace Corals\Modules\Ecommerce\Providers;

use Corals\Modules\Ecommerce\Models\Attribute;
use Corals\Modules\Ecommerce\Models\Brand;
use Corals\Modules\Ecommerce\Models\Category;
use Corals\Modules\Ecommerce\Models\Coupon;
use Corals\Modules\Ecommerce\Models\Order;
use Corals\Modules\Ecommerce\Models\Product;
use Corals\Modules\Ecommerce\Models\Shipping;
use Corals\Modules\Ecommerce\Models\SKU;
use Corals\Modules\Ecommerce\Models\Tag;
use Corals\Modules\Ecommerce\Policies\AttributePolicy;
use Corals\Modules\Ecommerce\Policies\BrandPolicy;
use Corals\Modules\Ecommerce\Policies\CategoryPolicy;
use Corals\Modules\Ecommerce\Policies\CouponPolicy;
use Corals\Modules\Ecommerce\Policies\OrderPolicy;
use Corals\Modules\Ecommerce\Policies\ProductPolicy;
use Corals\Modules\Ecommerce\Policies\ShippingPolicy;
use Corals\Modules\Ecommerce\Policies\SKUPolicy;
use Corals\Modules\Ecommerce\Policies\TagPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class EcommerceAuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Product::class => ProductPolicy::class,
        SKU::class => SKUPolicy::class,
        Order::class => OrderPolicy::class,
        Category::class => CategoryPolicy::class,
        Attribute::class => AttributePolicy::class,
        Brand::class => BrandPolicy::class,
        Coupon::class => CouponPolicy::class,
        Tag::class => TagPolicy::class,
        Shipping::class => ShippingPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
