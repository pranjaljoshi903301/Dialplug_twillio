<footer id="listar-footer" class="listar-footer listar-haslayout">
    <div class="listar-footeraboutarea">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="listar-upperbox">
                        <strong class="listar-logo"><a href="{{ url('/')}}"><img src="{{ \Settings::get('site_logo') }}"
                                                                                 style="max-width: 200px"
                                                                                 alt="{{ \Settings::get('site_name', 'Corals') }}"></a></strong>
                        <ul class="listar-socialicons">
                            @foreach(\Settings::get('social_links',[]) as $key=>$link)
                                <li><a href="{{ $link }}" target="_blank"><i class="fa fa-{{ $key }}"></i></a></li>
                            @endforeach
                        </ul>
                        <nav class="listar-navfooter">
                            <ul>
                                @foreach(Menus::getMenu('frontend_footer','active') as $menu)
                                    <li><a href="{{url($menu->url)}}">@if($menu->icon)<i
                                                    class="{{ $menu->icon }} fa-fw"></i>@endif{{$menu->name}}</a></li>
                                @endforeach
                            </ul>
                        </nav>
                    </div>
                    <div class="listar-lowerbox">
                        <div class="listar-description">
                            <p class="text-primary mb-0 d-inline-block" style="color: #FFFFFF;margin-right: 5px">
                                <i class="fa fa-phone fa-fw"></i> {{ \Settings::get('contact_mobile','+970599593301') }}
                            </p>
                            @if(count(\Settings::get('supported_languages')) > 1)
                                <li class="nav-item dropdown display-none d-md-flex d-inline-block">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" id="dropdownLanguage"
                                       data-toggle="dropdown"
                                       aria-haspopup="true" aria-expanded="false">
                                        {!! \Language::flag() !!} {!! \Language::getName() !!}<i
                                                class="fa fa-caret-down fa-fw"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownLanguage">
                                        {!! \Language::flags('language-dropdown-menu','dropdown-item') !!}
                                    </div>
                                </li>
                            @endif

                        </div>
                        <address>
                            <span> {!! \Settings::get('footer_text','') !!}</span></address>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
