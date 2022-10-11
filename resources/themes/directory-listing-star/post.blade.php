@extends('layouts.public')


@section('content')
    <main id="listar-main" class="listar-main listar-haslayout">
        <div id="listar-twocolumns" class="listar-twocolumns">
            <div class="listar-themepost listar-post listar-detail listar-postdetail">
                <figure class="listar-featuredimg">
                    @if($featured_image)
                        <div class="bg custom-background-post" data-bg="{{$featured_image}}"
                             data-scrollax="properties: { translateY: '30%' }"></div>
                    @endif
                    <figcaption>
                        <div class="container">
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="listar-postcontent">
                                        <div class="listar-postauthordpname">
                                            <span class="listar-postauthordp"><a href="javascript:void(0);"><img
                                                            src="{{$item->post->author->picture_thumb}}" alt=""
                                                            style="max-width: 35px"></a></span>
                                            <span class="listar-postauhorname"><a
                                                        href="javascript:void(0);">{{$item->post->author->full_name}}</a></span>
                                        </div>
                                        <time datetime="2017-08-08">
                                            <i class="icon-clock4"></i>
                                            <span>{{format_date($item->published_at)}}</span>
                                        </time>
                                        <span class="listar-postcomment">
                                            @foreach($item->post->activeCategories as $category)
                                                <a href="{{ url('category/'.$category->slug) }}">
                                                    &nbsp;<span class="custom-span">{{ $category->name }}</span>
                                                </a>
                                            @endforeach
											</span>
                                        <div class="listar-btnquickinfo">
                                            <a class="listar-btnshare" href="javascript:void(0);">
                                                @if(count($activeTags = $item->post->activeTags))
                                                    @foreach($activeTags as $tag)
                                                        <a href="{{ url('tag/'.$tag->slug) }}"><span
                                                                    class="custom-span">{{ $tag->name }}</span></a>
                                                        ,
                                                    @endforeach
                                                @endif
                                            </a>
                                        </div>
                                        <h1>{{$item->title}}</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </figcaption>
                </figure>
                <div class="clearfix"></div>
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-push-1 col-md-10 col-lg-push-1 col-lg-10">
                            <div id="listar-detailcontent" class="listar-detailcontent">
                                <div class="listar-description">
                                    <p>{!! $item->rendered !!}</p>
                                </div>
                                <div class="listar-description">
                                    <p>@lang('corals-directory-listing-star::labels.partial.categories') :</p>
                                    @foreach($item->post->activeCategories as $category)
                                        <a href="{{ url('category/'.$category->slug) }}">
                                            &nbsp;{{ $category->name }}
                                        </a>,&nbsp;
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection