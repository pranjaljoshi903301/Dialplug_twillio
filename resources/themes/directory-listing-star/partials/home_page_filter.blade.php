<form id="filterForm" action="{{url('listings')}}" class="listar-formtheme listar-formsearchlisting overflow-hidden">
    <fieldset>
        <div class="form-group listar-inputwithicon" style="padding-top: 5px">
            <i class="icon-icons185"></i>
            <input type="text" name="search" class="form-control border-none"
                   placeholder="@lang('corals-directory-listing-star::labels.template.listing.keywords'):"
                   value="{{request()->get('search')}}"/>
        </div>
        <div class="form-group listar-inputwithicon">
            <i class="icon-global"></i>
            <div class="listar-select listar-selectlocation">
                <select id="listar-locationchosen" name="location"
                        class="listar-locationchosen listar-chosendropdown">
                    <option value="">@lang('corals-directory-listing-star::labels.template.listing.location')</option>
                    @foreach(\Address::getLocationsList('Directory',true) as $location)
                        <option value="{{$location->slug}}" {{  checkActiveKey($location->slug,'location') ? "selected" : ""  }}>{{$location->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group listar-inputwithicon">
            <i class="icon-global"></i>
            <div class="listar-select listar-selectlocation">
                <select id="listar-locationchosen" name="category"
                        class="listar-locationchosen listar-chosendropdown">
                    <option value=""
                            selected>@lang('corals-directory-listing-star::labels.template.listing.all_categories')</option>
                    @foreach(\Category::getCategoriesList('Directory',false,true) as $category)
                        <option value="{{$category->slug}}" {{  checkActiveKey($category->slug,'category')?'selected':'' }}>{{$category->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <button type="submit" class="listar-btn listar-btngreen flex-center">@lang('corals-directory-listing-star::labels.partial.search')</button>
    </fieldset>
</form>
