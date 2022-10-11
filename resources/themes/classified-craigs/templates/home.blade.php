@extends('layouts.public')

@section('page_header')
    @php \Actions::do_action('pre_content',$item, $home??null) @endphp

    <div class="page-title">
        <div class="container">

            {!! $item->content !!}
        </div>
        <!--end container-->
    </div>
@endsection
@section('home','home-page')
@section('side_bar')
    @include('partials.home_search')
@endsection
@section('content')
    <div class="page home-page">

        <section class="block">
            <div class="container">
                <div class="section-title clearfix">
                    <div class="float-xl-left float-md-left float-sm-none">
                        <h2>@lang('corals-classified-craigs::labels.partial.latest_products')</h2>
                    </div>
                </div>
                <!--============ Items ==========================================================================-->
                <div class="items masonry grid-xl-4-items grid-lg-3-items grid-md-2-items">
                    @include('partials.latest_products')
                </div>
            </div>
        </section>
    </div>
    @include('partials.news')
@endsection


@section('js')

    <script>
        var autocomplete;

        function initAutocomplete() {
            autocomplete = new google.maps.places.Autocomplete(
                /** @type {!HTMLInputElement} */(document.getElementById('_autocomplete')),
                {
                    types: ['geocode'],
                });
            // When the user selects an address from the dropdown, populate the address
            // fields in the form.
            autocomplete.addListener('place_changed', fillInAddress);
        }

        function fillInAddress() {
            // Get the place details from the autocomplete object.
            var place = autocomplete.getPlace();

            for (var i = 0; i < place.address_components.length; i++) {
                for (var j = 0; j < place.address_components[i].types.length; j++) {
                    if (place.address_components[i].types[j] == "postal_code") {
                        $('#zip_code').val(place.address_components[i].long_name);

                    }
                }
            }

        }

        var googleSrc = 'https://maps.googleapis.com/maps/api/js?key={{ \Settings::get('utility_google_address_api_key') }}&libraries=places&callback=initAutocomplete';
        document.write('<script src="' + googleSrc + '" async defer><\/script>');
    </script>
@endsection
