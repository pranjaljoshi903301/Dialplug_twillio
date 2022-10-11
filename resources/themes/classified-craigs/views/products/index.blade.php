@extends('layouts.master')

@section('title', $title)

@section('page_header')
    @include('partials.page_header', ['content'=> '<h2 class="product-title">'. $title .'</h2>'])
@endsection

@section('content')
    <div class="section-title clearfix">
        <div class="float-left float-xs-none">
            <nav class="nav-table">
                <ul>
                    <li class="{{checkActiveKey('','status')?'active':''}}"><a
                                class="btn btn-sm" href="{{url('classified/user/products?status=')}}">
                            @lang('corals-classified-craigs::labels.partial.all_products')
                            ({{\Classified::getProductsCount(true)}})</a></li>
                    <li class="{{checkActiveKey('active','status')?'active':''}}"><a
                                class="btn btn-sm" href="{{url('classified/user/products?status=active')}}">@lang('corals-classified-craigs::labels.product.active')
                            ({{\Classified::getActiveProductsCount(false,true)}})</a></li>
                    <li class="{{checkActiveKey('sold','status')?'active':''}}"><a
                                class="btn btn-sm" href="{{url('classified/user/products?status=sold')}}">@lang('corals-classified-craigs::labels.product.sold')
                            ({{\Classified::getSoldProductsCount(true)}})</a></li>
                    <li class="{{checkActiveKey('featured','status')?'active':''}}"><a
                                class="btn btn-sm"
                                href="{{url('classified/user/products?status=featured')}}">@lang('corals-classified-craigs::labels.partial.is_featured')
                            ({{\Classified::getFeaturedProductsCount(true)}})</a></li>
                    <li class="{{checkActiveKey('archived','status')?'active':''}}"><a
                                class="btn btn-sm"
                                href="{{url('classified/user/products?status=archived')}}">@lang('corals-classified-craigs::labels.product.archived')
                            ({{\Classified::getArchivedProductsCount(true)}})</a></li>
                </ul>
            </nav>
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
    <div class="items list compact grid-xl-3-items grid-lg-2-items grid-md-2-items">
        @forelse($products as $product)
            <div class="item" id="{{'row_'.$product->hashed_id}}">
                @if($product->is_featured)
                    <div class="ribbon-featured">
                        <a href="#">@lang('corals-classified-craigs::labels.partial.is_featured')</a>
                    </div>
                @elseif($product->condition=='new')
                    <div class="ribbon-featured">
                        <a href="{{url('products?condition='.$product->condition)}}">@lang('corals-classified-craigs::labels.product.new')</a>
                    </div>
                @endif
                <div class="wrapper">
                    <div class="image">
                        <h3>
                            @foreach($product->activeCategories as $category)
                                <a href="#" class="tag category">{{ $category->name }}</a>
                            @endforeach
                            <a href="#" class="title">{!!  \Str::limit(strip_tags($product->name),40) !!}</a>
                            @foreach($product->activeTags as $tag)
                                <span class="tag">{!! $tag->name !!}</span>
                            @endforeach
                        </h3>
                        <a href="{{url("products/".$product->slug)}}"
                           class="image-wrapper background-image">
                            <img src="{{$product->image}}" alt="{{ $product->name }}">
                        </a>
                    </div>
                    <!--end image-->
                    <h4 class="location">
                        <a href="{{url('products?location='.$product->getLocationSlug()) }}">{!! $product->getLocation() !!}</a>
                    </h4>
                    <div class="price">{!! $product->present('price') !!}</div>
                    <div class="admin-controls">
                        <a href="{{url('classified/user/products/'.$product->hashed_id.'/edit')}}">
                            <i class="fa fa-pencil"></i>Edit
                        </a>
                        <a href="{{url('products/'.$product->slug)}}" class="ad-hide">
                            <i class="fa fa-eye"></i>Show
                        </a>
                        <a href="{{url('classified/user/products/'.$product->hashed_id)}}" class="ad-remove"
                           data-action="delete"
                           data-page_action="removeRow" data-action_data="{{ $product->hashed_id }}">
                            <i class="fa fa-trash"></i>Remove
                        </a>
                    </div>
                    <!--end admin-controls-->
                    <div class="description">
                        <p>{!! \Str::limit(strip_tags($product->description),80) !!}</p>
                    </div>
                    <!--end description-->
                    <a href="{{url('products/'.$product->slug)}}" class="detail text-caps underline">@lang('corals-classified-craigs::labels.product.detail')</a>
                </div>
            </div>
        @empty
            <div class="item">
                <label class="ml-5 align-text-bottom">
                    <h2><b>@lang('corals-classified-craigs::labels.product.sorry_no_show')</b></h2>
                </label>
            </div>
        @endforelse
    </div>
    {{ $products->appends(request()->except('page'))->links('partials.paginator') }}
    <!--end items-->
@endsection

@section('js')
    @parent
    <script type="text/javascript">
        function removeRow(response, $form, hashed_id) {
            $("#row_" + hashed_id).fadeOut();
        }
    </script>
@endsection