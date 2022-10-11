@foreach($menus as $menu)
    @if($menu->hasChildren('active') && $menu->user_can_access)
        <li class="nav-item {{ \Request::is(explode(',',$menu->active_menu_url))|| $menu->getProperty('always_active',false,'boolean')?'active':'' }} has-child">
            <a class="nav-link" href="#">
                {!! $menu->icon?'<i class="'. $menu->icon .' fa-fw"></i> ':'' !!}{{ $menu->name }}
            </a>
            <ul class="child">
                @foreach($menu->getChildren('active') as $menu)
                    @if(!$menu->user_can_access)
                        @continue
                    @endif
                    <li class="nav-item">
                        <a href="{{ url($menu->url) }}" class="nav-link">
                            {!! $menu->icon?'<i class="'. $menu->icon .' fa-fw"></i> ':'' !!}{{ $menu->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>
    @elseif($menu->user_can_access)
        <li class="nav-item {{ \Request::is(explode(',',$menu->active_menu_url))|| $menu->getProperty('always_active',false,'boolean')?'active':'' }}">
            <a class="nav-link" href="{{ url($menu->url) }}">
                {!! $menu->icon?'<i class="'. $menu->icon .' fa-fw"></i> ':'' !!}{{ $menu->name }}
            </a>
        </li>
    @endif
@endforeach
