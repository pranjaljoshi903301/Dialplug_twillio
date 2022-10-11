<div class="modal fade listar-placequickview" tabindex="-1" role="dialog" id="listing-modal-{{$listing->hashed_id}}">
    <div class="modal-dialog listar-modaldialog" role="document" style="top: 0px">
        <div class="modal-content listar-modalcontent">
            <div class="listar-themepost listar-placespost" id="custom-margin" style="width: 100% !important;">
                <span class="listar-btnclosequickview" data-toggle="modal"
                      data-target="#listing-modal-{{$listing->hashed_id}}">X</span>
                <figure class="listar-featuredimg" data-vide-bg="poster: {{$listing->image}}"
                        data-vide-options="position: 50% 50%">
						<span class="listar-contactnumber">
							<i class="icon-"><img src="{{\Theme::url('images/icons/icon-03.png')}}"
                                                  alt="image description"></i>
							<em>{{$listing->user->phone_number ?? 'No Number'}}</em>
						</span>
                </figure>
                <div class="listar-postcontent">
                    <h3><a id="custom-modal-listing" href="{{$listing->getShowUrl()}}">{{$listing->name}}</a><i
                                class="icon-checkmark listar-postverified listar-themetooltip" data-toggle="tooltip"
                                data-placement="top" title="Verified"></i></h3>
                    <div class="listar-description">
                        <p>{!! \Str::limit($listing->description,300) !!}</p>
                    </div>
                    <ul class="listar-listfeatures">
                        @foreach($listing->activeCategories as $category)
                            <li>{!! $category->name !!}</li>
                        @endforeach
                    </ul>
                    <div class="listar-reviewcategory">
                        <div class="listar-review custom-modal-listing">
                            @include('partials.components.rating',['rating'=> $listing->averageRating(1)[0],'rating_count'=>null])
                            <em id="custom-modal-listing">({{($listing->wishlistsCount())}} @lang('corals-directory-listing-star::labels.partial.component.save_listing')
                                )</em>
                        </div>
                        <a href="{{ url('listings?location='.$listing->location->slug) }}" class="listar-category"
                           id="custom-modal-listing">
                            <i class="icon-tourism"></i>
                            <span id="custom-modal-listing">{{$listing->location->name}}</span>
                        </a>
                    </div>
                    <div class="listar-themepostfoot" id="listar-listing">
                        <div class="listar-postbtns">
                            @if(\Settings::get('directory_wishlist_enable',true))
                                @include('partials.components.wishlist_product',['wishlist'=> $listing->inWishList() ])
                            @endif
                            <div class="listar-btnquickinfo" id="listar-btnquickinfo">
                                <div class="listar-shareicons">
                                    @include('partials.components.social_share',['url'=> $listing->getShowUrl() , 'title'=>$listing->name ])
                                </div>
                                <a class="listar-btnshare" href="javascript:void(0);" id="listar-btnquickinfo">
                                    <i class="icon-share3"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>