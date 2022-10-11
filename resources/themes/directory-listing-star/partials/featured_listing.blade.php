<div id="listar-relatedlistingslider"
     class="listar-relatedlistingslider listar-gridview owl-carousel">
    @foreach(\Corals\Modules\Directory\Facades\Directory::getListingsList(true) as $listing)
        <div class="item">
            @include('partials.listing_grid_item')
        </div>
    @endforeach
</div>
@foreach(\Corals\Modules\Directory\Facades\Directory::getListingsList(true) as $listing)
    @include('partials.modal_listing')
@endforeach
