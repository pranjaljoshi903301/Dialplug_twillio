{!! Theme::js('js/jquery-3.3.1.min.js') !!}
{!! Theme::js('js/bootstrap.bundle.min.js') !!}
{!! Theme::js('js/jquery.magnific-popup.min.js') !!}
{!! Theme::js('js/side-menu.js') !!}
{!! Theme::js('js/jquery.xmalert.min.js') !!}
{!! Theme::js('js/dashboard-header.js') !!}
{!! Theme::js('js/alerts-generator.js') !!}
<!-- Ladda -->
{!! Theme::js('plugins/Ladda/spin.min.js') !!}
{!! Theme::js('plugins/Ladda/ladda.min.js') !!}
<!-- toastr -->
{!! Theme::js('plugins/toastr/toastr.min.js') !!}
<!-- SlimScroll -->
{!! Theme::js('plugins/sweetalert2/dist/sweetalert2.all.min.js') !!}
{!! Theme::js('plugins/select2/dist/js/select2.full.min.js') !!}
{!! Assets::js() !!}

{!! Theme::js('js/functions.js') !!}
{!! Theme::js('js/main.js') !!}
<!-- corals js -->
{!! Theme::js('plugins/lodash/lodash.js') !!}
{!! \Html::script('assets/corals/plugins/lightbox2/js/lightbox.min.js') !!}
{!! \Html::script('assets/corals/plugins/clipboard/clipboard.min.js') !!}
{!! \Html::script('assets/corals/js/corals_functions.js') !!}
{!! \Html::script('assets/corals/js/corals_main.js') !!}
@include('Corals::corals_main')

@yield('js')

@php  \Actions::do_action('admin_footer_js') @endphp

@include('partials.notifications')