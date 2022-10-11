@extends('layouts.master')

@section('title',trans('corals-directory-listing-star::labels.partial.dashboard'))
@section('content')
    @php
        $wishlistManager = new Corals\Modules\Utility\Classes\Wishlist\WishlistManager(new \Corals\Modules\Directory\Models\Listing);
        $wishlistCount = $wishlistManager->getUserWishlist(true);
    @endphp

    <main id="listar-main" class="listar-main listar-haslayout">
        <div class="listar-dashboardbanner">
            <ol class="listar-breadcrumb">
                <li><a href="javascript:void(0);">@lang('corals-directory-listing-star::labels.dashboard.hello')</a>
                </li>
                <li class="listar-active">{{user()->name}}</li>
            </ol>
            <h1>@lang('corals-directory-listing-star::labels.dashboard.title')</h1>
        </div>
        <div id="listar-content" class="listar-content">
            <form class="listar-formtheme listar-formaddlisting listar-formdashboard">
                <div class="row">
                    <fieldset class="listar-statisticsarea">
                        <ul class="listar-statistics">
                            <li class="listar-activelists">
                                <div class="listar-couterholder">
                                    <h3 data-from="0"
                                        data-to="{{\Corals\Modules\Directory\Facades\Directory::getListingsCount('active',user()->id)}}"
                                        data-speed="1000"
                                        data-refresh-interval="50">{{\Corals\Modules\Directory\Facades\Directory::getListingsCount('active',user()->id)}}</h3>
                                    <h4>@lang('corals-directory-listing-star::labels.dashboard.active_listings')</h4>
                                    <div class="listar-statisticicon"><i class="icon-map3"></i></div>
                                </div>
                            </li>
                            <li class="listar-newuser">
                                <div class="listar-couterholder">
                                    <h3 data-from="0" data-to="{{$wishlistCount}}" data-speed="1000">
                                        {{$wishlistCount}}</h3>
                                    <h4>@lang('corals-directory-listing-star::labels.dashboard.times_bookmarked')</h4>
                                    <div class="listar-statisticicon"><i class="icon-user2"></i></div>
                                </div>
                            </li>
                            <li class="listar-weeksviews">
                                <div class="listar-couterholder">
                                    <h3 data-from="0"
                                        data-to="{{\Corals\Modules\Directory\Facades\Directory::getListingsVisitorsCount('active',user()->id)}}"
                                        data-speed="1000" data-refresh-interval="50">
                                        {{\Corals\Modules\Directory\Facades\Directory::getListingsVisitorsCount('active',user()->id)}}</h3>
                                    <h4>@lang('corals-directory-listing-star::labels.dashboard.listing_views')</h4>
                                    <div class="listar-statisticicon"><i class="icon-linegraph"></i></div>
                                </div>
                            </li>
                            <li class="listar-newfeedback">
                                <div class="listar-couterholder">
                                    <h3 data-from="0"
                                        data-to="{{\Corals\Modules\Directory\Facades\Directory::getUserListingReviewsCount(user()->id)}}"
                                        data-speed="1000" data-refresh-interval="50">
                                        {{\Corals\Modules\Directory\Facades\Directory::getUserListingReviewsCount(user()->id)}}</h3>
                                    <h4>@lang('corals-directory-listing-star::labels.dashboard.total_reviews')</h4>
                                    <div class="listar-statisticicon"><i class="icon-bubble3"></i></div>
                                </div>
                            </li>
                        </ul>
                    </fieldset>
                </div>
                <div class="row ">
                    <div class="col-md-12 m-t-20">
                        <div class="listar-postvisit">
                            <h3>@lang('corals-directory-listing-star::labels.dashboard.my_listings')</h3>
                            <div class="listar-dashboardwishlists tab-content">
                                <div role="tabpanel" class="tab-pane active" id="home">
                                    @include('partials.my_listings')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>
@endsection
