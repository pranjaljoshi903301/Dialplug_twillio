<?php

return [
    'models' => [
        'bitrixtelephony' => [
            'presenter' => \Corals\Modules\BT\Transformers\BitrixTelephonyPresenter::class,
            'resource_url' => 'bt_config',
        ],
        'user' => [
            'presenter' => \Corals\Modules\BT\Transformers\BitrixTelephonyPresenter::class,
            'resource_url' => 'bt_users',
        ],
        'dashboard' => [
            'presenter' => \Corals\Modules\BT\Transformers\BitrixTelephonyPresenter::class,
            'resource_url' => 'dashboard',
        ],
    ]
];