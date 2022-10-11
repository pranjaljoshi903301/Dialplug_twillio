@extends('layouts.public')

@section('content')
    @include('partials.page_header', ['item'=>$blog, 'content'=> (empty($blog->rendered)?('<h1>'.$blog->title.'</h1>'):$blog->rendered).(isset($title)?('<br/>'.$title):'')])
    <main id="listar-main" class="listar-main listar-innerspeace listar-bglight listar-haslayout">
        <div class="container">
            <div class="row">
                <div id="listar-twocolumns" class="listar-twocolumns">
                    <div class="listar-posts listar-postsgrid listar-postslist">
                        <div class="col-xs-12 col-sm-12 {{ !in_array($blog->template,  ['right', 'left'])?'col-lg-12':'col-md-8' }} {{ $blog->template =='left' ? 'order-lg-2':'pull-right' }}">
                            <div id="listar-content" class="listar-content">
                                @forelse($posts as $post)
                                    <div class="listar-themepost listar-post">
                                        <figure class="listar-featuredimg">
                                            @if($post->featured_image)
                                                <div class="">
                                                    <a href="{{ url($post->slug) }}">
                                                        <img src="{{$post->featured_image}}" alt="Post">
                                                    </a>
                                                </div>
                                            @endif
                                            <span class="listar-postcomment">
                                            @foreach($post->activeCategories as $category)
                                                    <a class=""
                                                       href="{{ url('category/'.$category->slug) }}">
                                                   <span class="custom-span">{{ $category->name }}</span>
                                                </a>
                                                @endforeach
                                            </span>
                                        </figure>
                                        <div class="listar-postcontent">
                                            <div class="listar-postmetadata">
                                                <div class="listar-authorimgplusname">
                                                    <figure class="listar-authorimg"><img
                                                                src="{{$post->author->picture_thumb}}"
                                                                height="54" width="54"
                                                                alt="image description">
                                                    </figure>
                                                    <span>{{ $post->author->full_name }}</span>
                                                </div>
                                                <time datetime="2017-08-08">
                                                    <i class="icon-clock4"></i>
                                                    <span>{{ format_date($post->published_at) }}</span>
                                                </time>
                                            </div>
                                            <h2><a href="{{url($post->slug)}}">{{$post->title}}</a></h2>
                                            <div class="listar-description">
                                                <p>{{ \Str::limit(strip_tags($post->rendered ),250) }}</p>
                                            </div>
                                            <a class="listar-readmore"
                                               href='{{ url($post->slug) }}'>@lang('corals-directory-listing-star::labels.blog.read_more')</a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="alert alert-warning">
                                        <h4>@lang('corals-directory-listing-star::labels.blog.no_posts_found')</h4>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        @if(in_array($blog->template,['right','left']))
                            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 {{ $blog->template =='left' ? 'order-lg-1':'pull-left' }}">
                                @include('partials.blog_sidebar')
                            </div>
                        @endif
                    </div>
                    {{ $posts->links('partials.paginator') }}
                </div>
            </div>
        </div>
    </main>
@endsection