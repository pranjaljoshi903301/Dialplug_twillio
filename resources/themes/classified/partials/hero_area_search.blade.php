<div class="search-inner">
    <form class="search-form" method="GET" action="{{url('products')}}">

        <div class="form-group inputwithicon">
            <i class="lni-tag"></i>
            <input type="text" name="search" class="form-control"
                   placeholder="Enter product Keyword">
        </div>

        @if( \Settings::get('classified_enable_google_location', false))
            <div class="form-group inputwithicon">
                <i class="lni-map-marker"></i>
                <input type="text" id="_autocomplete" class="form-control"
                       placeholder="Enter Address">

                <input type="hidden" name="zipcode" id="zip_code">
            </div>
        @else

            <div class="form-group inputwithicon">
                <i class="lni-map-marker"></i>
                <div class="select">
                    <select name="location">
                        <option value="">Locations</option>
                        @foreach(\Address::getLocationsList('Classified', true) as $id => $location)
                            <option value="{{ $location->slug }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        @endif
        <div class="form-group inputwithicon">
            <i class="lni-menu"></i>
            <div class="select">
                <select name="category[]">
                    <option value="">Categories</option>
                    @foreach(\Category::getCategoriesList('Classified', true, true, 'active') as $category)
                        <option value="{{$category->slug}}">{{$category->name}}</option>
                    @endforeach

                </select>
            </div>
        </div>
        <button class="btn btn-common" type="submit">
            <i class="lni-search"></i> Search Now
        </button>
    </form>
</div>