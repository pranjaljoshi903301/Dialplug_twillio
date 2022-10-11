@extends('layouts.public')

@section('title','Faqs')
@section('content')
    <div class="listar-innerbanner">
        <div class="listar-parallaxcolor listar-innerbannerparallaxcolor">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="listar-innerbannercontent">
                            <div class="listar-pagetitle">
                                <h1>@lang('corals-directory-listing-star::labels.faqs.title')</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <main id="listar-main" class="listar-main listar-innerspeace listar-bglight listar-haslayout">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div id="listar-content" class="listar-content">
                        <div id="listar-testimonials" class="listar-testimonials listar-testimonialsvtwo">
                            @php $i = 1; @endphp
                            @forelse($faqs as $faq)
                                <div class="listar-testimonial">
                                    <div class="listar-testimonialholder">
                                        <span class="listar-iconquote"><img
                                                    src="{{\Theme::url('images/icons/icon-06.png')}}"
                                                    alt="image description"></span>
                                        <h2>{{ $faq->title }}</h2>
                                        <blockquote><q>{!! $faq->content !!}</q>
                                        </blockquote>
                                        @if(count($faq->categories))
                                            @foreach($faq->categories as $category)
                                                <h3>
                                                    {{$category->name}}
                                                </h3>
                                            @endforeach
                                        @endif
                                        @if(count($faq->tags))
                                            @foreach($faq->tags as $tag)
                                                <h4>{{$tag->name}}</h4>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                @php $i++; @endphp
                            @empty
                                <div class="alert alert-warning">
                                    <h4>@lang('corals-directory-listing-star::labels.faqs.no_faqs_found')</h4>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@stop