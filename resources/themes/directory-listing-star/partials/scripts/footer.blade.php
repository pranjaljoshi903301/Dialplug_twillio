<!-- JavaScript (jQuery) libraries, plugins and custom scripts-->
{{--{!! Theme::js('js/infobox.min.js') !!}--}}
{!! Theme::js('js/vendor/jquery-library.js') !!}

{!! Theme::js('js/vendor/bootstrap.min.js') !!}

{!! \Html::script('https://maps.googleapis.com/maps/api/js?key='.\Settings::get('utility_google_address_api_key','').'&libraries=places') !!}

{!! Theme::js('js/mapclustering/markerclusterer.min.js') !!}

{!! Theme::js('js/mapclustering/infobox.js') !!}

{!! Theme::js('js/mapclustering/map.js') !!}

{!! Theme::js('js/ResizeSensor.js.js') !!}

{!! Theme::js('js/jquery.sticky-sidebar.js') !!}

{!! Theme::js('js/jquery.navhideshow.js') !!}

{!! Theme::js('js/backgroundstretch.js') !!}

{!! Theme::js('js/jquery.sticky-kit.js') !!}

{!! Theme::js('js/bootstrap-slider.js') !!}

{!! Theme::js('js/owl.carousel.min.js') !!}

{!! Theme::js('js/jquery.vide.min.js') !!}

{!! Theme::js('js/scrollbar.min.js') !!}

{!! Theme::js('js/isotope.pkgd.js') !!}

{!! Theme::js('js/prettyPhoto.js') !!}

{!! Theme::js('js/raphael-min.js') !!}

{!! Theme::js('js/parallax.js') !!}

{!! Theme::js('js/sortable.js') !!}

{!! Theme::js('js/countTo.js') !!}

{!! Theme::js('js/appear.js') !!}

{!! Theme::js('js/gmap3.js') !!}

{!! Theme::js('js/dev_themefunction.js') !!}

{!! \Html::script('assets/corals/plugins/lightbox2/js/lightbox.min.js') !!}

{!! \Html::script('assets/corals/plugins/icheck/icheck.js') !!}

{!! Theme::js('plugins/toastr/toastr.min.js') !!}

{!! Theme::js('plugins/Lightbox/js/lightbox.js') !!}



{!! Theme::js('plugins/Ladda/spin.min.js') !!}
{!! Theme::js('plugins/Ladda/ladda.min.js') !!}


{!! Theme::js('plugins/select2/dist/js/select2.full.js') !!}

<!-- Jquery BlockUI -->
{!! Theme::js('plugins/jquery-block-ui/jquery.blockUI.min.js') !!}

{!! Theme::js('plugins/sweetalert2/dist/sweetalert2.all.min.js') !!}

{!! \Html::script('assets/corals/plugins/clipboard/clipboard.min.js') !!}

{!! \Html::script('assets/corals/js/corals_functions.js') !!}
{!! \Html::script('assets/corals/js/corals_main.js') !!}

{!! Theme::js('js/directory_functions.js') !!}
{!! Theme::js('js/directory_main.js') !!}


{!! Assets::js() !!}

@php  \Actions::do_action('footer_js') @endphp

@include('Corals::corals_main')
@yield('js')

<script type="text/javascript">
    {!! \Settings::get('custom_js', '') !!}
</script>

@include('partials.notifications')