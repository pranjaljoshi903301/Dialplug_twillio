<?php

//Bar
Breadcrumbs::register('twillio_bars', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(trans('Twillio::module.bar.title'), url(config('twillio.models.bar.resource_url')));
});

Breadcrumbs::register('twillio_bar_create_edit', function ($breadcrumbs) {
    $breadcrumbs->parent('twillio_bars');
    $breadcrumbs->push(view()->shared('title_singular'));
});

Breadcrumbs::register('twillio_bar_show', function ($breadcrumbs) {
    $breadcrumbs->parent('twillio_bars');
    $breadcrumbs->push(view()->shared('title_singular'));
});