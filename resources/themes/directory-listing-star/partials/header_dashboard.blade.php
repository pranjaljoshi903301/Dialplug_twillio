<header id="listar-dashboardheader" class="listar-dashboardheader listar-haslayout">
    <div class="cd-auto-hide-header listar-haslayout">
        <div class="container-fluid">
            <div class="row">
                <strong class="listar-logo"><a href="index.html"><img src="{{ \Settings::get('site_logo') }}"
                                                                      alt="company logo here"></a></strong>
                <nav class="listar-addnav">
                    <ul>
                        <li>
                            <div class="dropdown listar-dropdown">
                                <a class="listar-userlogin listar-btnuserlogin" href="javascript:void(0);"
                                   id="listar-dropdownuser" data-toggle="dropdown">
                                    <span><img src="{{ user()->picture_thumb }}" alt="{{ user()->name }}"
                                               style="max-width: 42px"></span>
                                    <em>{{user()->name}}</em>
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <div class="dropdown-menu listar-dropdownmen" aria-labelledby="listar-dropdownuser">
                                    <ul>
                                        <li>
                                            <a href="{{ user()->getDashboardURL() }}">
                                                <i class="icon-speedometer2"></i>
                                                <span>@lang('corals-directory-listing-star::labels.partial.dashboard')</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{url('directory/user/listings')}}">
                                                <i class="icon-layers"></i>
                                                <span>@lang('corals-directory-listing-star::labels.dashboard.my_listings')</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ url('profile') }}">
                                                <i class="icon-user2"></i>
                                                <span>@lang('corals-directory-listing-star::labels.partial.my_profile')</span>
                                            </a>
                                        </li>
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
                        </li>
                        <li>
                            <a class="listar-btn listar-btngreen" href="{{url('directory/user/listings/create')}}">
                                <i class="icon-plus"></i>
                                <span>@lang('corals-directory-listing-star::labels.template.home.add_listing') </span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <nav id="listar-nav" class="listar-nav">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                                data-target="#listar-navigation" aria-expanded="false">
                            <span class="sr-only">@lang('corals-directory-listing-star::labels.template.home.toogle_navigation')</span>
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
    <div id="listar-sidebarwrapper" class="listar-sidebarwrapper">
        <strong class="listar-logo"><a href="{{url('/')}}"><img src="{{ \Settings::get('site_logo') }}"
                                                                alt="company logo here"></a></strong>
        <span id="listar-btnmenutoggle" class="listar-btnmenutoggle"><i class="fa fa-angle-left"></i></span>
        <div id="listar-verticalscrollbar" class="listar-verticalscrollbar">
            <nav id="listar-navdashboard" class="listar-navdashboard">
                <ul>
                    <li>
                        <a href="{{ url('profile') }}">
                            <i class="icon-lock6"></i>
                            <span>@lang('corals-directory-listing-star::labels.partial.my_profile')</span>
                        </a>
                    </li>
                    <li><a href="{{ url('notifications') }}" class="_dropdown-toggle" data-_toggle="dropdown">
                            <i class="fa fa-bell"></i><span>
                                @lang('corals-directory-listing-star::labels.partial.notifications')
                                </span>
                            @if($unreadNotifications = user()->unreadNotifications()->count())
                                <span>({{ $unreadNotifications }})</span>
                            @endif
                        </a>
                    </li>
                </ul>
                <ul id="menu-content" class="menu-content">
                    @include('partials.menu.dashboard_menu_item', ['menus'=> \Menus::getMenu('sidebar','active')])
                </ul>
            </nav>
        </div>
    </div>
</header>