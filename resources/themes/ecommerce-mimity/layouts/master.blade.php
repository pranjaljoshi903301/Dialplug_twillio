<!DOCTYPE html>
<html lang="{{ \Language::getCode() }}" dir="{{ \Language::getDirection() }}">
<head>
    {!! \SEO::generate() !!}
    <meta charset="utf-8">
    <!-- Mobile Specific Meta Tag-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <link rel="shortcut icon" href="{{ \Settings::get('site_favicon') }}" type="image/png">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Fira+Sans">
    {!! Theme::css('css/bootstrap.css') !!}
    {!! Theme::css('css/perfect-scrollbar.min.css') !!}
    {!! Theme::css('css/font-awesome-4.7.0/css/font-awesome.min.css') !!}
    {!! Theme::css('css/swiper.min.css') !!}
    {!! Theme::css('plugins/photoswipe/photoswipe.min.css') !!}
    {!! Theme::css('plugins/photoswipe/photoswipe-default-skin/default-skin.min.css') !!}
    {!! Theme::css('css/style.css') !!}

    {!! Theme::css('css/nouislider.min.css') !!}
    {!! Theme::css('plugins/iziToast/css/iziToast.min.css') !!}
    {!! \Html::style('assets/corals/plugins/lightbox2/css/lightbox.min.css') !!}
    {!! \Html::style('assets/corals/plugins/icheck/skins/all.css') !!}
    {!! Theme::css('plugins/Ladda/ladda-themeless.min.css') !!}



<!-- Modernizr-->
    {!! Theme::js('js/modernizr.min.js') !!}

    <script type="text/javascript">
        window.base_url = '{!! url('/') !!}';
    </script>
    @yield('css')

    {!! \Assets::css() !!}

    {!! Theme::css('css/custom.css') !!}

    @if(\Language::isRTL())
        {!! Theme::css('css/custom-rtl.css') !!}
        {!! Theme::css('css/styles-rtl.css') !!}
    @endif

    @if(\Settings::get('google_tag_manager_id'))

    <!-- Google Tag Manager -->
        <script>(function (w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start': new Date().getTime(), event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', '{{ \Settings::get('google_tag_manager_id') }}');</script>
        <!-- End Google Tag Manager -->

    @elseif(\Settings::get('google_analytics_id'))

    <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async
                src="https://www.googletagmanager.com/gtag/js?id={{ \Settings::get('google_analytics_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());

            gtag('config', "{{ \Settings::get('google_analytics_id') }}");
        </script>
    @endif
    <style type="text/css">
        {!! \Settings::get('custom_css', '') !!}
    </style>
</head>

<body>
@php \Actions::do_action('after_body_open') @endphp

@if(\Settings::get('google_tag_manager_id'))
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id={{ \Settings::get('google_tag_manager_id') }}"
                height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->

@endif
@include('partials.header')
<div class="container-fluid" id="main-container">
    <div class="row">
        <div class="col" id="main-sidebar">
            @include('partials.sidebar')
        </div>
        <div class="col" id="main-content">
            <div id="wrapper">
                @yield('editable_content')
            </div>
            @include('partials.footer')
            <div class="modal fade fade-custom modal-cart" id="cartModal" tabindex="-1" role="dialog"
                 aria-labelledby="cartModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" >
                        <div class="cart_summary">
                            @include('partials.cart_summary')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(\Settings::get('google_tag_manager_id') && Settings::get('ecommerce_enable_google_ecommerce_tracking', true))
    @include('Ecommerce::analytics.analytics_script')
@endif

@yield('after_content')


<!-- JavaScript (jQuery) libraries, plugins and custom scripts-->
{!! Theme::js('js/jquery.min.js') !!}
{!! Theme::js('js/bootstrap.bundle.min.js') !!}
{!! Theme::js('js/perfect-scrollbar.min.js') !!}
{!! Theme::js('js/swiper.min.js') !!}
{!! Theme::js('js/jquery.raty-fa.min.js') !!}
{!! Theme::js('plugins/photoswipe/photoswipe.min.js') !!}
{!! Theme::js('plugins/photoswipe/photoswipe-ui-default.min.js') !!}
{!! Theme::js('js/script.js') !!}
{!! Theme::js('js/nouislider.min.js') !!}
{!! Theme::js('js/scripts.min.js') !!}
{!! Theme::js('js/custom.js') !!}

{!! Theme::js('plugins/iziToast/js/iziToast.min.js') !!}
{!! \Html::script('assets/corals/plugins/lightbox2/js/lightbox.min.js') !!}
{!! \Html::script('assets/corals/plugins/icheck/icheck.js') !!}
<!-- Ladda -->
{!! Theme::js('plugins/Ladda/spin.min.js') !!}
{!! Theme::js('plugins/Ladda/ladda.min.js') !!}
<!-- Jquery BlockUI -->
{!! Theme::js('plugins/jquery-block-ui/jquery.blockUI.min.js') !!}

{!! Theme::js('js/functions.js') !!}
{!! Theme::js('js/main.js') !!}
{!! \Html::script('assets/corals/js/corals_functions.js') !!}
{!! \Html::script('assets/corals/js/corals_main.js') !!}

@include('Ecommerce::cart.cart_script')

{!! Assets::js() !!}

@php  \Actions::do_action('footer_js') @endphp

@yield('js')

<script type="text/javascript">
    {!! \Settings::get('custom_js', '') !!}
</script>

@include('components.modal',['id'=>'global-modal'])

</body>
</html>