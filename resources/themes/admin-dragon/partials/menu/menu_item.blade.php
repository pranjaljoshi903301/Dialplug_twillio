@foreach($menus as $menu)
    @if($menu->hasChildren('active') && $menu->user_can_access)
        <li class="dropdown-item interactive {{ \Request::is($menu->active_menu_url)?'active': '' }} ">
            <a href="#">
                {!! $menu->icon?'<i class="'. $menu->icon .' fa-fw"></i> ':'' !!}
                {!! $menu->name !!}
                <svg class="svg-arrow">
                    <use xlink:href="#svg-arrow"></use>
                </svg>
            </a>
            <ul class="inner-dropdown {{ \Request::is($menu->active_menu_url)?'open':'' }}">
                @include('partials.menu.menu_item', ['menus'=>$menu->getChildren('active'),'is_sub'=>true])
            </ul>
        </li>
    @elseif($menu->user_can_access)
        @if(isset($is_sub) && $is_sub)
            <li class="inner-dropdown-item {{ \Request::is($menu->active_menu_url)?' active ':'' }}">
                <a href="{{ url($menu->url) }}"
                   target="{{ $menu->target??'_self' }}">
                    {!! $menu->icon?'<i class="'. $menu->icon .' fa-fw"></i> ':'' !!} <span>{{ $menu->name }}</span>
                </a>
            </li>
        @else
            <li class="dropdown-item interactive  {{ \Request::is($menu->active_menu_url)?'active': '' }} ">
                <a href="{{ url($menu->url) }}" target="{{ $menu->target??'_self' }}">
                    {!! $menu->icon?'<i class="'. $menu->icon .' fa-fw"></i> ':'' !!}
                    {!! $menu->name !!}
                </a>
            </li>
        @endif
    @endif
@endforeach