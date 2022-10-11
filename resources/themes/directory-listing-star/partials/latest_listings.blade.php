<section class="listar-sectionspace listar-bglight listar-haslayout">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="listar-sectionhead">
                    <div class="listar-sectiontitle">
                        <h2>@lang('corals-directory-listing-star::labels.template.home.latest_listings')</h2>
                    </div>
                </div>
                <div class="listar-horizontalthemescrollbar">
                    <div class="listar-themeposts listar-placesposts">
                        @foreach(\Corals\Modules\Directory\Facades\Directory::getListingsList(true,3) as $listing)
                            <div class="listar-themepost listar-placespost">
                                <figure class="listar-featuredimg"><a href="{{$listing->getShowUrl()}}"><img
                                                src="{{$listing->image}}" alt="{{$listing->name}}" style="height: 302px;"></a>
                                </figure>
                                <div class="listar-postcontent">
                                    <h3><a href="{{$listing->getShowUrl()}}">{{$listing->name}}</a></h3>
                                    <div class="listar-description">
                                        <p>{!! \Str::limit($listing->description,100) !!}</p>
                                    </div>
                                    <div class="listar-reviewcategory">
                                        <div class="listar-review">
                                            @include('partials.components.rating',['rating'=> $listing->averageRating(1)[0],'rating_count'=>null])
                                            <em>({{($listing->wishlistsCount())}} @lang('corals-directory-listing-star::labels.partial.component.save_listing')
                                                )</em>
                                        </div>
                                        @foreach($listing->activeCategories as $category)
                                            <a href="{{ url('listings?category='.$category->slug) }}" class="listar-category">
                                                <i class="icon-nightlife"></i>
                                                <span>{{$category->name}}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                    <div class="listar-themepostfoot">
                                        <a class="listar-location" href="javascript:void(0);">
                                            <i class="icon-icons74"></i>
                                            <em>{{$listing->location->name}}</em>
                                        </a>
                                        <div class="listar-postbtns">
                                            @if(\Settings::get('directory_wishlist_enable',true))
                                                @include('partials.components.wishlist_product',['wishlist'=> $listing->inWishList() ])
                                            @endif
                                            <div class="listar-btnquickinfo">
                                                <div class="listar-shareicons">
                                                    @include('partials.components.social_share',['url'=> $listing->getShowUrl() , 'title'=>$listing->name ])
                                                </div>
                                                <a class="listar-btnshare" href="javascript:void(0);">
                                                    <i class="icon-share3"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
