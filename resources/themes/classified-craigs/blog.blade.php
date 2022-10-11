@extends('layouts.public')

@section('page_header')
    @include('partials.page_header', ['item'=>$blog])
@endsection
@section('content')
    <section class="content">
        <section class="block">
            <div class="container">
                <div class="row">
                    <div class="{{ !in_array($blog->template ,['right','left'])?'col-md-12':'col-md-8' }} {{ $blog->template == 'left' ? 'order-lg-2':'' }}">
                        @forelse($posts as $post)
                            <article class="blog-post clearfix">
                                @if($post->featured_image)
                                    <a href="{{ url($post->slug) }}">
                                        <img src="{{ $post->featured_image }}" alt="">
                                    </a>
                                @endif
                                <div class="article-title">
                                    <h2><a href="{{url($post->slug
                                    )}}">{{ $post->title }}</a></h2>
                                    <div class="tags framed">
                                        @foreach($post->activeCategories as $category)
                                            <a href="{{ url('category/'.$category->slug)  }}"
                                               class="tag">{{ $category->name }}</a>
                                        @endforeach
                                    </div>
                                    <div class="tags framed">
                                        @if(count($activeTags = $post->activeTags))
                                            @foreach($activeTags
                                             as $tag)
                                                <a href="{{ url('tag/'.$tag->slug)  }}" class="tag">{{ $tag->name }}</a>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="meta">
                                    <figure>
                                        <a href="#" class="icon">
                                            <i class="fa fa-user"></i>
                                            {{ $post->author->full_name }}
                                        </a>
                                    </figure>
                                    <figure>
                                        <i class="fa fa-calendar-o"></i>
                                        {{ format_date($post->published_at) }}
                                    </figure>
                                </div>
                                <div class="blog-post-content">
                                    <p>
                                        {{ \Str::limit(strip_tags($post->rendered ),250) }}
                                    </p>
                                    <a href="{{ url($post->slug) }}" class="btn btn-primary btn-framed detail">
                                        @lang('corals-classified-craigs::labels.blog.read_more')
                                    </a>
                                </div>
                            </article>
                        @empty
                            <div class="article-title">
                                <h2>@lang('corals-classified-craigs::labels.blog.no_posts_found')</h2>
                            </div>
                        @endforelse
                        {{ $posts->links('partials.paginator') }}
                    </div>
                    @if(in_array($blog->template,['right','left']))
                        <div class="col-md-4 {{ $blog->template == 'left'?'order-lg-1':'' }}">
                            @include('partials.blog_sidebar')
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </section>
@endsection