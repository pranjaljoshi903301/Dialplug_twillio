<header id="listar-header" class="@yield('class_header','listar-header cd-auto-hide-header listar-haslayout')">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <strong class="listar-logo"><a href="{{ url('/')}}"><img src="{{ \Settings::get('site_logo') }}"
                                                                         style="max-width: 200px"
                                                                         alt="company logo here"></a></strong>
                <nav class="listar-addnav">
                    <ul>
                        @guest
                        <li>
                            <a id="listar-btnsignin" class="listar-btn listar-btnblue"
                               href="#listar-loginsingup">
                                <i class="icon-smiling-face"></i>
                                <span>@lang('corals-directory-listing-star::labels.template.home.join_now')</span>
                            </a>
                        </li>
                        @endguest
                        <li>
                            <a class="listar-btn listar-btngreen" href="{{url('directory/user/listings/create')}}">
                                <i class="icon-plus"></i>
                                <span>@lang('corals-directory-listing-star::labels.template.home.add_listing') </span>
                            </a>
                        </li>
                        <li>
                            @auth
                            <div class="dropdown listar-dropdown">
                                <a class="listar-userlogin listar-btnuserlogin" href="javascript:void(0);"
                                   id="listar-dropdownuser" data-toggle="dropdown">
                                    <span><img src="{{ user()->picture_thumb }}" alt="{{ user()->name }}"
                                               style="max-width: 42px"></span>
                                    <span style="padding-left: 8px;margin-right: 3px">{{ user()->name }}</span>
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <div class="dropdown-menu listar-dropdownmen" aria-labelledby="listar-dropdownuser">
                                    <ul>
                                        <li>
                                            <a href="{{ url('profile') }}">
                                                <i class="icon-user2"></i>
                                                <span>@lang('corals-directory-listing-star::labels.partial.my_profile')</span>
                                            </a>
                                        </li>
                                        @if(user() && user()->hasPermissionTo('Directory::listing.create'))
                                            <li>
                                                <a href="{{url('directory/user/listings/create')}}">
                                                    <i class="icon-plus"></i>
                                                    <span>@lang('corals-directory-listing-star::labels.template.home.add_listing')
                                                </span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{url('directory/user/listings')}}">
                                                    <i class="icon-layers"></i>
                                                    <span>@lang('corals-directory-listing-star::labels.dashboard.my_listings')
                                                </span>
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <a href="{{url('directory/wishlist/my')}}">
                                                <i class="fa fa-heart"></i>
                                                <span>@lang('corals-directory-listing-star::labels.dashboard.my_wishlists')</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('logout') }}" data-action="logout">
                                                <i class="icon-lock6"></i>
                                                <span>@lang('corals-directory-listing-star::labels.partial.logout')</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            @endauth
                            @guest
                            <div class="dropdown listar-dropdown">
                                <a class="listar-btnsignin listar-userlogin listar-btnuserlogin listar-btn listar-btnblue"
                                   href="javascript:void(0);"
                                   id="listar-dropdownuser" data-toggle="dropdown">
                                    <span>@lang('corals-directory-listing-star::labels.partial.my_account')</span>
                                </a>
                                <div class="dropdown-menu listar-dropdownmen" aria-labelledby="listar-dropdownuser">
                                    <ul>
                                        <li>
                                            <a href="{{ route('login') }}">
                                                <i class="icon-user2"></i>
                                                <span>@lang('corals-directory-listing-star::labels.partial.login')</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{route('register')}}">
                                                <i class="fa fa-heart"></i>
                                                <span>@lang('corals-directory-listing-star::labels.partial.register')</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            @endguest
                        </li>
                    </ul>
                </nav>
                <nav id="listar-nav" class="listar-nav">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                                data-target="#listar-navigation" aria-expanded="false">
                            <span class="sr-only">@lang('corals-directory-listing-star::labels.templates.home.toogle_navigation')</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div id="listar-navigation" class="collapse navbar-collapse listar-navigation">
                        <ul>
                            @include('partials.menu.menu_item',['menus' =>  \Menus::getMenu('frontend_top','active')])
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</header>