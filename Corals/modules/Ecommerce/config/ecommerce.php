<?php

return [
    'models' => [
        'product' => [
            'presenter' => \Corals\Modules\Ecommerce\Transformers\ProductPresenter::class,
            'resource_url' => 'e-commerce/products',
            'default_image' => 'assets/corals/images/default_product_image.png',
            'translatable' => ['name', 'description', 'caption'],
            'actions' => [
                'delete' => [
                    'href_pattern' => ['pattern' => '[arg]', 'replace' => ['return $object->getOriginalShowURL();']],
                    'label_pattern' => ['pattern' => '[arg]', 'replace' => ["return trans('Corals::labels.delete');"]],
                    'policies' => ['destroy'],
                    'data' => [
                        'action' => 'delete',
                        'table' => '.dataTableBuilder'
                    ],
                ],
                'sku' => [
                    'href_pattern' => [
                        'pattern' => '[arg]/sku',
                        'replace' => ['return $object->getOriginalShowURL();']
                    ],
                    'label_pattern' => [
                        'pattern' => '[arg]',
                        'replace' => ["return trans('Ecommerce::labels.product.variations');"]
                    ],
                    'policies' => ['variations'],
                    'data' => [],
                ],
                'sku_add' => [
                    'href_pattern' => [
                        'pattern' => '[arg]/sku/create',
                        'replace' => ['return $object->getOriginalShowURL();']
                    ],
                    'label_pattern' => [
                        'pattern' => '[arg]',
                        'replace' => ["return trans('Ecommerce::labels.product.variations_create');"]
                    ],
                    'policies' => ['variations'],
                    'data' => [],
                ]
            ],
            'ajaxSelectOptions' => [
                'label' => 'Product (Ecommerce)',
                'model_class' => \Corals\Modules\Ecommerce\Models\Product::class,
                'columns' => ['name'],
            ]
        ],
        'coupon' => [
            'presenter' => \Corals\Modules\Ecommerce\Transformers\CouponPresenter::class,
            'resource_url' => 'e-commerce/coupons',
        ],
        'shipping' => [
            'presenter' => \Corals\Modules\Ecommerce\Transformers\ShippingPresenter::class,
            'resource_url' => 'e-commerce/shippings',
        ],
        'order' => [
            'presenter' => \Corals\Modules\Ecommerce\Transformers\OrderPresenter::class,
            'resource_url' => 'e-commerce/orders',
            'statuses' => 'Ecommerce::status.order',
            'shipping_statuses' => 'Ecommerce::status.shipping',
            'payment_statuses' => 'Ecommerce::status.payment',
            'actions' => [
                'delete' => [],
                'update_payment' => [
                    'icon' => 'fa fa-fw fa-money',
                    'href_pattern' => [
                        'pattern' => '[arg]/edit-payment',
                        'replace' => ['return $object->getShowUrl();']
                    ],
                    'label_pattern' => [
                        'pattern' => '[arg]',
                        'replace' => ["return trans('Ecommerce::labels.order.update_payment');"]
                    ],
                    'policies' => ['update_payment'],
                    'data' => [
                        'action' => 'modal-load',
                        'title_pattern' => [
                            'pattern' => '[arg]',
                            'replace' => ["return trans('Ecommerce::labels.order.update_payment');"]
                        ],
                    ],
                ],
                'update_shipping' => [
                    'icon' => 'fa fa-fw fa-truck',
                    'href_pattern' => [
                        'pattern' => '[arg]/edit-shipping',
                        'replace' => ['return $object->getShowUrl();']
                    ],
                    'label_pattern' => [
                        'pattern' => '[arg]',
                        'replace' => ["return trans('Ecommerce::labels.order.update_shipping');"]
                    ],
                    'policies' => ['update_shipping'],
                    'data' => [
                        'action' => 'modal-load',
                        'title_pattern' => [
                            'pattern' => '[arg]',
                            'replace' => ["return trans('Ecommerce::labels.order.update_shipping');"]
                        ],
                    ],
                ],
                'update_status' => [
                    'icon' => 'fa fa-fw fa-flag',
                    'href_pattern' => [
                        'pattern' => '[arg]/edit-status',
                        'replace' => ['return $object->getShowUrl();']
                    ],
                    'label_pattern' => [
                        'pattern' => '[arg]',
                        'replace' => ["return trans('Ecommerce::labels.order.update_status');"]
                    ],
                    'policies' => ['update_status'],
                    'data' => [
                        'action' => 'modal-load',
                        'title_pattern' => [
                            'pattern' => '[arg]',
                            'replace' => ["return trans('Ecommerce::labels.order.update_status');"]
                        ],
                    ],
                ],
                'notify_buyer' => [
                    'icon' => 'fa fa-fw fa-bell',
                    'href_pattern' => ['pattern' => '[arg]/notify-buyer', 'replace' => ['return $object->getShowUrl();']],
                    'label_pattern' => ['pattern' => '[arg]', 'replace' => ["return trans('Ecommerce::labels.order.notify_buyer');"]],
                    'policies' => ['notify_buyer'],
                    'data' => [
                        'action' => 'post',
                        'confirmation_pattern' => ['pattern' => '[arg]', 'replace' => ["return trans('Ecommerce::messages.order.notify_buyer_confirmation');"]],
                    ],
                ],
                'refund_order' => [
                    'icon' => 'fa fa-undo',
                    'href_pattern' => ['pattern' => '[arg]/refund-order', 'replace' => ['return $object->getShowUrl();']],
                    'label_pattern' => ['pattern' => '[arg]', 'replace' => ["return trans('Ecommerce::labels.order.refund_order');"]],
                    'policies' => ['refundOrder'],
                    'data' => [
                        'action' => 'modal-load',
                        'title_pattern' => ['pattern' => '[arg]', 'replace' => ["return trans('Ecommerce::labels.order.refund_order');"]],
                    ],
                ],
                'pay_order' => [
                    'icon' => 'fa fa-fw fa-edit',
                    'href_pattern' => [
                        'pattern' => '[arg]',
                        'replace' => ['return url("e-commerce/checkout/?order=" . $object->hashed_id);']
                    ],
                    'label_pattern' => [
                        'pattern' => '[arg]',
                        'replace' => ["return trans('Ecommerce::labels.order.pay_order');"]
                    ],
                    'policies' => ['payOrder'],
                    'data' => [],
                ],
            ],
            'ajaxSelectOptions' => [
                'label' => 'Order (Ecommerce)',
                'model_class' => \Corals\Modules\Ecommerce\Models\Order::class,
                'columns' => ['order_number'],
            ]
        ],
        'shop' => [
            'sort_options' => 'Ecommerce::status.shop_order',
        ],
        'wishlist' => [
            'presenter' => \Corals\Modules\Ecommerce\Transformers\WishlistPresenter::class,
            'resource_url' => 'e-commerce/wishlist',
        ],
        'order_item' => [
            'presenter' => \Corals\Modules\Ecommerce\Transformers\OrderItemPresenter::class,
        ],
        'category' => [
            'presenter' => \Corals\Modules\Ecommerce\Transformers\CategoryPresenter::class,
            'resource_url' => 'e-commerce/categories',
            'default_image' => 'assets/corals/images/default_product_image.png',
            'translatable' => ['name', 'description']

        ],
        'tag' => [
            'presenter' => \Corals\Modules\Ecommerce\Transformers\TagPresenter::class,
            'resource_url' => 'e-commerce/tags',
        ],
        'brand' => [
            'presenter' => \Corals\Modules\Ecommerce\Transformers\BrandPresenter::class,
            'resource_url' => 'e-commerce/brands',
            'default_image' => 'assets/corals/images/default_product_image.png'
        ],
        'attribute' => [
            'presenter' => \Corals\Modules\Ecommerce\Transformers\AttributePresenter::class,
            'resource_url' => 'e-commerce/attributes',
        ],
        'sku' => [
            'presenter' => \Corals\Modules\Ecommerce\Transformers\SKUPresenter::class,
            'resource_route' => 'e-commerce.products.sku.index',
            'resource_relation' => 'product',
            'relation' => 'sku',
            'default_image' => 'assets/corals/images/default_product_image.png',
            'inventory_options' => [
                'finite' => 'Ecommerce::attributes.product.type_options.finite',
                'bucket' => 'Ecommerce::attributes.product.type_options.bucket',
                'infinite' => 'Ecommerce::attributes.product.type_options.infinite'
            ],
            'bucket' => [
                'in_stock' => 'Ecommerce::attributes.product.bucket_options.in_stock',
                'out_of_stock' => 'Ecommerce::attributes.product.bucket_options.out_of_stock',
                'limited' => 'Ecommerce::attributes.product.bucket_options.limited',
            ],
        ],
        'sku_property' => [],
    ],
    'settings' => [
        'Company' => [
            'owner' => [
                'label' => 'Ecommerce::labels.settings.company.owner',
                'type' => 'text',
                'required' => true,
                'settings_type' => 'TEXT',

            ],
            'name' => [
                'label' => 'Ecommerce::labels.settings.company.name',
                'type' => 'text',
                'required' => true,
                'settings_type' => 'TEXT',

            ],
            'street1' => [
                'label' => 'Ecommerce::labels.settings.company.street',
                'type' => 'text',
                'required' => true,
                'settings_type' => 'TEXT',

            ],
            'city' => [
                'label' => 'Ecommerce::labels.settings.company.city',
                'type' => 'text',
                'required' => true,
                'settings_type' => 'TEXT',

            ],
            'state' => [
                'label' => 'Ecommerce::labels.settings.company.state',
                'type' => 'text',
                'required' => true,
                'settings_type' => 'TEXT',

            ],
            'zip' => [
                'label' => 'Ecommerce::labels.settings.company.zip',
                'type' => 'text',
                'required' => true,
                'settings_type' => 'TEXT',

            ],
            'country' => [
                'label' => 'Ecommerce::labels.settings.company.country',
                'type' => 'text',
                'required' => true,
                'settings_type' => 'TEXT',

            ],
            'phone' => [
                'label' => 'Ecommerce::labels.settings.company.phone',
                'type' => 'text',
                'required' => true,
                'settings_type' => 'TEXT',

            ],
            'email' => [
                'label' => 'Ecommerce::labels.settings.company.email',
                'type' => 'text',
                'required' => true,
                'settings_type' => 'TEXT',

            ],
        ],
        'Shipping' => [
            'weight_unit' => [
                'label' => 'Ecommerce::labels.settings.shipping.weight_unit',
                'type' => 'select',
                'settings_type' => 'TEXT',

                'options' => [
                    'kg' => 'kg',
                    'g' => 'g',
                    'lb' => 'lbs',
                    'oz' => 'oz'
                ],
                'required' => true,
            ],
            'dimensions_unit' => [
                'label' => 'Ecommerce::labels.settings.shipping.dimensions_unit',
                'type' => 'select',
                'settings_type' => 'TEXT',

                'options' => [
                    'm' => 'm',
                    'cm' => 'cm',
                    'mm' => 'mm',
                    'in' => 'in',
                    'yd' => 'yd'
                ],
                'required' => true,
            ],
            'shippo_live_token' => [
                'label' => 'Ecommerce::labels.settings.shipping.shippo_live_token',
                'type' => 'text',
                'required' => true,
                'settings_type' => 'TEXT',
            ],
            'shippo_test_token' => [
                'label' => 'Ecommerce::labels.settings.shipping.shippo_test_token',
                'type' => 'text',
                'required' => true,
                'settings_type' => 'TEXT',
            ],
            'shippo_sandbox_mode' => [
                'label' => 'Ecommerce::labels.settings.shipping.shippo_sandbox_mode',
                'type' => 'boolean',
                'settings_type' => 'BOOLEAN',
            ],
        ],
        'Tax' => [
            'calculate_tax' => [
                'label' => 'Ecommerce::labels.settings.tax.calculate_tax',
                'type' => 'boolean',
                'required' => true,
                'settings_type' => 'BOOLEAN',
            ]
        ],
        'Rating' => [
            'enable' => [
                'label' => 'Ecommerce::labels.settings.rating.enable',
                'type' => 'boolean',
                'required' => true,
                'settings_type' => 'BOOLEAN',
            ]
        ],
        'Wishlist' => [
            'enable' => [
                'label' => 'Ecommerce::labels.settings.wishlist.enable',
                'type' => 'boolean',
                'required' => true,
                'settings_type' => 'BOOLEAN',
            ]
        ],
        'Appearance' => [
            'page_limit' => [
                'label' => 'Ecommerce::labels.settings.appearance.page_limit',
                'type' => 'number',
                'required' => false,
                'settings_type' => 'NUMBER',
            ],
            'enable_google_ecommerce_tracking' => [
                'label' => 'Ecommerce::labels.settings.appearance.enable_google_ecommerce_tracking',
                'type' => 'boolean',
                'required' => false,
                'settings_type' => 'BOOLEAN',
            ]
        ],
        'Search' => [
            'title_weight' => [
                'label' => 'Ecommerce::labels.settings.search.title_weight',
                'type' => 'number',
                'step' => 0.01,
                'required' => false,
                'settings_type' => 'NUMBER',
            ],
            'content_weight' => [
                'label' => 'Ecommerce::labels.settings.search.content_weight',
                'type' => 'number',
                'step' => 0.01,
                'settings_type' => 'NUMBER',
                'required' => false,
            ],
            'enable_wildcards' => [
                'label' => 'Ecommerce::labels.settings.search.enable_wildcards',
                'type' => 'boolean',
                'required' => true,
                'settings_type' => 'BOOLEAN',
            ]
        ],
        'AdditonalCharge' => [
            'additonal_charge_title' => [
                'label' => 'Ecommerce::labels.settings.additonal_charge.title',
                'type' => 'text',
                'required' => false,
                'settings_type' => 'TEXT',
            ],
            'additonal_charge_amount' => [
                'label' => 'Ecommerce::labels.settings.additonal_charge.amount',
                'type' => 'number',
                'step' => 0.01,
                'settings_type' => 'NUMBER',
                'required' => false,
            ],
            'additonal_charge_type' => [
                'label' => 'Ecommerce::labels.settings.additonal_charge.type',
                'type' => 'select',
                'options' => [
                    'fixed' => 'Fixed',
                    'percentage' => 'Percentage',
                ],
                'settings_type' => 'TEXT',
                'required' => false,
            ],
            'additonal_charge_gateways' => [
                'label' => 'Ecommerce::labels.settings.additonal_charge.gateways',
                'type' => 'text',
                'settings_type' => 'TEXT',
                'required' => false,
            ],
        ],
        'Checkout' => [
            'guest_checkout' => [
                'label' => 'Ecommerce::labels.settings.checkout.guest_checkout',
                'type' => 'boolean',
                'required' => false,
                'settings_type' => 'BOOLEAN',
            ]
        ]
    ],
];
