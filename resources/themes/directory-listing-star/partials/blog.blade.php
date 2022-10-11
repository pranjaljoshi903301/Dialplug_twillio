<section class="listar-sectionspace listar-haslayout listar-bglight">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="listar-sectionhead">
                    <div class="listar-sectiontitle">
                        <h2>@lang('corals-directory-listing-star::labels.template.home.tips_articles')</h2>
                    </div>
                    <div class="listar-description">
                        <p>@lang('corals-directory-listing-star::labels.template.home.browse_the_latest_articles_from_our_blog')
                        </p>
                    </div>
                </div>
            </div>
            <div class="listar-themeposts listar-blogposts">
                @foreach(\Corals\Modules\CMS\Facades\CMS::getLatestPosts(3,'active') as $post)
                    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                        <div class="listar-themepost listar-post">
                            <figure class="listar-featuredimg">
                                <a href="{{url($post->slug)}}"><img src="{{$post->featured_image}}"
                                                                    alt=""></a>
                                @foreach($post->categories as $category)
                                    <a class="listar-postcategory" href="newsv1.html">{{$category->name}}</a>
                                @endforeach
                            </figure>
                            <div class="listar-postcontent">
                                <figure class="listar-authorimg"><img src="{{$post->author->picture_thumb}}" height="54"
                                                                      width="54" alt="{{$post->author->full_name}}">
                                </figure>
                                <h3><a href="{{url($post->slug)}}">{{$post->title}}</a></h3>
                                <div class="listar-themepostfoot">
                                    <time datetime="2017-08-08">
                                        <i class="icon-clock4"></i>
                                        <span>{{$post->created_at}}</span>
                                    </time>
                                    <a href="{{url('blog')}}"><span class="listar-postcomment">
												<i class="icon-comment"></i>
												<span>@lang('corals-directory-listing-star::labels.template.home.read_all')</span>
											</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
