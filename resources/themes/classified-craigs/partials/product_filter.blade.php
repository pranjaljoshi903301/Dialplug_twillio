<section>
    <h2>@lang('corals-classified-craigs::labels.template.product.search')</h2>
    <form class="sidebar-form form" id="filterForm">
        <input type="hidden" name="sort" id="filterSort" value=""/>
        <input type="hidden" name="user" id="filterSort" value="{{  \Request::get('user') }}"/>
        <div class="form-group">
            <label for="input-location"
                   class="col-form-label">@lang('corals-classified-craigs::labels.partial.where')</label>


            @if( \Settings::get('classified_enable_google_location', false))

                <input type="text" id="_autocomplete" class="form-control"
                       placeholder="Enter Address">

                <input type="hidden" name="zipcode" id="zip_code">
            @else

                <select name="location" id="location" class="@yield('class_search')" data-placeholder="Select Location">
                    <option value="">@lang('corals-classified-craigs::labels.partial.select_location')</option>
                    @foreach(\Address::getLocationsList('Classified', true) as $id => $location)
                        <option value="{{$location->slug}}" {{ request()->get('location') == $location->slug?'selected':'' }}>
                            {!! $location->name !!}
                        </option>
                    @endforeach
                </select>



            @endif


            <span class="geo-location input-group-addon" data-toggle="tooltip" data-placement="top"
                  title="Find My Position"><i class="fa fa-map-marker"></i></span>
        </div>
        <div class="form-group">
            <input type="search" autocomplete="off" name="search" class="form-control" id="search-input"
                   placeholder="@lang('corals-classified-craigs::labels.template.product.search')"
                   value="{{request()->get('search')}}">
            <button type="submit" id="search-submit" class="input-group-addon" style=""><i class="fa fa-search"></i>
            </button>
        </div>
        <button type="submit" class="btn btn-primary width-100">
            @lang('corals-classified-craigs::labels.template.shop.filter')
        </button>
        <!--Alternative Form-->
        <div class="alternative-search-form">
            <a href="#collapseAlternativeSearchForm" class="icon" data-toggle="collapse" aria-expanded="false"
               aria-controls="collapseAlternativeSearchForm"><i
                        class="fa fa-plus"></i>@lang('corals-classified-craigs::labels.template.product.more_option')
            </a>
            <div class="collapse" id="collapseAlternativeSearchForm">
                <div class="wrapper">
                    <ul>
                        @foreach(\Category::getCategoriesList('Classified', true, true, 'active') as $category)
                            <li class="{{ $hasChildren = $category->hasChildren()?'has-children':'' }} parent-category">
                                @if($hasChildren)
                                    <input class=""
                                           name="category[]" value="{{ $category->slug }}"
                                           type="checkbox"
                                           id="ex-check-{{ $category->id }}" {{ checkActiveKey($category->slug,'category')?'checked':'' }}>
                                    <a href="#" data-toggle="collapse" data-target="#collapse_{{$category->id}}"
                                       aria-expanded="false"
                                       aria-controls="collapse_{{$category->id}}" style="display:initial" }}>
                                        {{ $category->name }}
                                        ({{\Classified::getCategoryAvailableProducts($category->id, true)}})
                                        <i class="fa" aria-hidden="true"></i>
                                    </a>

                                @else
                                    <label class="">
                                        <input class=""
                                               name="category[]" value="{{ $category->slug }}"
                                               type="checkbox"
                                               id="ex-check-{{ $category->id }}" {{ checkActiveKey($category->slug,'category')?'checked':'' }}>
                                        <label class="" style="display: inline;"
                                               for="ex-check-{{ $category->id }}">
                                            {{ $category->name }}
                                            ({{ \Classified::getCategoryAvailableProducts($category->id, true)}})
                                        </label>
                                    </label>
                                @endif
                                @if($hasChildren)
                                    <ul>
                                        <div id="collapse_{{$category->id}}" class="panel-collapse collapse"
                                             role="tabpanel"
                                             aria-labelledby="collapseListGroupHeading1">
                                            @foreach($category->children as $child)
                                                <li class="ml-4" style="display:block;">
                                                    <label class="">
                                                        <input class=""
                                                               name="category[]" value="{{ $child->slug }}"
                                                               type="checkbox"
                                                               id="ex-check-{{ $child->id }}"
                                                                {{ checkActiveKey($child->slug,'category')?'checked':'' }}>
                                                        <label class=""
                                                               for="ex-check-{{ $child->id }}">
                                                            {{ $child->name }}
                                                            ({{ \Classified::getCategoryAvailableProducts($child->id,
                                                            true) }}
                                                            )
                                                        </label>
                                                    </label>
                                                </li>
                                            @endforeach
                                        </div>
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <div class="form-group">
                        {!! \Classified::getAttributesForFilters(request()->input('category')) !!}
                    </div>
                    <div class="form-group">
                        <div class="form-group">
                            <input name="price[min]" type="text" value="{{request()->input('price.min') ?? ""}}"
                                   class="form-control small" id="min-price"
                                   placeholder="@lang('corals-classified-craigs::labels.product.min_price')">
                            <span class="input-group-addon small">$</span>
                        </div>
                        <div class="form-group">
                            <input name="price[max]" type="text" value="{{request()->input('price.max') ?? ""}}"
                                   class="form-control small" id="max-price"
                                   placeholder="@lang('corals-classified-craigs::labels.product.max_price')">
                            <span class="input-group-addon small">$</span>
                        </div>
                    </div>
                    @if(\Settings::get('classified_year_model_visible'))
                        <div class="form-group">
                            <div class="form-group">
                                <input name="year_model[min]" type="text"
                                       value="{{request()->input('year_model.min') ?? ""}}"
                                       class="form-control small" id="min-year_model"
                                       placeholder="@lang('corals-classified-craigs::labels.product.min_year_model')">
                            </div>
                            <div class="form-group">
                                <input name="year_model[max]" type="text"
                                       value="{{request()->input('year_model.max') ?? ""}}"
                                       class="form-control small" id="max-year_model"
                                       placeholder="@lang('corals-classified-craigs::labels.product.max_year_model')">
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        <select name="condition" class="small"
                                data-placeholder="@lang('corals-classified-craigs::labels.product.by_condition')">
                            <option value=""
                                    selected>@lang('corals-classified-craigs::labels.product.select_condition')</option>
                            @foreach(\Settings::get('classified_product_condition_options',[]) as $condition_key=>$condition)
                                <option value="{{$condition_key}}" {{ request()->get('condition') == $condition_key?'selected':'' }}>
                                    {{$condition}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

@section('js')

    @parent

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
