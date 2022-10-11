<?php

// Demo
Breadcrumbs::register('demos', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('demos', url(config('demo.models.demo.resource_url')));
});

Breadcrumbs::register('demo_create_edit', function ($breadcrumbs) {
    $breadcrumbs->parent('demos');
    $breadcrumbs->push(view()->shared('title_singular'));
});

Breadcrumbs::register('demo_show', function ($breadcrumbs) {
    $breadcrumbs->parent('demos');
    $breadcrumbs->push(view()->shared('title_singular'));
});