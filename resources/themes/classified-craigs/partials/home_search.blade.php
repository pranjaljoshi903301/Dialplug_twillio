<form class="hero-form form" method="GET" action="{{url('products')}}">
    <div class="container">
        <div class="main-search-form">
            <div class="form-row">
                <div class="col-md-3 col-sm-3">
                    <div class="form-group">
                        <label for="what"
                               class="col-form-label">@lang('corals-classified-craigs::labels.partial.what')</label>
                        <input type="text" name="search" class="form-control  @yield('class_search')"
                               placeholder="@lang('corals-classified-craigs::labels.template.product.search')">
                    </div>
                </div>
                <div class="col-md-3 col-sm-3">
                    <div class="form-group">
                        <label for="input-location"
                               class="col-form-label">@lang('corals-classified-craigs::labels.partial.where')</label>
                        @if( \Settings::get('classified_enable_google_location', false))

                            <input type="text" id="_autocomplete" class="form-control"
                                   placeholder="Enter Address">

                            <input type="hidden" name="zipcode" id="zip_code">
                        @else

                            <select name="location" id="location" class="@yield('class_search')"
                                    data-placeholder="Select Location">
                                <option value="">@lang('corals-classified-craigs::labels.partial.select_location')</option>
                                @foreach(\Address::getLocationsList('Classified', true) as $id => $location)
                                    <option value="{{$location->slug}}">{!! $location->name !!}</option>
                                @endforeach
                            </select>



                        @endif
                        <span class="geo-location input-group-addon" data-toggle="tooltip" data-placement="top"
                              title="Find My Position"><i class="fa fa-map-marker"></i></span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3">
                    <div class="form-group">
                        <label for="category"
                               class="col-form-label">@lang('corals-classified-craigs::labels.post.category')</label>
                        <select name="category[]" class="@yield('class_search')" id="category"
                                data-placeholder="Select Category">
                            <option value="">@lang('corals-classified-craigs::labels.partial.select_category')</option>
                            @foreach(\Category::getCategoriesList('Classified', true, true, 'active') as $category)
                                <option value="{{$category->slug}}">{!! $category->name !!}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-sm-3">
                    <button type="submit"
                            class="btn btn-primary width-100 @yield('class_search')">@lang('corals-classified-craigs::labels.template.product.search')</button>
                </div>
            </div>
        </div>
        <div class="alternative-search-form">
            <a href="#collapseAlternativeSearchForm" class="icon" data-toggle="collapse" aria-expanded="false"
               aria-controls="collapseAlternativeSearchForm"><i
                        class="fa fa-plus"></i>@lang('corals-classified-craigs::labels.template.product.more_option')
            </a>
            <div class="collapse" id="collapseAlternativeSearchForm">
                <div class="wrapper">
                    <div class="form-row">
                        <!--end col-xl-6-->
                        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
                            <div class="form-row">
                                <div class="col-md-4 col-sm-4">
                                    <div class="form-group">
                                        @php
                                            $min = \Classified::getMinPrice()??0;
                                            $max= \Classified::getMaxPrice()??99999;
                                        @endphp
                                        @if($min !== $max )
                                            <div class="form-group">
                                                <input name="price[min]" value="" type="text" class="form-control small"
                                                       id="min-price"
                                                       placeholder="@lang('corals-classified-craigs::labels.product.min_price')">
                                                <span class="input-group-addon small">$</span>
                                            </div>
                                    </div>
                                    <!--end form-group-->
                                </div>
                                <!--end col-md-4-->
                                <div class="col-md-4 col-sm-4">
                                    <div class="form-group">
                                        <input name="price[max]" type="text" value="" class="form-control small"
                                               id="max-price"
                                               placeholder="@lang('corals-classified-craigs::labels.product.max_price')">
                                        <span class="input-group-addon small">$</span>
                                    </div>
                                @endif
                                <!--end form-group-->
                                </div>
                                <!--end col-md-4-->
                                <div class="col-md-4 col-sm-4">
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
                                <!--end col-md-3-->
                            </div>
                            <!--end form-row-->
                        </div>
                        <!--end col-xl-6-->
                    </div>
                    <!--end row-->
                </div>
                <!--end wrapper-->
            </div>
            <!--end collapse-->
        </div>
    </div>
</form>