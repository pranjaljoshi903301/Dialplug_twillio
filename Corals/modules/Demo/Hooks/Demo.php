<?php

namespace Corals\Modules\Demo\Hooks;

use Corals\Modules\Directory\Models\Listing;
use Corals\Modules\Marketplace\Models\Product;
use Corals\Modules\Subscriptions\Models\Subscription;
use Corals\Settings\Models\Setting;
use Corals\User\Models\User;

class Demo
{
    /**
     * FooBar constructor.
     */
    function __construct()
    {
    }

    /**
     * @param $object
     * @return bool
     * @throws \Exception
     */
    public function disable_model_update($object)
    {

        if (app()->runningInConsole()) {
            return true;
        }

        if (!user()) {
            return true;
        }
        if (user()) {
            if (isset($object->created_by) && ($object->created_by == user()->id)) {
                return true;
            }
        }

        $object_class = get_class($object);
        if ($object_class == Subscription::class || $object_class == Product::class) {
            return true;
        }


        if (($object_class == User::class) && (user()->id != 1)) {
            return true;
        }
        if (($object_class == Listing::class)) {
            return true;

        }

        if (($object_class == Setting::class) && (($object->code == "payment_paypal_rest_sandbox_access_token") || ($object->code == "payment_paypal_rest_sandbox_access_token_expiry"))) {
            return true;

        }
        throw_demo_exception();

    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function disable()
    {
        if (app()->runningInConsole()) {
            return true;
        }

        if (user() && (user()->id == 1)) {
            return true;
        }

        if (!app()->runningInConsole()) {

            throw_demo_exception();
        }
    }

    public function show_demo_mode()
    {
        echo '<li id="demo_mode_nav">
        <a href="#" style="pointer-events: none; cursor: default;">
        <i class="fa fa-eye"></i> DEMO MODE
        </a>
        </li>';
    }

    public function add_live_chat()
    {
        echo '<script type="text/javascript">
                    function add_chatinline() {
                        var hccid = 29741647;
                        var nt = document.createElement("script");
                        nt.async = true;
                        nt.src = "https://mylivechat.com/chatinline.aspx?hccid=" + hccid;
                        var ct = document.getElementsByTagName("script")[0];
                        ct.parentNode.insertBefore(nt, ct);
                    }
        
                    add_chatinline();
               </script>';
    }

    public function disable_elfinder_root_write($data)
    {
        $data ['defaults'] = array('read' => true, 'write' => false);
        return $data;
    }

    public function show_demo_home($item, $home)
    {
        if ($home) {
            echo '<div style="text-align: center;padding: 8px 30px;z-index: 999999;;background-color: #3fadfc  !important;color: #FFF;">
            You\'re currently viewing the the Demo frontend Theme of ' . config('app.name') . ',
            click <a href="' . url('login') . '" style="color: #2f3b59;font-weight: 700;;">here</a> to access user area
            dashboards
            , to View Our Official Website click <a target="_blank" href="https://www.laraship.com/"
                                                    style="color: #2f3b59;font-weight: 700;">here</a>
        </div>';
        }
    }

    public function show_demo_preview()
    {
        $demos = [

            ['key' => 'demo', 'name' => ' Subscription Demo 1', 'product' => 'Laraship Subscription', 'url' => 'https://demo.laraship.com', 'buy_url' => 'https://www.laraship.com/product/laraship-subscriptions/'],
            ['key' => 'braintree', 'name' => ' Subscription Demo 2', 'product' => 'Laraship Subscription', 'url' => 'https://braintree.laraship.com', 'buy_url' => 'https://www.laraship.com/product/laraship-braintree-subscriptions/'],

            ['key' => 'ecommerce', 'name' => ' eCommerce Demo 1', 'product' => 'Laraship eCommerce', 'url' => 'https://ecommerce.laraship.com', 'buy_url' => 'https://www.laraship.com/product/laraship-laravel-ecommerce-shopping-cart/'],
            ['key' => 'ecommerce2', 'name' => ' eCommerce Demo 2', 'product' => 'Laraship eCommerce', 'url' => 'https://ecommerce2.laraship.com', 'buy_url' => 'https://www.laraship.com/product/laraship-laravel-ecommerce-shopping-cart/'],
            ['key' => 'ecommerce3', 'name' => ' eCommerce Demo 3', 'product' => 'Laraship eCommerce', 'url' => 'https://ecommerce3.laraship.com', 'buy_url' => 'https://www.laraship.com/product/laraship-laravel-ecommerce-shopping-cart/'],
            ['key' => 'amazon', 'name' => ' eCommerce Demo 4', 'product' => 'Laraship eCommerce', 'url' => 'https://amazon.laraship.com', 'buy_url' => 'https://www.laraship.com/product/laraship-laravel-ecommerce-shopping-cart/'],

            ['key' => 'marketplace', 'name' => ' Marketplace Demo 1', 'product' => 'Laraship Marketplace', 'url' => 'https://marketplace.laraship.com', 'buy_url' => 'https://www.laraship.com/product/laraship-laravel-marketplace/'],
            ['key' => 'marketplace2', 'name' => ' Marketplace Demo 2', 'product' => 'Laraship Marketplace', 'url' => 'https://marketplace2.laraship.com', 'buy_url' => 'https://www.laraship.com/product/laraship-laravel-marketplace/'],
            ['key' => 'marketplace3', 'name' => ' Marketplace Demo 3', 'product' => 'Laraship Marketplace', 'url' => 'https://marketplace3.laraship.com', 'buy_url' => 'https://www.laraship.com/product/laraship-laravel-marketplace/'],
            ['key' => 'marketplace4', 'name' => ' Marketplace Demo 4', 'product' => 'Laraship Marketplace', 'url' => 'https://marketplace4.laraship.com', 'buy_url' => 'https://www.laraship.com/product/laraship-laravel-marketplace/'],

            ['key' => 'elite', 'name' => ' Elite Demo ', 'product' => 'Laraship Elite', 'url' => 'https://elite.laraship.com', 'buy_url' => 'https://www.laraship.com/product/laraship-elite-platform/'],

            ['key' => 'directory', 'name' => ' Directory Demo 1', 'product' => 'Laraship Directory', 'url' => 'https://directory.laraship.com', 'buy_url' => 'https://www.laraship.com/product/laraship-laravel-directory/'],
            ['key' => 'directory2', 'name' => ' Directory Demo 2', 'product' => 'Laraship Directory', 'url' => 'https://directory2.laraship.com', 'buy_url' => 'https://www.laraship.com/product/laraship-laravel-directory/'],

            ['key' => 'classifieds', 'name' => ' Classified Demo 1', 'product' => 'Laraship Classified', 'url' => 'https://classifieds.laraship.com', 'buy_url' => 'https://www.laraship.com/product/laraship-laravel-classified/'],
            ['key' => 'classified2', 'name' => ' Classified Demo 2', 'product' => 'Laraship Classified', 'url' => 'https://classified2.laraship.com', 'buy_url' => 'https://www.laraship.com/product/laraship-laravel-classified/'],

            ['key' => 'formbuilder', 'name' => ' Form Builder', 'product' => 'Laraship Form Builder', 'url' => 'https://formbuilder.laraship.com', 'buy_url' => 'https://www.laraship.com/product/laraship-form-builder-cms/'],


        ];

        $selected_demo = [];
        foreach ($demos as $demo) {
            if ($demo['url'] == config('app.url')) {
                $selected_demo = $demo;
            }
        }
        $preview = view('Demo::demos.preview_frame')->with(compact('demos', 'selected_demo'))->render();
        echo $preview;
    }


    public function show_demo_logins()
    {

        echo '    <div class="text-center" id="demo-logins"><button type="button" class="button dark btn btn-sm btn-danger" id="admin_login">Login as Admin</button>';
        if (\Modules::isModuleActive('corals-subscriptions') || \Modules::isModuleActive('corals-ecommerce') || \Modules::isModuleActive('corals-classified') || \Modules::isModuleActive('corals-marketplace')) {
            echo '<strong>&nbsp;&nbsp;OR&nbsp;&nbsp;</strong>
            <a type="button" class="button primary btn btn-sm btn-warning" href="' . route('register') . '">Create Account</a>';
        }
        echo '<hr/></div>';

    }

    public function demo_login_js()
    {
        echo '    <script type="text/javascript">
        $(document).ready(function () {
            $("#admin_login").on("click", function(e) {
                e.preventDefault();
                $("#email").val("superuser@laraship.com");
                $("#password").val("123456");
                $("#login-form").submit();
                $("#login-box").submit();
                $("#customer-form").submit();
                
                
            })
            $("#member_login").on("click", function(e) {
                e.preventDefault();
                $("#email").val("member@corals.io");
                $("#password").val("123456");
                $("#login-form").submit();
                $("#login-box").submit();
                $("#customer-form").submit();

            })
        });
    </script>';
    }

    public function demo_product_pricing_message()
    {
        echo ' <p class="btn bg-green text-center m-b-10" style="width: 100%;"><b>This is a sample product, once
                            you select a plan you will be redirected to checkout using a test credit card</p></p>';
    }

    public function demo_checkout_message($plan, $gateway)
    {
        echo '<p class=" bg-green text-center m-b-40 p-10 px-3 py-3" style="width: 100%;"><b>Laraship supports many payment gateways</p>';
    }

    public function demo_paypal_account($gateway)
    {
        echo '            <p class="bg-blue m-b-40 p-10 px-3 py-3" style="    width: 100%;">Use this PayPal account for testing<br>
                User: <b>larashippro@gmail.com</b><br>
                Password:&nbsp;&nbsp;&nbsp;<b>larashippro</b><br>';
    }

    public function demo_card_account($gateway)
    {
        echo '            <p class="bg-blue m-b-40 p-10" style="    width: 100%;">Use this credit card for testing<br>
                Number: <b>4242424242424242</b><br>
                EXP:&nbsp;&nbsp;&nbsp;<b>02/22</b><br>
                CCV:&nbsp;&nbsp;&nbsp;<b>111</b><br>
                ZIP:&nbsp;&nbsp;&nbsp;<b>12345</b></p>';
    }

    public function show_shop_banner()
    {
        echo \Shortcode::compile('zone', 'shop');

    }

    public function show_featured_category_banner()
    {
        //echo  \Shortcode::compile( 'zone','featured-category' ) ;

    }

    public function show_filter_banner()
    {
        echo \Shortcode::compile('zone', 'filter');

    }

    public function show_footer_banner()
    {
        echo \Shortcode::compile('zone', 'footer');

    }

    public function mailchimp_subscribe($user)
    {
        try {

            $mc = new \NZTim\Mailchimp\Mailchimp('920946cbb4e1a16e643071a062812ad2-us17');

            // Adds/updates an existing subscriber:
            $mc->subscribe('fe7e1f0e80', $user->email, $merge = ['FNAME' => $user->full_name], $confirm = false);
            // Use $confirm = false to skip double-opt-in if you already have permission.
            // This method will update an existing subscriber and will not ask an existing subscriber to re-confirm.


        } catch (\Exception $e) {

            //throw new \Exception($e->getMessage());
        }
    }


}
