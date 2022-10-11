<header class="hero">
    <div class="hero-wrapper">
        <!--============ Secondary Navigation ===============================================================-->
        <div class="secondary-navigation">
            <div class="container">
                <ul class="left">
                    <li>
                      <span>
                          <i class="fa fa-phone"></i>{{ \Settings::get('contact_mobile','+970599593301') }}
                      </span>
                    </li>
                </ul>
                <!--end left-->
                <ul class="right dropdown">
                    @auth
                    <li class="nav-item dropdown">
                        <a class="dropdown-toggle" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true"
                           aria-expanded="false"
                           href="#">
                            <i class="fa fa-user"></i>{{ user()->name }}
                        </a>
                        <ul class="dropdown-menu custom-menu" aria-labelledby="dropdownMenuLink">
                            <li class="nav-item">
                                <a class="dropdown-item" href="{{url('classified/user/products')}}">
                                    <i class="fa fa-cube fa-fw"></i>
                                    @lang('corals-classified-craigs::labels.product.my')
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="dropdown-item" href="{{ user()->getDashboardURL() }}">
                                    <i class="fa fa-fw fa-dashboard"></i>@lang('corals-classified-craigs::auth.dashboard')
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="dropdown-item" href="{{ url('profile') }}">
                                    <i class="fa fa-fw fa-user"></i>@lang('corals-classified-craigs::auth.profile')
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="dropdown-item" href="{{ route('logout') }}" data-action="logout">
                                    <i class="fa fa-fw fa-sign-out"></i> @lang('corals-classified-craigs::auth.logout')
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{url('classified/wishlist/my')}}">
                            <i class="fa fa-heart"></i>@lang('corals-classified-craigs::labels.partial.my_adds')
                        </a>
                    </li>
                    @endauth
                    @guest
                    <li>
                        <a href="{{url('classified/wishlist/my')}}">
                            <i class="fa fa-heart"></i>@lang('corals-classified-craigs::labels.partial.my_adds')
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('login') }}">
                            <i class="fa fa-sign-in"></i>@lang('corals-classified-craigs::auth.login')
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('register') }}">
                            <i class="fa fa-pencil-square-o"></i>@lang('corals-classified-craigs::auth.register')
                        </a>
                    </li>
                    @endguest

                </ul>
                <!--end right-->
            </div>
            <!--end container-->
        </div>
        <!--============ End Secondary Navigation ===========================================================-->
        <!--============ Main Navigation ====================================================================-->
        <div class="main-navigation">
            <div class="container">
                <nav class="navbar navbar-expand-lg navbar-light justify-content-between">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <img src="{{ \Settings::get('site_logo') }}" alt="{{ \Settings::get('site_name') }}"
                             style="max-width: 160px">
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar"
                            aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbar">
                        <!--Main navigation list-->
                        <ul class="navbar-nav">
                            @include('partials.menu.menu_item' , ['menus'=> \Menus::getMenu('frontend_top','active')])
                            <li class="nav-item">
                                <a href="{{url('classified/user/products/create')}}"
                                   class="btn btn-primary text-caps btn-rounded btn-framed">
                                    @lang('corals-classified-craigs::labels.product.add')
                                </a>
                            </li>
                        </ul>
                        <!--Main navigation list-->
                    </div>
                    @yield('main_search')
                </nav>
                @yield('page_breadcrumbs')
            </div>
        </div>
        @yield('page_header')
        @yield('side_bar')
        <div class="background">
            <div class="background-image original-size">
                <img src="{{ Theme::url('img/hero-background-icons.jpg') }}" alt="">
            </div>
        </div>
    </div>
</header>