@extends('layouts.public')

@section('page_header')

    @include('partials.page_header', ['price'=> $product->present('price') ,'location' => $product->getLocation() ,'title'=>$product->name])

@endsection

@section('content')
    <section class="content">
        <section class="block">
            <div class="container">
                <div class="row">
                    <div class="col-md-9">

                        <section>
                            <h2>@lang('corals-classified-craigs::labels.template.product.description')</h2>
                            <p>
                                @if(!empty($product->description))
                                    {!! $product->description !!}
                                @else
                                    @lang('corals-classified-craigs::labels.product.not_available')
                                @endif
                            </p>
                        </section>
                        <section>

                            @if(!empty($product->user->phone))
                                <a href="tel:{{ $product->user->phone }}" class="btn btn-common call">
                                    <i class="fa fa-phone" aria-hidden="true"></i>
                                    <span class="phonenumber">{{$product->user->phone}}</span></a>
                            @endif
                            <div class="share">
                                <span>@lang('corals-classified-craigs::labels.template.product.share')</span>
                                @include('partials.components.social_share',['url'=> URL::current() , 'title'=>$product->name ])
                            </div>
                        </section>
                        <section>
                            @php $productUser = getUserByHash(\Request::get('user')) @endphp
                            <div class="gallery-carousel owl-carousel">
                                @forelse($product->getMedia($product->galleryMediaCollection) as $img)
                                    <img src="{{$img->getUrl() }}" alt="{{ $product->name }}"
                                         data-hash="{{$img->getUrl()}}">
                                @empty
                                    <img src="{{ $product->image }}"
                                         class="img-fluid"
                                         alt="{{ $product->name }}"/>
                                @endforelse
                            </div>
                            <div class="gallery-carousel-thumbs owl-carousel">
                                @foreach($product->getMedia($product->galleryMediaCollection) as $img)
                                    <a href="#{{$img->getUrl()}}" class="owl-thumb active-thumb background-image">
                                        <img src="{{ $img->getUrl() }}" alt="{{ $product->name }}">
                                    </a>
                                @endforeach
                            </div>
                            <h3 style="margin-top: 14px"> @if(\Settings::get('classified_messaging_is_enable',true) && (!user() || user()->can('create', Corals\Modules\Messaging\Models\Discussion::class)))
                                    <a href="{{ url('messaging/discussions/create?user='.$product->user->hashed_id) }}"
                                       class="btn btn-primary text-caps btn-rounded btn-framed"><i
                                                class="fa fa-envelope"></i></a>
                                @else
                                    <a href="mailto:{{$product->user->email}}"
                                       class="btn btn-primary text-caps btn-rounded btn-framed"><i
                                                class="fa fa-envelope"></i></a>
                                @endif
                                <a class="btn btn-primary text-caps btn-rounded btn-framed" href="#" data-toggle="modal"
                                   data-target="#ProductRefertModal"><i class="fa fa-user-plus"></i>
                                    @lang('corals-classified-craigs::labels.template.product.friend_send')
                                </a>

                                <a href="#" data-toggle="modal" data-target="#ProductReportModal"
                                   class="btn btn-primary text-caps btn-rounded btn-framed"><i
                                            class="fa fa-warning"></i> @lang('corals-classified-craigs::labels.template.product.report')
                                </a>

                                <div class="wishlist">
                                    @if(\Settings::get('classified_wishlist_enable',true))
                                        @include('partials.components.wishlist',['wishlist'=> $product->inWishList() ])
                                    @endif
                                </div>
                            </h3>
                        </section>
                        @include('partials.refer_modal')
                        @include('partials.report_modal',['product' => $product])

                        <section>
                            <div class="row">
                                <div class="col-md-4">
                                    <h2>@lang('corals-classified-craigs::labels.template.product.detail_product')</h2>
                                    <dl>
                                        <dt>@lang('corals-classified-craigs::labels.template.product.date_posted')</dt>
                                        <dd>{!! $product->created_at->diffForHumans() !!}</dd>
                                        <dt>@lang('Corals::attributes.status')</dt>
                                        <dd>{!! $product->present('status') !!}</dd>
                                        @if(\Settings::get('classified_year_model_visible'))
                                            <dt>@lang('corals-classified-craigs::labels.template.product.year_model')</dt>
                                            <dd>{!! $product->present('year_model') ?? " - " !!}</dd>
                                        @endif
                                        <dt>@lang('corals-classified-craigs::labels.template.home.locations')</dt>
                                        <dd>{!! $product->present('location') !!}</dd>
                                        <dt>@lang('corals-classified-craigs::labels.template.product.condition')</dt>
                                        <dd>{!! $product->condition ?? ' N/A' !!}</dd>
                                        <dt>@lang('corals-classified-craigs::labels.product.price')</dt>
                                        <dd>{!! $product->present('price') !!}</dd>
                                        <dt>@lang('corals-classified-craigs::labels.template.home.city')</dt>
                                        <dd>{!! optional($product->location)->city !!}</dd>
                                        <dt>@lang('corals-classified-craigs::labels.template.home.country')</dt>
                                        <dd>{!! optional($product->location)->country !!}</dd>
                                    </dl>
                                </div>
                                <div class="col-md-8">
                                    <h2>@lang('corals-classified-craigs::labels.template.product.item_location')</h2>
                                    <div class="map height-300px" id="map-small">

                                        <iframe src="https://maps.google.com/maps?q={{ $product->location->lat }},{{ $product->location->long }}&hl=en&z=14&amp;output=embed"
                                                width="100%" height="350" frameborder="0" style="border:0"
                                                allowfullscreen></iframe>

                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="col-md-3">
                        <aside class="sidebar">
                            <section>
                                <h2>@lang('corals-classified-craigs::labels.partial.similar_ads')</h2>
                                <div class="items compact">
                                    @php
                                        $category = \Corals\Modules\Utility\Models\Category\Category::query()->active()->pluck('slug')->toArray();
                                    @endphp
                                    @foreach(\Classified::getRandomProductForCategory($category , 2) as $product)
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
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </section>
                            @include('partials.product_filter')
                        </aside>
                    </div>
                </div>
            </div>
        </section>
    </section>
@endsection

@section('js')

@endsection
