<?php

\Corals\Settings\Models\Setting::whereIn('code',['ecommerce_search_enable_wildcards',
    'ecommerce_wishlist_enable',
    'ecommerce_rating_enable',
    'ecommerce_shipping_shippo_sandbox_mode',
    'ecommerce_tax_calculate_tax'])->update(['type'=>'BOOLEAN']);


