<aside class="listar-sidebar">
    <div class="listar-widget listar-widgetcategories">
        <div class="listar-widgettitle">
            <h3>@lang('corals-directory-listing-star::labels.partial.categories') :</h3>
        </div>
        <div class="listar-widgetcontent">
            <ul>
                @foreach(\CMS::getCategoriesList(true,'active') as $category)
                    <li><a href="{{url('category/'.$category->slug)}}">{{$category->name}}</a>
                        <span>({{\CMS::getCategoryPostsCount($category)}})</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="listar-widget listar-widgettags">
        <div class="listar-widgettitle">
            <h3>@lang('corals-directory-listing-star::labels.partial.tag_cloud') :</h3>
        </div>
        <div class="listar-widgetcontent">
            <ul>
                @foreach(\CMS::getTagsList(true,'active') as $tag)
                    <li><a class="listar-tag {{ Request::is('tag/'.$tag->slug)?'active':'' }}"
                           href="{{url('tag/'.$tag->slug)}}">{{$tag->name}}</a></li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="listar-widget listar-widgetsearch">
        <form class="listar-formtheme listar-formsearch" action="{{ url('blog') }}" method="get">
            <fieldset>
                <input name="query" id="se" type="text" class="form-control search"
                       placeholder="@lang('corals-directory-listing-star::labels.blog.Search blog')">
                <button class="search-submit" id="submit_btn"><i class="icon-search4"></i></button>
            </fieldset>
        </form>
    </div>
</aside>