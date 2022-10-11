<?php

//BitrixMobile
Breadcrumbs::register('bm_bitrixmobile', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(trans('BM::module.bitrixmobile.title'), url(config('bm.models.bitrixmobile.resource_url')));
});

Breadcrumbs::register('bm_bitrixmobile_create_edit', function ($breadcrumbs) {
    $breadcrumbs->parent('bm_bitrixmobile');
    $breadcrumbs->push(view()->shared('title_singular'));
});

Breadcrumbs::register('bm_bitrixmobile_show', function ($breadcrumbs) {
    $breadcrumbs->parent('bm_bitrixmobile');
    $breadcrumbs->push(view()->shared('title_singular'));
});

//Users
Breadcrumbs::register('bm_users', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(trans('BM::module.user.title'), url(config('bm.models.user.resource_url')));
});

Breadcrumbs::register('bm_user_create_edit', function ($breadcrumbs) {
    $breadcrumbs->parent('bm_bitrixmobile');
    $breadcrumbs->push(view()->shared('title_singular'));
});

//Lines
Breadcrumbs::register('bm_lines', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(trans('BM::module.line.title'), url(config('bm.models.line.resource_url')));
});

Breadcrumbs::register('bm_line_create_edit', function ($breadcrumbs) {
    $breadcrumbs->parent('bm_bitrixmobile');
    $breadcrumbs->push(view()->shared('title_singular'));
});
