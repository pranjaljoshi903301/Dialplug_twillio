<form id="listing_form" class="listar-formtheme listar-formsearchlisting">
    <fieldset>
        <div class="form-group listar-inputwithicon">
            <i class="icon-icons185"></i>
            <input type="text" name="search" class="form-control"
                   placeholder="@lang('corals-directory-listing-star::labels.template.listing.keywords'):"
                   value="{{request()->get('search')}}"/>
        </div>
        <div class="form-group listar-inputwithicon">
            <i class="icon-global"></i>
            <div class="listar-select listar-selectlocation">
                <select name="location" id="listar-locationchosen"
                        class="listar-locationchosen listar-chosendropdown custom-select">
                    <option value="">@lang('corals-directory-listing-star::labels.template.listing.location')</option>
                    @foreach(\Address::getLocationsList('Directory',true) as $location)
                        <option value="{{$location->slug}}" {{  checkActiveKey($location->slug,'location') ? "selected" : ""  }}>{{$location->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group listar-inputwithicon">
            <i class="icon-layers"></i>
            <div class="listar-select listar-selectlocation">
                <select id="listar-categorieschosen" name="category"
                        class="listar-categorieschosen listar-chosendropdown custom-select">
                    <option value=""
                            selected>@lang('corals-directory-listing-star::labels.template.listing.all_categories')</option>
                    @foreach(\Category::getCategoriesList('Directory',false,true) as $category)
                        <option value="{{$category->slug}}" {{  checkActiveKey($category->slug,'category')?'selected':'' }}>{{$category->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <div class="listar-distance">
            <h2>@lang('corals-directory-listing-star::labels.template.listing.radius_around_selected_destination')</h2>
            <input id="listar-distancerangeslider" type="range" data-slider-min="0"
                   data-slider-max="100" data-slider-step="1"
                   data-slider-value="{{request()->has('distance')?request()->get('distance'):0}}" name="distance">
        </div>
        <input type="hidden" id="lat" name="lat" value="{{request()->get('lat')}}">
        <input type="hidden" id="long" name="long" value="{{request()->get('long')}}">
    </fieldset>
    <fieldset>
        <div class="listar-leftbox">
            <a id="listar-btnadvancefeatures" class="listar-btnadvancefeatures" href="javascript:void(0);"><i
                        class="icon-minus"></i><span>@lang('corals-directory-listing-star::labels.template.listing.more_filter')</span></a>
        </div>
        <div class="listar-rightbox">
            <ul class="listar-views">
                <li><a class="{{ $layout=='grid'?'active':'' }}"
                       href="{{ request()->fullUrlWithQuery(['layout'=>'grid']) }}"><i class="icon-grid"></i></a></li>
                <li class="listar-active">
                    <a class="{{ $layout=='list'?'active':'' }}"
                       href="{{ request()->fullUrlWithQuery(['layout'=>'list']) }}"><i class="icon-icons152"></i></a>
                </li>
            </ul>
        </div>
        <div id="listar-advancefitures" class="listar-advancefitures display-none">
			<span class="listar-checkbox filter-tags-wrap">
                <input id="check-a" type="checkbox" name="open"
                       value="open" {{  checkActiveKey('open','open')?'checked':'' }}>
                    <label for="check-a">@lang('corals-directory-listing-star::labels.template.listing.open_only')</label>
            </span>
            <span class="listar-radio filter-tags-wrap" style="display: flex;margin-top: 10px">
              <input id="check-b" type="radio" value="current_location" name="location_coordinates"
                     onclick="getLocation()" {{  checkActiveKey('current_location','location_coordinates')?'checked':'' }}>
              <label for="check-b">@lang('corals-directory-listing-star::labels.template.listing.using_current_location_as_reference')</label>
               <input id="check-c" type="radio" value="listing_location"
                      name="location_coordinates"
                      class="filter-tags-wrap {{  checkActiveKey('listing_location','location_coordinates')||request()->has('location') ?'':'display-none' }}"
                       {{  checkActiveKey('listing_location','location_coordinates')?'checked':'' }}>
                        <label for="check-c">@lang('corals-directory-listing-star::labels.template.listing.using_listing_location_as_reference')</label>
            </span>
            <div class="listar-select listar-selectlocation custom-tag" style="margin-top: 25px">
                {!! CoralsForm::select('tags[]','', \Tag::getTagsList('Directory'),false,null,['class'=>'listar-locationchosen listar-chosendropdown custom-select', 'multiple'=>true], 'select2') !!}

            </div>
        </div>
    </fieldset>
    <button type="submit" class="listar-btn listar-btngreen flex-center" id="custom-filter">@lang('corals-directory-listing-star::labels.template.listing.filter')</button>
    <button type="button" class="listar-btn listar-btnblue flex-center"
            onclick="clearForm(null, $('#listing_form'))">@lang('corals-directory-listing-star::labels.template.listing.clear_filter')</button>
</form>