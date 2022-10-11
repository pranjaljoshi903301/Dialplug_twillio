@extends('layouts.public')


@section('css')
    <style>
        #modal-content {
            width: 50%;
        }

        .modal.in .modal-dialog {
            display: flex;
            justify-content: center;
        }
    </style>
@endsection
@section('content')
    <div class="listar-innerpagesearch">
        <a id="listar-btnsearchtoggle" class="listar-btnsearchtoggle" href="javascript:void(0);"><i
                    class="icon-icons185"></i></a>
        <div id="listar-innersearch" class="listar-innersearch">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        @include('partials.home_page_filter')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <main id="listar-main" class="listar-main listar-haslayout">
        <div id="listar-twocolumns" class="listar-twocolumns">
            <div class="listar-themepost listar-placespost listar-detail listar-detailvtwo">
                <figure class="listar-featuredimg">
                    <section class="parallax-section">
                        <div class="image-background bg" data-bg="{{$listing->image}}"
                             data-scrollax="properties: { translateY: '30%' }"></div>
                    </section>
                    <figcaption>
                        <div class="container">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="listar-postcontent">
                                        <div class="listar-reviewcategory">
                                            <h1>{!! $listing->name !!}<i
                                                        class="icon-checkmark listar-postverified listar-themetooltip"
                                                        data-toggle="tooltip" data-placement="top"
                                                        title=""
                                                        data-original-title="Verified"></i>
                                                @if($listing->user)
                                                    <span style="font-size: 15px;"> - @lang('corals-directory-listing-star::labels.template.product_single.added_by') </span>
                                                    <a style="font-size: 15px;" class="custom-color"
                                                       href="{{ url('listings?user='.$listing->user->hashed_id) }}">
                                                        {{ $listing->user->name }}</a>
                                                @endif
                                            </h1>
                                            <div class="listar-review">
                                                @if(\Settings::get('directory_rating_enable',true))
                                                    @include('partials.components.rating',['rating'=> $listing->averageRating(1)[0],'rating_count'=>$listing->countRating()[0] ])
                                                @endif
                                            </div>
                                        </div>
                                        <ul class="listar-postinfotags">
                                            <li>
                                                @if(empty($listing->user_id))
                                                    @if(!user())
                                                        <a href="{{ url('login') }}"
                                                           class="listar-tagviews">
                                                            <i class="fa fa-paperclip"></i>
                                                            <span>@lang('Directory::attributes.claim.listing_claim')</span>
                                                        </a>
                                                    @else
                                                        @can('create', Corals\Modules\Directory\Models\Claim::class)
                                                            <a href="#" class="listar-btnshare" data-toggle="modal"
                                                               data-target="#listing_claim-{{$listing->hashed_id}}"><i
                                                                        class="fa fa-paperclip"></i>@lang('Directory::attributes.claim.listing_claim')
                                                            </a>
                                                        @endcan
                                                    @endif
                                                @endif
                                            </li>
                                            <li>
                                                @if(\Settings::get('directory_wishlist_enable',true))
                                                    @include('partials.components.wishlist_product',['wishlist'=> $listing->inWishList() ])
                                                @endif
                                            </li>
                                            <li>
                                                <div class="listar-btnquickinfo">
                                                    <a class="listar-btnshare"
                                                       href="javascript:void(0);">
                                                        <i class="icon-share3"></i>
                                                        <span>@lang('corals-directory-listing-star::labels.template.product_single.share')</span>
                                                    </a>
                                                    <div class="listar-btnquickinfo">
                                                        <div class="listar-shareicons">
                                                            @include('partials.components.social_share',['url'=> URL::current() , 'title'=>$listing->name ])
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <li><span class="listar-tagviews"><i
                                                            class="icon-eye2"></i><span>{{$listing->visitors_count}}</span></span>
                                            </li>
                                        </ul>
                                        <div class="listar-themepostfoot">
                                            <ul>
                                                @if($listing->getProperty('contact_info.phone_number') )
                                                    <li>
                                                        <i class="icon-telephone114"></i>
                                                        <span>{{$listing->getProperty('contact_info.phone_number')}}</span>
                                                    </li>
                                                @endif
                                                <li>
                                                    <i class="icon-icons74"></i>
                                                    <span>{{$listing->location->name}}</span>
                                                </li>
                                                @if($listing->getProperty('contact_info.email'))
                                                    <li>
                                                        <i class="icon-email"></i>
                                                        <span><a href="mailto:{{$listing->getProperty('contact_info.email') }}">{{$listing->getProperty('contact_info.email')}}</a></span>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </figcaption>
                </figure>
                <div class="clearfix"></div>
                <div class="container">
                    <div class="row">
                        <div id="listar-detailcontent" class="listar-detailcontent">
                            <div class="listar-content">
                                <div class="listar-themetabs">
                                    <div id="listar-fixedtabnav" class="listar-fixedtabnav">
                                        <div class="container">
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                    <ul class="listar-themetabnav">
                                                        <li>
                                                            <a href="#listar-overview">@lang('corals-directory-listing-star::labels.template.product_single.overview')</a>
                                                        </li>
                                                        <li>
                                                            <a href="#listar-addressmaplocation">@lang('corals-directory-listing-star::labels.template.product_single.location')</a>
                                                        </li>
                                                        <li>
                                                            <a href="#listar-reviews">@lang('corals-directory-listing-star::labels.partial.reviews')</a>
                                                        </li>
                                                        <li>
                                                            <a href="#listar-gallery">@lang('corals-directory-listing-star::labels.template.product_single.gallery')</a>
                                                        </li>
                                                    </ul>
                                                    @if($listing->getProperty('social') || $listing->getProperty('contact_info'))
                                                        <ul class="listar-socialicons listar-socialiconsborder">
                                                            @if($listing->getProperty('social.facebook'))
                                                                <li class="listar-facebook">
                                                                    <a href="{{ $listing->getProperty('social.facebook')  }}"
                                                                       target="_blank"><i
                                                                                class="fa fa-facebook"></i></a></li>
                                                            @endif
                                                            @if($listing->getProperty('social.twitter'))
                                                                <li class="listar-twitter"><a
                                                                            href="{{ $listing->getProperty('social.twitter')  }}"
                                                                            target="_blank"><i
                                                                                class="fa fa-twitter"></i></a></li>
                                                            @endif
                                                            @if($listing->getProperty('contact_info.phone_number') )
                                                                <li class="listar-vimeo">
                                                                    <a href="mailto:{{$listing->getProperty('contact_info.phone_number') }}"><i
                                                                                class="icon-telephone114"></i></a>
                                                                </li>
                                                            @endif
                                                            @if($listing->getProperty('contact_info.email'))
                                                                <li class="listar-vimeo">
                                                                    <a href="{{$listing->getProperty('contact_info.email') }}"><i
                                                                                class="icon-email"></i></a>
                                                                </li>
                                                            @endif
                                                            @if($listing->getProperty('contact_info.whatsapp'))
                                                                <li class="listar-vimeo">
                                                                    <a href="mailto:{{$listing->getProperty('contact_info.whatsapp') }}"><i
                                                                                class="icon-whatsapp"></i></a>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <ul id="listar-themetabnav" class="listar-themetabnav">
                                        <li class="listar-active"><a
                                                    href="#listar-overview">@lang('corals-directory-listing-star::labels.template.product_single.overview')</a>
                                        </li>
                                        <li>
                                            <a href="#listar-addressmaplocation">@lang('corals-directory-listing-star::labels.template.product_single.location')</a>
                                        </li>
                                        <li>
                                            <a href="#listar-reviews">@lang('corals-directory-listing-star::labels.partial.reviews')</a>
                                        </li>
                                        <li>
                                            <a href="#listar-gallery">@lang('corals-directory-listing-star::labels.template.product_single.gallery')</a>
                                        </li>
                                    </ul>
                                    <div class="listar-sectionholder">
                                        <div id="listar-overview" class="listar-overview">
                                            <h3>@lang('corals-directory-listing-star::labels.template.product_single.about') {{$listing->name}} </h3>
                                            <p>{!! $listing->description !!}</p>
                                            <div class="listar-videobox overflow-hidden">
                                                <img src="{{$listing->image}}">
                                            </div>
                                            <div class="listar-amenitiesarea">
                                                <div class="listar-title">
                                                    <h3>@lang('corals-directory-listing-star::labels.template.product_single.amenities')</h3>
                                                </div>
                                                <ul class="listar-amenities">
                                                    @foreach($listing->options as $option)
                                                        <li>{{$option->attribute->lable}}
                                                            :{{$option->formattedValue}}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                        <div id="listar-addressmaplocation"
                                             class="listar-addressmaplocation">
                                            <div class="listar-title">
                                                <h3>@lang('corals-directory-listing-star::labels.template.product_single.location')</h3>
                                            </div>
                                            <iframe src="https://maps.google.com/maps?q={{ $listing->location->lat }},{{ $listing->location->long }}&hl=en&z=14&amp;output=embed"
                                                    width="100%" height="500" frameborder="0"
                                                    style="border:0"
                                                    allowfullscreen></iframe>
                                        </div>
                                        <div id="listar-reviews" class="listar-reviews">
                                            @if(\Settings::get('directory_rating_enable',true))
                                                @include('partials.rating_form')
                                            @endif
                                            @if(!($ratings = $listing->ratings('approved')->get())->isEmpty())
                                                <div class="listar-title">
                                                    <h3>@lang('corals-directory-listing-star::labels.template.product_single.reviews',['count'=> $ratings->count() ])</h3>
                                                </div>
                                                @foreach($ratings as $review)
                                                    @include('partials.rating_single',['review'=> $review ,'show_name'=>false,'show_status'=>false  ])
                                                @endforeach
                                            @endif
                                        </div>
                                        <div id="listar-gallery">
                                            <div role="tabpanel" class="tab-pane active" id="gallery">
                                                <div id="listar-postgallery" class="listar-postgallery">
                                                    @foreach($listing->getMedia($listing->galleryMediaCollection) as $img)
                                                        <div class="listar-masnory">
                                                            <figure><a href="{{$img->getUrl() }}"
                                                                       data-rel="prettyPhoto[gallery]"
                                                                       rel="prettyPhoto[gallery]"><img
                                                                            src="{{$img->getUrl()}}"
                                                                            alt="{{$listing->name}}"></a></figure>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <aside id="listar-stickysidebar"
                                   class="listar-sidebar listar-stickysidebar">
                                <div class="sidebar__inner">
                                    <div class="listar-widget listar-widgetsocialicon">
                                        <div class="listar-widgetcontent">
                                            @if($listing->getProperty('social') || $listing->getProperty('contact_info'))
                                                <ul class="listar-socialicons listar-socialiconsborder">
                                                    @if($listing->getProperty('social.facebook'))
                                                        <li class="listar-facebook">
                                                            <a href="{{ $listing->getProperty('social.facebook')  }}"
                                                               target="_blank"><i
                                                                        class="fa fa-facebook"></i></a></li>
                                                    @endif
                                                    @if($listing->getProperty('social.twitter'))
                                                        <li class="listar-twitter"><a
                                                                    href="{{ $listing->getProperty('social.twitter')  }}"
                                                                    target="_blank"><i
                                                                        class="fa fa-twitter"></i></a></li>
                                                    @endif
                                                    @if($listing->getProperty('contact_info.phone_number') )
                                                        <li class="listar-vimeo">
                                                            <a href="mailto:{{$listing->getProperty('contact_info.phone_number') }}"><i
                                                                        class="icon-telephone114"></i></a>
                                                        </li>
                                                    @endif
                                                    @if($listing->getProperty('contact_info.email'))
                                                        <li class="listar-vimeo">
                                                            <a href="mailto:{{$listing->getProperty('contact_info.email') }}"><i
                                                                        class="icon-email"></i></a>
                                                        </li>
                                                    @endif
                                                    @if($listing->getProperty('contact_info.whatsapp'))
                                                        <li class="listar-vimeo">
                                                            <a href="mailto:{{$listing->getProperty('contact_info.whatsapp') }}"><i
                                                                        class="icon-whatsapp"></i></a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="listar-widget listar-widgetopeninghours">
                                        <div class="listar-widgettitle">
                                            <h3 class="custom-width">
                                                <i class="icon-alarmclock"></i>
                                                <span>@lang('corals-directory-listing-star::labels.template.product_single.working_hours')
                                                    : </span>
                                            </h3>
                                            <p class="{{$listing->isOpen()?'':'status-closed'}}"><i
                                                        class="icon-alarmclock"></i>
                                                <span class="{{$listing->isOpen()?'current-status':'status-closed'}}">{{$listing->isOpen()?'Now Open':'Closed'}}</span>
                                            </p>
                                        </div>
                                        <div class="listar-widgetcontent">
                                            <ul class="listar-openinghours">
                                                @foreach($schdule as $key =>$day)
                                                    <li>
                                                        <span>{{$key}}</span>
                                                        <span class="{{$day['start']=='Off'?'status-closed':''}}">{{$day['start']=='Off'?'Off':date("g:i a", strtotime($day['start'].":00"))}}
                                                            {{ $day['end']=='Off'?'':'- '.date("g:i a", strtotime($day['end'].":00"))}}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="listar-widget">
                                        <div class="listar-widgettitle">
                                            <h3 class="d-flex">
                                                <i class="icon-trophy"></i>
                                                <span>@lang('corals-directory-listing-star::labels.template.product_single.contact_with',['name' => optional($listing->user)->full_name ] )</span>
                                            </h3>
                                        </div>
                                        @if(!empty($listing->user_id))
                                            <div class="listar-widgetcontent">
                                                @if(\Settings::get('directory_messaging_is_enable',true) && (!user() || user()->can('create', Corals\Modules\Messaging\Models\Discussion::class)))
                                                    <a class="listar-btnclainnow"
                                                       href="{{ url('messaging/discussions/create?user='.$listing->user->hashed_id) }}">@lang('corals-directory-listing-star::labels.template.product_single.send_message')
                                                        <i class="fa fa-angle-right"></i></a>
                                                @else
                                                    <form class="add-comment custom-form ajax-form"
                                                          id="main-contact-form"
                                                          name="contact-form"
                                                          action="{{url('directory/listings/contact')}}"
                                                          method="POST"
                                                          data-page_action="clearForm">
                                                        <fieldset>
                                                            <input type="hidden"
                                                                   value="{{csrf_token()}}"
                                                                   name="_token">
                                                            <input type="hidden"
                                                                   value="{{$listing->name}}"
                                                                   name="listing_name">
                                                            <input type="hidden"
                                                                   value="{{$listing->getProperty('contact_info.email') }}"
                                                                   name="listing_email">
                                                            <label><i class="fa fa-user-o"></i></label>
                                                            <div class="form-group">
                                                                <input type="text"
                                                                       placeholder="@lang('corals-directory-basic::labels.template.product_single.your_name') *"
                                                                       value="" name="name"/>

                                                            </div>
                                                            <div class="clearfix"></div>
                                                            <label><i class="fa fa-envelope-o"></i>
                                                            </label>
                                                            <div class="form-group">
                                                                <input type="text"
                                                                       placeholder="@lang('corals-directory-basic::labels.template.product_single.your_email')*"
                                                                       value="" name="email"/>
                                                            </div>

                                                            <div class="form-group">
                                                     <textarea cols="40" rows="3"
                                                               placeholder="@lang('corals-directory-basic::labels.template.product_single.additional_information'):"
                                                               name="message"></textarea>
                                                            </div>

                                                        </fieldset>
                                                        <button type="submit"
                                                                class="listar-btn listar-btngreen">@lang('corals-directory-listing-star::labels.template.product_single.send_message')
                                                        </button>
                                                        </fieldset>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    @if($listing->user)
                                        <div class="listar-widget listar-widgetformauthor">
                                            <div class="listar-widgetcontent">
                                                <div class="listar-authorinfo">
                                                    <figure><a href="javascript:void(0);"><img
                                                                    src="{{$listing->user->picture_thumb}}"
                                                                    style="max-width: 61px"
                                                                    alt="image description"></a>
                                                    </figure>
                                                    <div class="listar-authorcontent">
                                                        <h4>{{$listing->user->full_name}}</h4>
                                                        <ul style="padding-top: 20px">
                                                            <li>
                                                <span><i class="fa fa-map-marker"></i> @lang('corals-directory-listing-star::labels.template.product_single.address')
                                                    :</span> <a>{{$listing->address }}</a></li>
                                                            @if($listing->getProperty('contact_info.phone_number'))
                                                                <li>
                                                <span><i class="fa fa-phone"></i> @lang('corals-directory-listing-star::labels.template.product_single.phone')
                                                    :</span>
                                                                    <a>{{$listing->getProperty('contact_info.phone_number')}}</a>
                                                                </li>
                                                            @endif
                                                            @if($listing->getProperty('contact_info.email'))
                                                                <li>
                                                <span><i class="fa fa-envelope-o"></i> @lang('corals-directory-listing-star::labels.template.product_single.email')
                                                    :</span> <a
                                                                            href="mailto:{{$listing->getProperty('contact_info.email') }}">{{$listing->getProperty('contact_info.email') }}</a>
                                                                </li>
                                                            @endif

                                                            @if($listing->website)
                                                                <li>
                                                <span><i class="fa fa-globe"></i> @lang('corals-directory-listing-star::labels.template.product_single.website')
                                                    :</span> <a target="_blank"
                                                                href="{{$listing->website}}">{{$listing->website}}</a>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                        <ul class="listar-socialicons listar-socialiconsborder"
                                                            style="padding-top: 20px">
                                                            @if($listing->getProperty('social.facebook'))
                                                                <li class="listar-facebook">
                                                                    <a href="{{ $listing->getProperty('social.facebook')  }}"
                                                                       target="_blank"><i
                                                                                class="fa fa-facebook"></i></a></li>
                                                            @endif
                                                            @if($listing->getProperty('social.twitter'))
                                                                <li class="listar-twitter"><a
                                                                            href="{{ $listing->getProperty('social.twitter')  }}"
                                                                            target="_blank"><i
                                                                                class="fa fa-twitter"></i></a></li>
                                                            @endif

                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </aside>
                        </div>
                    </div>
                </div>
            </div>
            <section class="listar-sectionspace listar-bglight listar-listingvtwo listar-haslayout">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="listar-title">
                                <h3>@lang('corals-directory-listing-star::labels.template.product_single.amenities')</h3>
                            </div>
                            @include('partials.featured_listing')
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <div class="modal fade listar-placequickview" tabindex="-1" role="dialog"
         id="listing_claim-{{$listing->hashed_id}}">
        <div class="modal-dialog listar-modaldialog" role="document">
            <div class="modal-content listar-modalcontent" id="modal-content">
                <div class="listar-themepost listar-placespost">
                <span class="listar-btnclosequickview" data-toggle="modal"
                      data-target="#listing_claim-{{$listing->hashed_id}}">X</span>
                    <div class="" style="padding:20px ">
                        {!! Form::open( ['url' => url('directory/user/'.$listing->hashed_id.'/claim'),'method'=>'POST', 'class'=>'ajax-form','id'=>'claim-form','data-page_action'=>"closeModal", "files"=>true]) !!}

                        {!! CoralsForm::textarea('brief_description','Directory::attributes.claim.brief_description',true, '', ['rows'=>5 , 'class'=>'custom-radius']) !!}
                        {!! CoralsForm::file('claim_file', 'Directory::attributes.claim.proof_of_business_registration',false,[ 'class'=>'custom-radius']) !!}

                        <button type="submit" style="color: #FFFFFF;"
                                class="btn big-btn listar-btngreen flat-btn">@lang('Directory::attributes.claim.send_claim')
                            <i class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
