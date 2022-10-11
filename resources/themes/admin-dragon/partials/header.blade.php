<div class="dashboard-header retracted">
    <div class="db-options-button">
        <img src="{{Theme::url('images/dashboard/db-list-right.png')}}" alt="db-list-right">
        <img src="{{Theme::url('images/dashboard/close-icon.png')}}" alt="close-icon">
    </div>
    <div class="dashboard-header-item title" id="dashborad-header-first" style="">
        <div class="db-side-menu-handler">
            <img src="{{Theme::url('images/dashboard/db-list-left.png')}}" alt="db-list-left">
        </div>
        <a href="{{ url('/') }}">
            <img src="{{ \Settings::get('site_logo') }}" class="" style="margin-top: 22px;
    max-width: 170px;"/>
        </a>
    </div>
    <div class="dashboard-header-item form custom-header-dashboard" id="dashboard-header-seconde" style="">
        <a href="{{url('logout')}}"
           data-action="logout" class="button secondary"><i class="fa fa-sign-out"></i> @lang('admin-dragon::labels.partial.logout')</a>
        @auth
        @if (schemaHasTable('notifications'))
            <a href="{{ url('notifications') }}" class="button secondary">
                <i class="fa fa-bell"></i>
                @if($unreadNotifications = user()->unreadNotifications()->count())
                    <span class="label label-warning badge badge-warning">{{ $unreadNotifications }}</span>
                @endif
            </a>
        @endif
        @endauth
        @if (user()->can('Settings::module.manage') && !config('settings.models.module.disable_update'))
            <a href="{{ url('modules') }}" class="button secondary">
                <i class="fa fa-refresh"></i>
                @if($updatesAvailable = \Modules::hasUpdates())
                    <span class="label label-info">{{ $updatesAvailable }}</span>
                @endif
            </a>
        @endif
        @if (schemaHasTable('announcements'))
            <li class="" style="list-style: none;display: flex;">
                <a href="#" class="dropdown-toggle custom-icon" data-toggle="dropdown" style="display: flex">
                    <i class="button secondary fa fa-bullhorn"></i>
                    @if($unreadAnnouncements = \Announcement::unreadAnnouncements())
                        <span class="label label-success">{{ $unreadAnnouncements }}</span>
                    @endif
                </a>
                <ul class="dropdown-menu">
                    @if($unreadAnnouncements)
                        <li class="header text-center">
                            <small>@lang('Announcement::labels.unread_count_message',['count'=>$unreadAnnouncements])</small>
                        </li>
                        @foreach(\Announcement::unreadAnnouncements(user(), false, 5) as $announcement)
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <li><!-- start message -->
                                        <a href="{{ $announcement->getShowURL() }}"
                                           class="show_announcement"
                                           data-ann_hashed_id="{{ $announcement->hashed_id }}"
                                           data-title="{{ $announcement->title }}">
                                            @if($announcement->image)
                                                <div class="pull-left">
                                                    <img src="{{ $announcement->image }}" class="img-responsive"
                                                         alt="ann-img">
                                                </div>
                                            @else
                                                <div class="pull-left btn btn-info btn-circle m-r-10">
                                                    <i class="fa fa-bullhorn"></i>
                                                </div>
                                            @endif
                                            <h4 style="text-overflow: ellipsis;overflow: hidden;">
                                                {{ $announcement->title }}
                                            </h4>
                                            <p>
                                                <small>
                                                    <i class="fa fa-clock-o"></i> {{ $announcement->starts_at->diffForHumans() }}
                                                </small>
                                            </p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endforeach
                    @endif
                    <li class="footer">
                        <a href="{{ url('announcements') }}">@lang('Announcement::labels.see_all')</a>
                    </li>
                </ul>
            </li>
        @endif
    </div>

    <div class="dashboard-header-item stats" id="dashboard-header-seconde" style="width: 20.25%">
        <ul class="stats-meta" id="custom-header-cart">
            <li class="navbar-nav my-lg-0 mr-2">
                @if(count(\Settings::get('supported_languages', [])) > 1)
                    <li class="nav-item" id="custom-menu-language">
                        <a class="nav-link dropdown-toggle waves-effect waves-dark db-flex" href="#"
                           data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false">
                            <small class="custom-small">{!! \Language::flag() !!}</small>
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right animated bounceInDown p-3">
                            {!! \Language::flags('list-unstyled','mb-1') !!}
                        </div>
                    </li>
                @endif
            </li>
            <li class="list-unstyled currencies" style="display: inline-block;">
                @php \Actions::do_action('post_display_frontend_menu') @endphp
            </li>
        </ul>
    </div>

    <div class="dashboard-header-item back-button">
        <a href="{{url('shop')}}" class="button mid dark-light">Shop</a>
    </div>
</div>