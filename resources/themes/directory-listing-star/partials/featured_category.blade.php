<section class="listar-sectionspace listar-haslayout">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="listar-sectionhead">
                    <div class="listar-sectiontitle">
                        <h2>@lang('corals-directory-listing-star::labels.template.home.featured_categories')</h2>
                    </div>
                    <div class="listar-description">
                        <p>@lang('corals-directory-listing-star::labels.template.home.explore_some_of_the_best_tips_from_around_the_city_from_our_partners_and_friends')</p>
                    </div>
                </div>
            </div>
            <div class="listar-themeposts listar-categoryposts">
                @foreach(\Category::getCategoriesList('Directory',false,true,'active',[],true) as $category)
                    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                        <div class="listar-themepost listar-categorypost">
                            <figure class="listar-featuredimg">
                                <a href="{{url('listings?category='.$category->slug)}}">
                                    <img src="{{$category->thumbnail}}"
                                         alt="{{$category->name}}">
                                    <div class="listar-contentbox">
                                        <div class="listar-postcontent">
                                                    <span class="listar-categoryicon listar-flip"><i
                                                                class="icon-tourism"></i></span>
                                            <h3>{{$category->name}}</h3>
                                            <h4>@lang('corals-directory-listing-star::labels.template.home.locations') {{Corals\Modules\Directory\Facades\Directory::getCategoryListingsCount($category->id)}} </h4>
                                        </div>
                                    </div>
                                </a>
                            </figure>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
