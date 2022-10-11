@extends('layouts.crud.create_edit')



@section('content_header')
    @component('components.content_header')
        @slot('page_title')
            {{ $title_singular }}
        @endslot

        @slot('breadcrumb')
            {{ Breadcrumbs::render('classified_product_create_edit') }}
        @endslot
    @endcomponent
@endsection

@section('content')
    @parent
    <div class="row">
        <div class="col-md-7">
            @component('components.box')
                {!! CoralsForm::openForm($product) !!}
                <div class="row">
                    <div class="col-md-6">
                        {!! CoralsForm::text('name','Classified::attributes.product.name',true,$product->name,[]) !!}
                    </div>
                    <div class="col-md-6">
                        {!! CoralsForm::text('caption','Classified::attributes.product.caption',true,$product->caption) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        {!! CoralsForm::text('slug','Classified::attributes.product.slug',false, $product->slug, ['help_text'=>'Classified::attributes.product.slug_help']) !!}
                    </div>
                    <div class="col-md-6">
                        {!! CoralsForm::radio('status','Corals::attributes.status',true, $statusOptions) !!}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">

                        @if( \Settings::get('classified_enable_google_location', false))
                            {!! CoralsForm::text('address','Utility::attributes.location.address', true,null,['id'=>'_autocomplete','placeholder'=>'Utility::attributes.location.address_placeholder']) !!}

                            <input type="hidden" name="lat" id="lat"
                                   value="{{ $product->exists ? $product->lat : ""  }}">
                            <input type="hidden" name="long" id="long"
                                   value="{{ $product->exists ? $product->long : ""  }}">
                            <input type="hidden" name="zip_code" id="zip_code"
                                   value="{{ $product->exists ? $product->zip_code : ""  }}">
                        @else
                            {!! CoralsForm::select('location_id','Classified::attributes.product.location', \Address::getLocationsList('Classified'), true, null, [], 'select2') !!}
                        @endif
                        {!! CoralsForm::select('condition','Classified::attributes.product.condition', \Settings::get('classified_product_condition_options',[])) !!}

                        {!! CoralsForm::select('categories[]','Classified::attributes.product.categories', \Category::getCategoriesList('Classified'),true,null,['id'=>'categories', 'multiple'=>true], 'select2') !!}
                        <div id="attributes">
                        </div>
                    </div>
                    <div class="col-md-6">
                        @if(\Settings::get('classified_year_model_visible'))
                            {!! CoralsForm::number('year_model', 'Classified::attributes.product.year_model', false, $product->year_model,
                            ['step'=> 1, 'min'=>0, 'max'=>999999]) !!}
                        @endif
                        {!! CoralsForm::number('price', 'Classified::attributes.product.price', false, $product->price,
                        ['step'=>0.01, 'min'=>0, 'max'=>999999, 'left_addon'=>'<i class="'.$product->currency_icon.'"></i>']) !!}
                        {!! CoralsForm::checkbox('price_on_call', 'Classified::attributes.product.price_on_call', $product->price_on_call,1,['help_text'=>'Classified::attributes.product.price_on_call_help']) !!}
                        {!! CoralsForm::checkbox('is_featured', 'Classified::attributes.product.is_featured', $product->is_featured) !!}
                        {!! CoralsForm::checkbox('verified', 'Classified::attributes.product.verified', $product->verified) !!}
                        {!! CoralsForm::select('tags[]','Classified::attributes.product.tags', \Tag::getTagsList('Classified'),false,null,['class'=>'tags', 'multiple'=>true], 'select2') !!}
                        {!! CoralsForm::text('brand','Classified::attributes.product.brand') !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {!! CoralsForm::textarea('description','Classified::attributes.product.description',false, $product->description, ['class'=>'ckeditor','rows'=>5]) !!}
                    </div>
                </div>

                {!! \Actions::do_action('classified_product_form_post_fields', $product) !!}

                {!! CoralsForm::customFields($product) !!}
                <div class="row">
                    <div class="col-md-12">
                        {!! CoralsForm::formButtons() !!}
                    </div>
                </div>
                {!! CoralsForm::closeForm($product) !!}
            @endcomponent
        </div>
        @if($product->exists)
            <div class="col-md-5">
                @component('components.box')
                    @include('Utility::gallery.gallery',['galleryModel'=> $product, 'editable'=>true])
                @endcomponent
            </div>
        @endif
    </div>
@endsection

@section('js')
    @include('Utility::category.category_scripts', ['category_field_id'=>'#categories','attributes_div'=>'#attributes'])

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
            $('#lat').val(place.geometry.location.lat());
            $('#long').val(place.geometry.location.lng());
            //$('#zip_code').val(place.geometry.location.lng());

            console.log(place.address_components);

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
