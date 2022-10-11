<div class="row">
    <div class="col-lg-3 col-xs-6">
        @widget('products')
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        @widget('coupons')
    </div>
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        @widget('product_categories')

    </div>

    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        @widget('store_revenue')

    </div>
    <!-- ./col -->
</div>
<div class="row">
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        @widget('orders')
    </div>
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        @widget('processing_orders')
    </div>
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        @widget('pending_orders')
    </div>
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        @widget('cancelled_orders')
    </div>
    <!-- ./col -->
</div>

<div class="row">
    <div class="col-lg-6 col-xs-12">
        @widget('monthly_revenue')
    </div>
    <div class="col-lg-6 col-xs-12">
        @widget('brand_ratio')
    </div>
</div>

<hr/>


