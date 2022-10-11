@extends('layouts.theme')

@section('hero_area')
    <!-- Hero Area Start -->
    <div id="hero-area">
        <div class="overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-lg-12 col-xs-12 text-center">
                    <div class="contents">
                        {!! $item->rendered !!}
                        <div class="search-bar">
                            @include('partials.hero_area_search')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero Area End -->
    @php \Actions::do_action('pre_content',$item,true) @endphp
@endsection
@section('editable_content')
    @include('partials.trending_categories')

    @include('partials.latest_products')

    @include('partials.featured_products')

    <section class="services section-padding">
        <div class="container">
            {!!   \Shortcode::compile( 'block','home-features' ) ; !!}
        </div>
    </section>

    @include('partials.counter_area');
    <!--
        <section id="pricing-table" class="section-padding">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mainHeading">
                            <h2 class="section-title">Select A Package</h2>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-xs-12">
                        <div class="table">
                            <div class="icon">
                                <i class="lni-gift"></i>
                            </div>
                            <div class="title">
                                <h3>SILVER</h3>
                            </div>
                            <div class="pricing-header">
                                <p class="price-value"><sup>$</sup>29<span>/ Mo</span></p>
                            </div>
                            <ul class="description">
                                <li><strong>Free</strong> ad posting</li>
                                <li><strong>No</strong> Featured ads availability</li>
                                <li><strong>For 30</strong> days</li>
                                <li><strong>100%</strong> Secure!</li>
                            </ul>
                            <button class="btn btn-common">Buy Now</button>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-xs-12">
                        <div class="table" id="active-tb">
                            <div class="icon">
                                <i class="lni-leaf"></i>
                            </div>
                            <div class="title">
                                <h3>STANDARD</h3>
                            </div>
                            <div class="pricing-header">
                                <p class="price-value"><sup>$</sup>89<span>/ Mo</span></p>
                            </div>
                            <ul class="description">
                                <li><strong>Free</strong> ad posting</li>
                                <li><strong>6</strong> Featured ads availability</li>
                                <li><strong>For 30</strong> days</li>
                                <li><strong>100%</strong> Secure!</li>
                            </ul>
                            <button class="btn btn-common">Buy Now</button>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-xs-12">
                        <div class="table">
                            <div class="icon">
                                <i class="lni-layers"></i>
                            </div>
                            <div class="title">
                                <h3>PLANINIUM</h3>
                            </div>
                            <div class="pricing-header">
                                <p class="price-value"><sup>$</sup>99<span>/ Mo</span></p>
                            </div>
                            <ul class="description">
                                <li><strong>Free</strong> ad posting</li>
                                <li><strong>20</strong> Featured ads availability</li>
                                <li><strong>For 25</strong> days</li>
                                <li><strong>100%</strong> Secure!</li>
                            </ul>
                            <button class="btn btn-common">Buy Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    -->

    <!-- Subscribe Section Start -->
    <section class="subscribes section-padding">
        <div class="container">
            <div class="row wrapper-sub">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <p>@lang('corals-classified-master::labels.template.home.join')</p>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    {!! Form::open( ['url' => url('utilities/newsletter/subscribe'),'method'=>'POST', 'class'=>'ajax-form','id'=>'subscribeForm']) !!}

                    <div class="subscribe">
                        <div class="form-group">
                            <label name="list_id"></label>
                            <input class="form-control" name="email"
                                   placeholder="@lang('corals-classified-master::labels.template.home.your_email')"
                                   type="text">
                        </div>

                        <button class="btn btn-common"
                                type="submit">@lang('corals-classified-master::labels.template.home.subscribe')</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- Subscribe Section End -->
    @include('partials.news')
@stop



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
