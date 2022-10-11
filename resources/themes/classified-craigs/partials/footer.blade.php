<section class="footer">
    <div class="wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-5">
                    <a class="brand" href="{{ url('/') }}">
                        <img src="{{ \Settings::get('site_logo') }}" alt="{{ \Settings::get('site_name') }}"
                             style="max-width: 160px">
                    </a>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut nec tincidunt arcu, sit amet
                        fermentum sem. Class aptent taciti sociosqu ad litora torquent per conubia nostra.
                    </p>
                    <ul class="list-unstyled currencies" style="display: inline-block;">
                        @php \Actions::do_action('post_display_frontend_menu') @endphp
                    </ul>
                    @if(count(\Settings::get('supported_languages', [])) > 1)
                        <li class="dropdown locale" style="list-style-type: none;display: inline-block">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                {!! \Language::flag() !!} {!! \Language::getName() !!}
                            </a>
                            {!! \Language::flags('dropdown-menu') !!}
                        </li>
                    @endif
                </div>
                <!--end col-md-5-->
                <div class="col-md-3">
                    <h2>Navigation</h2>
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <nav>
                                <ul class="list-unstyled">
                                    @foreach(Menus::getMenu('frontend_footer','active') as $menu)
                                        <li>
                                            <a href="{{ url($menu->url) }}">
                                                {!! $menu->icon?'<i class="'. $menu->icon .' fa-fw"></i> ':'' !!}{{ $menu->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </nav>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <nav>
                                <ul class="list-unstyled">
                                    @foreach(\Settings::get('social_links',[]) as $key=>$link)
                                        <li>
                                            <a class="{{ $key }}" href="{{ $link }}"
                                               target="_blank"><i class="fa fa-{{ $key }}"></i>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                <!--end col-md-3-->
                <div class="col-md-4">
                    <h2>Contact</h2>
                    <address>
                        <figure>
                            {!! \Settings::get('footer_text','') !!}
                        </figure>
                        <br>
                        <strong>Email:</strong> <a href="#">hello@example.com</a>
                        <br>
                        <strong>Skype: </strong> Craigs
                        <br>
                        <br>
                        <a href="{{url('/contact-us')}}" class="btn btn-primary text-caps btn-framed">@lang('corals-classified-craigs::labels.template.home.contact_us')</a>
                    </address>
                </div>
                <!--end col-md-4-->
            </div>
            <!--end row-->
        </div>
        <div class="background">
            <div class="background-image original-size">
                <img src="{{ Theme::url('img/footer-background-icons.jpg')}}" alt="">
            </div>
            <!--end background-image-->
        </div>
        <!--end background-->
    </div>
</section>