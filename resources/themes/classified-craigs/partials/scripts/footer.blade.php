<!-- JavaScript (jQuery) libraries, plugins and custom scripts-->
{!! Theme::js('js/jquery-3.3.1.min.js') !!}

{!! Theme::js('js/popper.min.js') !!}

{!! Theme::js('bootstrap/js/bootstrap.min.js') !!}

{!! \Html::script('https://maps.googleapis.com/maps/api/js?key='.\Settings::get('utility_google_address_api_key','').'&libraries=places') !!}

{!! Theme::js('js/selectize.min.js') !!}

<script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

{!! Theme::js('plugins/Lightbox/js/lightbox.js') !!}

{!! \Html::script('assets/corals/plugins/icheck/icheck.js') !!}

{!! Theme::js('plugins/select2/dist/js/select2.full.min.js') !!}

{!! Theme::js('js/masonry.pkgd.min.js') !!}

{!! Theme::js('js/icheck.min.js') !!}

{!! Theme::js('js/jquery.validate.min.js') !!}

{!! Theme::js('js/custom.js') !!}

{!! Theme::js('js/owl.carousel.min.js') !!}

{!! Theme::js('plugins/toastr/toastr.min.js') !!}

{!! Theme::js('plugins/sweetalert2/dist/sweetalert2.all.min.js') !!}

{!! Theme::js('js/classified_craigs_functions.js') !!}

{!! Theme::js('js/classified_craigs_main.js') !!}

{!! Theme::js('plugins/Ladda/spin.min.js') !!}

{!! Theme::js('plugins/Ladda/ladda.min.js') !!}

{!! \Html::script('assets/corals/js/corals_functions.js') !!}

{!! \Html::script('assets/corals/js/corals_main.js') !!}


{!! Assets::js() !!}

@php  \Actions::do_action('footer_js') @endphp
@include('Corals::corals_main')


@yield('js')

<script type="text/javascript">
    {!! \Settings::get('custom_js', '') !!}
</script>

@include('partials.notifications')