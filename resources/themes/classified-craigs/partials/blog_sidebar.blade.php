<aside class="sidebar">
    <section>
        <h2>@lang('corals-classified-craigs::labels.partial.search_blog')</h2>
        <!--============ Side Bar Search Form ===========================================-->
        <form class="sidebar-form form" id="search-form" action="{{ url('blog') }}" method="get">
            <div class="form-group">
                <input type="search" class="form-control" name="query"
                       id="search-input" placeholder="@lang('corals-classified-craigs::labels.template.search')">
                <button type="submit" id="search-submit" class="search-btn"
                        style="position:absolute;"><i class="fa fa-search"></i>
                </button>
            </div>
            <!--end form-group-->
        </form>
        <!--============ End Side Bar Search Form =======================================-->
    </section>
    <section>
        <h2>@lang('corals-classified-craigs::labels.partial.latest_products')</h2>
        @foreach(\CMS::getLatestPosts(3) as $post)
            <div class="sidebar-post">
                @if($post->featured_image)
                    <a href="{{ url($post->slug) }}" class="background-image">
                        <img src="{{ $post->featured_image }}">
                    </a>
            @endif
            <!--end background-image-->
                <div class="description">
                    <h4>
                        <a href="{{ url($post->slug) }}">{{ $post->title }}</a>
                    </h4>
                    <div class="meta">
                        <a href="#">{{ $post->author->full_name }}</a>
                        <figure>{{ format_date($post->published_at) }}</figure>
                    </div>
                    <!--end meta-->
                </div>
                <!--end description-->
            </div>
        @endforeach
    </section>
    <section>
        <h2>@lang('corals-classified-craigs::labels.post.category')</h2>
        <ul class="sidebar-list list-unstyled">
            @foreach(\CMS::getCategoriesList(true , 'active') as $category)
                <li>
                    <a href="{{ url('category/'.$category->slug) }}">{{ $category->name }}<span>{{ \CMS::getCategoryPostsCount($category) }}</span></a>
                </li>
            @endforeach
        </ul>
    </section>
</aside>