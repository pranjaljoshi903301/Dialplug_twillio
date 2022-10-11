@extends('layouts.master')

@section('title', $title)

@section('page_header')
    @include('partials.page_header',['content'=> '<h2 class="product-title">'. $title .'</h2>'])
@endsection

@section('content')
    <section class="block">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <div class="feature-box">
                        <figure>
                            <i class="fa fa-pencil"></i>
                            <span>{{\Classified::getProductsCount(true)}}</span>
                        </figure>
                        <h3><a href="{{url('classified/user/products?status=')}}">@lang('corals-classified-craigs::labels.product.total_products_posted')</a></h3>
                    </div>
                    <!--end feature-box-->
                </div>
                <div class="col text-center">
                    <div class="feature-box">
                        <figure>
                            <i class="fa fa-pencil"></i>
                            <span>{{\Classified::getFeaturedProductsCount(true)}}</span>
                        </figure>
                        <h3><a href="{{url('classified/user/products?status=featured')}}">@lang('corals-classified-craigs::labels.product.featured_products')</a></h3>
                    </div>
                    <!--end feature-box-->
                </div>
                <div class="col text-center">
                    <div class="feature-box">
                        <figure>
                            <i class="fa fa-pencil"></i>
                            <span>{{\Classified::getArchivedProductsCount(true)}}</span>
                        </figure>
                        <h3><a href="{{url('classified/user/products?status=archived')}}">@lang('corals-classified-craigs::labels.product.archived_products')</a></h3>
                    </div>
                    <!--end feature-box-->
                </div>
                <div class="col text-center">
                    <div class="feature-box">
                        <figure>
                            <i class="fa fa-pencil"></i>
                            <span>{{\Classified::getSoldProductsCount(true)}}</span>
                        </figure>
                        <h3><a href="{{url('classified/user/products?status=sold')}}">@lang('corals-classified-craigs::labels.product.sold_products')</a></h3>
                    </div>
                    <!--end feature-box-->
                </div>
                <div class="col text-center">
                    <div class="feature-box">
                        <figure>
                            <i class="fa fa-pencil"></i>
                            <span>{{\Classified::getMyWishlistsCount()}}</span>
                        </figure>
                        <h3><a href="{{url('classified/wishlist/my')}}">@lang('corals-classified-craigs::labels.product.my_wishlist_products')</a></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="background"></div>
    </section>
@endsection