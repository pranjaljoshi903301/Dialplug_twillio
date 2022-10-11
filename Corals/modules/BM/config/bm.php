<?php

return [
    'models' => [
        'bitrixmobile' => [
            'presenter' => \Corals\Modules\BM\Transformers\BitrixMobilePresenter::class,
            'resource_url' => 'bm_config',
        ],
        'dashboard' => [
            'presenter' => \Corals\Modules\Foo\Transformers\BitrixMobilePresenter::class,
            'resource_url' => 'dashboard',
        ],
    ]
];