@foreach(\Classified::getProductsList(true, 8) as $product)
    <div class="item">
        @if($product->is_featured)
            <div class="ribbon-featured">
                <a href="#">@lang('corals-classified-craigs::labels.partial.is_featured')</a>
            </div>
        @elseif($product->condition=='new')
            <div class="ribbon-featured">
                <a href="{{url('products?condition='.$product->condition)}}">@lang('corals-classified-craigs::labels.product.new')</a>
            </div>
        @endif
        @if(\Settings::get('classified_wishlist_enable',true))
            @include('partials.components.wishlist',['wishlist'=> $product->inWishList() ])
        @endif
        <div class="wrapper">
            <div class="image">
                <h3>
                    @foreach($product->activeCategories as $category)
                        <a href="{{url('products?category='.$category->slug)}}"
                           class="tag category">{{ $category->name }}</a>
                    @endforeach
                    <a href="{{url("products/".$product->slug)}}"
                       class="title">{!! $product->name !!}</a>

                    @foreach($product->activeTags as $tag)
                        <span class="tag">{{$tag->name}}</span>
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
            <div class="meta">
                <figure>
                    <i class="fa fa-calendar-o"></i>
                    {!! $product->created_at->diffForHumans() !!}
                </figure>
                <figure>
                    <a href="{{url('products?user='.$product->user->hashed_id)}}">
                        <i class="fa fa-user"></i>{!! $product->getStoreName() !!}
                    </a>
                </figure>
            </div>
            <!--end meta-->
            <div class="description">
                <p>{!! \Str::limit(strip_tags($product->description),80) !!}</p>
            </div>
            <!--end description-->
            <a href="{{url("products/".$product->slug)}}"
               class="detail text-caps underline">@lang('corals-classified-craigs::labels.product.detail')</a>
            @if($product->verified)
                <a class="detail-right text-caps underline">@lang('corals-classified-craigs::labels.partial.verified')
                </a>
            @endif
        </div>
    </div>
@endforeach