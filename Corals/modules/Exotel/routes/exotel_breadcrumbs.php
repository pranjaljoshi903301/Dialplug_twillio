<?php

//Bar
Breadcrumbs::register('exotel_bars', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(trans('Exotel::module.bar.title'), url(config('exotel.models.bar.resource_url')));
});

Breadcrumbs::register('exotel_bar_create_edit', function ($breadcrumbs) {
    $breadcrumbs->parent('exotel_bars');
    $breadcrumbs->push(view()->shared('title_singular'));
});

Breadcrumbs::register('exotel_bar_show', function ($breadcrumbs) {
    $breadcrumbs->parent('exotel_bars');
    $breadcrumbs->push(view()->shared('title_singular'));
});