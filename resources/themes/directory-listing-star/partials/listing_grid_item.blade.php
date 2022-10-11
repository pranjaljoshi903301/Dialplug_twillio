<div class="listar-themepost listar-placespost">
    <figure class="bg listar-featuredimg custom-background-listing-grid" data-bg="{{$listing->image}}"
            data-scrollax="properties: { translateY: '30%' }">
        <a href="{{$listing->getShowUrl()}}"></a>
        <div class="listar-postcontent">
            <h3><a href="{{$listing->getShowUrl()}}">{{$listing->name}}</a></h3>
            <div class="listar-reviewcategory">
                <div class="listar-review d-flex">
                    @include('partials.components.rating',['rating'=> $listing->averageRating(1)[0],'rating_count'=>null])
                    <em>({{($listing->wishlistsCount())}} @lang('corals-directory-listing-star::labels.partial.component.save_listing')
                        )</em>
                </div>
                <a href="javascript:void(0);" class="listar-category">
                    @foreach($listing->activeCategories as $category)
                        <span>
                        <a class="float-right listar-category"
                           href="{{ url('listings?category='.$category->slug) }}">{{$category->name}}</a>
                    </span>
                    @endforeach
                </a>
            </div>
            <div class="listar-themepostfoot">
                <a class="listar-location" href="{{ url('listings?location='.$listing->location->slug) }}">
                    <i class="icon-icons74"></i>
                    <em>{{$listing->location->name}}</em>
                </a>
                <div class="listar-postbtns">
                    <a class="listar-btnquickinfo" href="#" data-toggle="modal"
                       data-target="#listing-modal-{{$listing->hashed_id}}"><i
                                class="icon-expand"></i></a>
                    @if(\Settings::get('directory_wishlist_enable',true))
                        @include('partials.components.wishlist_product',['wishlist'=> $listing->inWishList() ])
                    @endif
                    <div class="listar-btnquickinfo">
                        <div class="listar-shareicons">
                            @include('partials.components.social_share',['url'=> $listing->getShowUrl() , 'title'=>$listing->name ])
                        </div>
                        <a class="listar-btnshare" href="javascript:void(0);">
                            <i class="icon-share3"></i>
                        </a></div>
                </div>
            </div>
        </div>
    </figure>
</div>

