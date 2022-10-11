@extends('layouts.public')

@section('page_header')
    @include('partials.page_header')
@endsection
@section('content')
    <section class="content">
        <section class="block">
            <div class="container">
                @php $productUser = getUserByHash(\Request::get('user')) @endphp
                @if($productUser)
                    @include('partials.user_details_header',compact('productUser'))
                @endif
                <div class="row flex-column-reverse flex-md-row">
                    <div class="col-md-9">
                        <!--============ Section Title===================================================================-->
                        <div class="section-title clearfix">
                            <div class="float-left float-xs-none">
                                <label class="mr-3 align-text-bottom">@lang('corals-classified-craigs::labels.template.product.sort_by')</label>
                                <select name="sorting" id="product_sort" class="small width-200px">
                                    <option disabled="disabled"
                                            selected>@lang('corals-classified-craigs::labels.template.product.select_option')</option>
                                    @foreach($sortOptions as $value => $text)
                                        <option value="{{ $value }}" {{ request()->get('sort') == $value?'selected':'' }}>
                                            {{ $text }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="float-right float-xs-none d-xs-none thumbnail-toggle">
                                <a href="#" class="change-class active" data-change-from-class="list"
                                   data-change-to-class="grid" data-parent-class="items">
                                    <i class="fa fa-th"></i>
                                </a>
                                <a href="#" class="change-class" data-change-from-class="grid"
                                   data-change-to-class="list" data-parent-class="items">
                                    <i class="fa fa-th-list"></i>
                                </a>
                            </div>
                        </div>
                        <!--============ Items ==========================================================================-->
                        <div class="items grid grid-xl-3-items grid-lg-3-items grid-md-2-items">
                            @forelse($products as $product)
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
                                                   class="title">{!!  \Str::limit(strip_tags($product->name),40) !!}
                                                </a>
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
                                            <a href="{{url('products?location='.$product->getLocationSlug()) }}">{!! \Str::limit( $product->getLocation() , 25)  !!}</a>
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
                                        <a href="{{url("products/".$product->slug .'?user='.$product->user->hashed_id)}}"
                                           class="detail text-caps underline">@lang('corals-classified-craigs::labels.product.detail')</a>
                                        @if($product->verified)
                                            <a class="detail-right text-caps underline">@lang('corals-classified-craigs::labels.partial.verified')
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <h4>@lang('corals-classified-craigs::labels.template.product.sorry_no_result')</h4>
                            @endforelse
                        </div>
                        <!--============ End Items ==============================================================-->
                    {{ $products->appends(request()->except('page'))->links('partials.paginator') }}
                    <!--end page-pagination-->
                    </div>
                    <!--end col-md-9-->
                    <div class="col-md-3">
                        <!--============ Side Bar ===============================================================-->
                        <aside class="sidebar">
                            @include('partials.product_filter')
                        </aside>
                        <!--============ End Side Bar ===========================================================-->
                    </div>
                    <!--end col-md-3-->
                </div>
            </div>
            <!--end container-->
        </section>
        <!--end block-->
    </section>
    <!--end content-->
@endsection

@section('js')
    @parent

    <script type="text/javascript">
        $(document).ready(function () {
            $("#product_sort").change(function () {
                $("#filterSort").val($(this).val());

                $("#filterForm").submit();
            });

            let hash = window.location.hash;

            if (hash.length) {

                let hasURLParameters = hash.indexOf('?');//-1 if not exist
                let indexOfHash = hash.indexOf('#');

                if (hasURLParameters && hasURLParameters > indexOfHash) {
                    hash = _.split(hash, '?')[0];
                }

                $('.pagination .page-link').each(function () {
                    let href = $(this).prop('href');
                    href = _.trim(href, '#');
                    $(this).prop('href', href + hash);
                });
            }

            $('a.nav-link').on('shown.bs.tab', function (e) {
                let href = $(this).prop('href');

                let hash = '';

                let indexOfHash = href.indexOf('#');

                if (indexOfHash) {
                    hash = '#' + _.split(href, '#')[1];

                    $('.pagination .page-link').each(function () {
                        let phref = $(this).prop('href');

                        phref = _.trim(phref, '#');

                        phref = _.split(phref, '#')[0];

                        $(this).prop('href', phref + hash);
                    });
                }
            })
        });
    </script>
@endsection