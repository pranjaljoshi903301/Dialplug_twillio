<?php

namespace Corals\Modules\Demo;

use Corals\Modules\Demo\Providers\DemoAuthServiceProvider;
use Corals\Modules\Demo\Providers\DemoObserverServiceProvider;
use Corals\Modules\Demo\Providers\DemoRouteServiceProvider;
use Corals\Modules\Demo\Hooks\Demo;

use Illuminate\Support\ServiceProvider;

class DemoServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Load view
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'Demo');

        // Load translation
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'Demo');

        // Load migrations
//        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        if (is_demo_mode()) {
            \Actions::add_action('pre_update', [Demo::class, 'disable_model_update'], 10);
            \Actions::add_action('pre_delete', [Demo::class, 'disable_model_update'], 10);
            \Actions::add_action('pre_update_module', [Demo::class, 'disable'], 10);
            \Actions::add_action('pre_download_module', [Demo::class, 'disable'], 10);
            \Actions::add_action('pre_download_new_module', [Demo::class, 'disable'], 10);
            \Actions::add_action('pre_uninstall_module', [Demo::class, 'disable'], 10);
            \Actions::add_action('pre_install_module', [Demo::class, 'disable'], 10);
            \Actions::add_action('show_navbar', [Demo::class, 'show_demo_mode'], 10);
            \Actions::add_action('pre_update_menu_tree', [Demo::class, 'disable'], 10);
            \Actions::add_action('pre_update_gallery', [Demo::class, 'disable'], 10);
            \Actions::add_action('pre_delete_file', [Demo::class, 'disable'], 10);
            \Actions::add_action('pre_save_file', [Demo::class, 'disable'], 10);
            \Actions::add_action('admin_footer_js', [Demo::class, 'add_live_chat'], 11);
            \Actions::add_action('footer_js', [Demo::class, 'add_live_chat'], 11);
            \Actions::add_action('post_seed_subscriptions', [Demo::class, 'add_demo_data'], 10);
            //\Actions::add_action('pre_content', [Demo::class, 'show_demo_home'], 10);
            \Actions::add_action('after_body_open', [Demo::class, 'show_demo_preview'], 10);
            \Actions::add_action('pre_login_form', [Demo::class, 'show_demo_logins'], 10);
            \Actions::add_action('admin_footer_js', [Demo::class, 'demo_login_js'], 10);
            \Actions::add_action('pre_pricing_table', [Demo::class, 'demo_product_pricing_message'], 10);
            \Actions::add_action('pre_checkout_form', [Demo::class, 'demo_checkout_message'], 10);
            \Actions::add_action('pre_paypal_checkout_form', [Demo::class, 'demo_paypal_account'], 10);
            \Actions::add_action('pre_stripe_checkout_form', [Demo::class, 'demo_card_account'], 10);
            \Actions::add_action('pre_braintree_checkout_form', [Demo::class, 'demo_paypal_account'], 10);
            \Actions::add_action('pre_braintree_checkout_form', [Demo::class, 'demo_card_account'], 9);
            \Actions::add_action('pre_install_theme', [Demo::class, 'disable'], 110);
            \Actions::add_action('pre_uninstall_theme', [Demo::class, 'disable'], 110);
            \Actions::add_action('pre_activate_theme', [Demo::class, 'disable'], 110);
            \Actions::add_action('pre_deactivate_theme', [Demo::class, 'disable'], 110);
            \Actions::add_action('pre_import_theme_demo', [Demo::class, 'disable'], 110);
            //\Actions::add_action('post_cancel_subscription', [Demo::class, 'disable'], 110);

            \Filters::add_filter('el_finder_root', [Demo::class, 'disable_elfinder_root_write'], 110);

            //\Actions::add_action('pre_display_shop', [Demo::class, 'show_shop_banner'], 10);
            \Actions::add_action('pre_display_ecommerce_featured_categories', [Demo::class, 'show_featured_category_banner'], 10);
            \Actions::add_action('post_display_ecommerce_filter', [Demo::class, 'show_filter_banner'], 10);
            \Actions::add_action('pre_display_footer', [Demo::class, 'show_footer_banner'], 10);
            \Actions::add_action('social_registration', [Demo::class, 'mailchimp_subscribe'], 10);



        }

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/demo.php', 'demo');

        $this->app->register(DemoRouteServiceProvider::class);
        $this->app->register(DemoAuthServiceProvider::class);
        $this->app->register(DemoObserverServiceProvider::class);
    }
}
