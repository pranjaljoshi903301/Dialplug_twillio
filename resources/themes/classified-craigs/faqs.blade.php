@extends('layouts.public')


@section('content')
    <section class="content">
        <section class="block">
            <div class="container">
                <div class="row">
                    <section>
                        <form class="form inputs-underline" novalidate="novalidate">
                            <h3>{!! $faq->content !!}</h3>
                        </form>
                    </section>
                    @if(count($categories = \CMS::getCategoriesList(true,null,null,'faq')))
                        <div class="col-md-9">
                            @php
                                $faqs =  \Corals\Modules\CMS\Models\Faq::query()->published()->get()
                            @endphp
                            @if(count($faqs))
                                @foreach($faqs as $faq)
                                    <section>
                                        <div class="answer" href="#{{$faq->id}}">
                                            <div class="box">
                                                <h3>{{ $faq->title }}</h3>
                                                <p>{!! $faq->content !!}
                                                </p>
                                            </div>
                                            @if(count($faq->categories))
                                                @foreach($faq->categories as $category)
                                                    <figure>{{ $category->name }}</figure>
                                                @endforeach
                                            @endif
                                            @if(count($faq->tags))
                                                @foreach($faq->tags as $tag)
                                                    <figure>{{ $tag->name }}</figure>
                                                @endforeach
                                            @endif
                                        </div>
                                        @endforeach
                                    </section>
                                    @else
                                        <div class="alert alert-warning">
                                            <h4>@lang('corals-classified-craigs::labels.faqs.no_faqs_found')</h4>
                                        </div>
                                    @endif

                        </div>
                        <div class="col-md-3">
                            <aside class="sidebar">
                                <h2>@lang('corals-classified-craigs::labels.post.category')</h2>
                                <div class="sidebar-form form">
                                    @foreach($categories as $category)
                                        <a class="btn btn-primary width-100 mb-sm-4"
                                           href="#{{$category->slug}}">{{ $category->name }}</a>
                                    @endforeach
                                </div>
                            </aside>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <h3>@lang('corals-classified-craigs::labels.faqs.no_faqs_found')</h3>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </section>
@endsection
