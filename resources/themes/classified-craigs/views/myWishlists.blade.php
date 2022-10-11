@extends('layouts.master')

@section('title', $title)

@section('page_header')
    @include('partials.page_header', ['content'=> '<h2 class="product-title">'. $title .'</h2>'])
@endsection

@section('content')
    <div class="section-title clearfix">
        <div class="float-left float-xs-none">
            <h2><b>@lang('corals-classified-craigs::labels.product.my_wishlists')</b></h2>

        </div>
        <div class="float-right d-xs-none thumbnail-toggle">
            <a href="#" class="change-class" data-change-from-class="list" data-change-to-class="grid"
               data-parent-class="items">
                <i class="fa fa-th"></i>
            </a>
            <a href="#" class="change-class active" data-change-from-class="grid" data-change-to-class="list"
               data-parent-class="items">
                <i class="fa fa-th-list"></i>
            </a>
        </div>
    </div>
    <!--============ Items ==========================================================================-->
    <div class="items list compact grid-xl-3-items grid-lg-3-items grid-md-2-items">
        @forelse($wishlists as $wishlist)
            <div class="item" id="{{'row_'.$wishlist->hashed_id}}">
                <div class="ribbon-vertical">
                    <i class="fa fa-star"></i>
                </div>
                <!--end ribbon-vertical-->
                <div class="wrapper">
                    <div class="image">
                        <h3>
                            @foreach($wishlist->wishlistable->activeCategories as $category)
                                <a href="#"
                                   class="tag category">{!! $category->name  !!}</a>
                            @endforeach
                            <a href="{{url('products/'.$wishlist->wishlistable->slug)}}" class="title">
                                {!!  \Str::limit(strip_tags($wishlist->wishlistable->name),40) !!}
                            </a>
                            <span class="tag">{!!  $wishlist->wishlistable->status !!}</span>
                        </h3>
                        <a href="#" class="image-wrapper background-image">
                            <img src="{!!$wishlist->wishlistable->image !!}" alt="">
                        </a>
                    </div>
                    <!--end image-->
                    <h4 class="location">
                        <a href="#">{!! $wishlist->wishlistable->present('location') !!}</a>
                    </h4>
                    <div class="price">{!! $wishlist->wishlistable->price !!}</div>
                    <div class="admin-controls">
                        <a href="{{url('products/'.$wishlist->wishlistable->slug)}}" class="ad-hide">
                            <i class="fa fa-eye"></i>show
                        </a>
                        <a href="{{url('utilities/wishlist/'.$wishlist->hashed_id)}}"
                           data-action="delete"
                           data-page_action="removeRow" data-action_data="{{$wishlist->hashed_id}}" class="ad-remove">
                            <i class="fa fa-trash"></i>Remove
                        </a>
                    </div>
                    <!--end meta-->
                    <div class="description">
                        <p>{!! $wishlist->wishlistable->present('caption') !!}</p>
                    </div>
                    <!--end description-->
                    <a href="{{url('products/'.$wishlist->wishlistable->slug)}}" class="detail text-caps underline">@lang('corals-classified-craigs::labels.product.detail')</a>
                </div>
            </div>
        @empty
            <h3><b>@lang('corals-classified-craigs::labels.product.sorry_no_show')</b></h3>
        @endforelse
    </div>
    <!--end items-->
    <!--============ End Items ==============================================================-->
    {!! $wishlists->links('partials.paginator') !!}
    <!--end page-pagination-->
@endsection

@section('js')
    @parent
    <script type="text/javascript">

        function removeRow(respnse, $form, hashed_id) {
            $("#row_" + hashed_id).fadeOut();
        }
    </script>
@endsection