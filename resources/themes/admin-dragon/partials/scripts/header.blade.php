{!! Theme::css('css/simple-line-icons.css') !!}
{!! Theme::css('css/magnific-popup.css') !!}
{!! Theme::css('css/bootstrap.css') !!}

{!! Theme::css('font-awesome-4.7.0/css/font-awesome.min.css') !!}
<!-- animate.css -->
{!! Theme::css('plugins/select2/dist/css/select2.min.css') !!}

<!-- Ladda  -->
{!! Theme::css('plugins/Ladda/ladda-themeless.min.css') !!}
<!-- toastr -->
{!! Theme::css('plugins/toastr/toastr.min.css') !!}
<!-- sweetalert2 -->
{!! Theme::css('plugins/sweetalert2/dist/sweetalert2.css') !!}
{!! \Html::style('assets/corals/plugins/lightbox2/css/lightbox.min.css') !!}

@yield('css')

{!! Theme::css('css/style.css') !!}

{!! Theme::css('css/custom.css') !!}

{!! \Assets::css() !!}
<script type="text/javascript">
    window.base_url = '{!! url('/') !!}';
</script>

{!! \Html::script('assets/corals/js/corals_header.js') !!}

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