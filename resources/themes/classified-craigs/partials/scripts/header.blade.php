<meta charset="utf-8">
<!-- Mobile Specific Meta Tag-->
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<link rel="shortcut icon" href="{{ \Settings::get('site_favicon') }}" type="image/png">
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Varela+Round" rel="stylesheet">


{!! Theme::css('bootstrap/css/bootstrap.css') !!}
<!-- Vendor Styles including: Bootstrap, Font Icons, Plugins, etc.-->
{!! Theme::css('fonts/font-awesome.css') !!}
<!-- Main Template Styles-->
{!! Theme::css('plugins/select2/dist/css/select2.min.css') !!}

{!! Theme::css('plugins/Ladda/ladda-themeless.min.css') !!}

{!! Theme::css('css/owl.carousel.min.css') !!}
<!-- toastr -->
{!! Theme::css('plugins/sweetalert2/dist/sweetalert2.css') !!}

{!! Theme::css('plugins/toastr/toastr.min.css') !!}

{!! Theme::css('css/selectize.css') !!}

{!! Theme::css('css/style.css') !!}

{!! Theme::css('css/user.css') !!}

{!! Theme::css('css/custom.css') !!}



<script type="text/javascript">
    window.base_url = '{!! url('/') !!}';
</script>

@yield('css')

{!! \Assets::css() !!}

@if(\Settings::get('google_analytics_id'))
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