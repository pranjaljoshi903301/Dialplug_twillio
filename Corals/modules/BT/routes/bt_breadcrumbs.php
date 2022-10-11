<?php

//BitrixTelephony
Breadcrumbs::register('bt_bitrixtelephony', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(trans('BT::module.bitrixtelephony.title'), url(config('bt.models.bitrixtelephony.resource_url')));
});

Breadcrumbs::register('bt_bitrixtelephony_create_edit', function ($breadcrumbs) {
    $breadcrumbs->parent('bt_bitrixtelephony');
    $breadcrumbs->push(view()->shared('title_singular'));
});

Breadcrumbs::register('bt_bitrixtelephony_show', function ($breadcrumbs) {
    $breadcrumbs->parent('bt_bitrixtelephony');
    $breadcrumbs->push(view()->shared('title_singular'));
});

//Users
Breadcrumbs::register('bt_users', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(trans('BT::module.user.title'), url(config('bt.models.user.resource_url')));
});

Breadcrumbs::register('bt_user_create_edit', function ($breadcrumbs) {
    $breadcrumbs->parent('bt_bitrixtelephony');
    $breadcrumbs->push(view()->shared('title_singular'));
});
