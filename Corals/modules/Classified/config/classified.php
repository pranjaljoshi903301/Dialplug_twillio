<?php

return [
    'models' => [
        'product' => [
            'presenter' => \Corals\Modules\Classified\Transformers\ProductPresenter::class,
            'resource_url' => 'classified/products',
            'user_resource_url' => 'classified/user/products',
            'sort_options' => [
                'low_high_price' => 'Classified::labels.product.sort_options.low_high_price',
                'high_low_price' => 'Classified::labels.product.sort_options.high_low_price',
                'a_z_order' => 'Classified::labels.product.sort_options.a_z_order',
                'z_a_order' => 'Classified::labels.product.sort_options.z_a_order'
            ],
            'status_options' => [
                'active' => 'Classified::attributes.product.status_options.active',
                'inactive' => 'Classified::attributes.product.status_options.inactive',
                'sold' => 'Classified::attributes.product.status_options.sold',
                'archived' => 'Classified::attributes.product.status_options.archived'
            ],
            'actions' => [
                'delete' => [
                    'href_pattern' => ['pattern' => '[arg]', 'replace' => ['return $object->getShowUrl();']],
                    'label_pattern' => ['pattern' => '[arg]', 'replace' => ["return trans('Corals::labels.delete');"]],
                    'data' => [
                        'action' => 'delete',
                        'page_action' => 'site_reload'
                    ]
                ]

            ],
            'ajaxSelectOptions' => [
                'label' => 'Product (Classified)',
                'model_class' => \Corals\Modules\Classified\Models\Product::class,
                'columns' => ['name'],
            ]
        ],
        'wishlist' => [
            'resource_url' => 'classified/wishlist'
        ],
        'product_option' => [
        ]
    ]
];
