<?php

return [
    'models' => [
        'listing' => [
            'presenter' => \Corals\Modules\Directory\Transformers\ListingPresenter::class,
            'resource_url' => 'directory/listings',
            'sort_options' => 'Directory::status.sort_options',
            'default_image' => 'assets/corals/images/default_listing_image.png',
            'user_resource_url' => 'directory/user/listings',
            'review_resource_url' => 'directory/user/reviews',
        ],
        'claim' => [
            'presenter' => \Corals\Modules\Directory\Transformers\ClaimPresenter::class,
            'resource_url' => 'directory/claims',
            'actions' => [
                'edit' => [],
                'view' => [
                    'icon' => 'fa fa-fw fa-eye',
                    'href_pattern' => ['pattern' => '[arg]', 'replace' => ['return $object->getShowUrl();']],
                    'label_pattern' => ['pattern' => '[arg]', 'replace' => ["return trans('Directory::attributes.claim.view');"]],
                    'data' => [
                        'action' => 'modal-load',
                        'title_pattern' => ['pattern' => '[arg]', 'replace' => ["return trans('Directory::attributes.claim.listing_claim');"]],
                    ],
                ],
                'declined' => [
                    'icon' => 'fa fa-fw fa-remove',
                    'href_pattern' => ['pattern' => '[arg]/reasons', 'replace' => ['return $object->getShowUrl();']],
                    'label_pattern' => ['pattern' => '[arg]', 'replace' => ["return trans('Directory::attributes.claim.status_options.declined');"]],
                    'policies' => ['updateStatus'],
                    'policies_args' => 'declined',
                    'data' => [
                        'action' => 'modal-load',
                        'title_pattern' => ['pattern' => '[arg]', 'replace' => ["return trans('Directory::attributes.claim.status_options.declined');"]],
                    ],
                ],
                'approved' => [
                    'icon' => 'fa fa-fw fa-check',
                    'href_pattern' => ['pattern' => '[arg]/approved', 'replace' => ['return $object->getShowUrl();']],
                    'label_pattern' => ['pattern' => '[arg]', 'replace' => ["return trans('Directory::attributes.claim.status_options.approved');"]],
                    'policies' => ['updateStatus'],
                    'policies_args' => 'approved',
                    'data' => [
                        'action' => "post",
                        'table' => "#ClaimsDataTable"
                    ],
                ],
                'pending' => [
                    'icon' => 'fa fa-clock-o',
                    'href_pattern' => ['pattern' => '[arg]/pending', 'replace' => ['return $object->getShowUrl();']],
                    'label_pattern' => ['pattern' => '[arg]', 'replace' => ["return trans('Directory::attributes.claim.status_options.pending');"]],
                    'policies' => ['updateStatus'],
                    'policies_args' => 'pending',
                    'data' => [
                        'action' => "post",
                        'table' => "#ClaimsDataTable"
                    ],
                ]
            ]
        ],
        'wishlist' => [
            'resource_url' => 'directory/wishlist',
        ],
        'invite_friends' => [
            'resource_url' => 'directory/user/invite-friends',
        ]
    ]
];
