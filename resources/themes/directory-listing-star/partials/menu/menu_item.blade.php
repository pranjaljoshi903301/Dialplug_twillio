@foreach($menus as $menu)
    @if($menu->hasChildren('active') && $menu->isRoot() && $menu->user_can_access)
        <li class="current-menu-item {{ \Request::is(explode(',',$menu->active_menu_url))|| $menu->getProperty('always_active',false,'boolean')?'active':'' }} {{ $menu->isRoot()?'':'menu-item-has-children has-children' }}">
            <a href="javascript:void(0);">{{ $menu->name }}</a>
            <ul class="sub-menu">
                @include('partials.menu.menu_item', ['menus' => $menu->getChildren('active')])
            </ul>
        </li>
    @elseif($menu->user_can_access)
        <li class="@if($menu->hasChildren())menu-item-has-children @endif {{ \Request::is(explode(',',$menu->active_menu_url))|| $menu->getProperty('always_active',false,'boolean')?'active':'' }}">
            <a class="{{ \Request::is(explode(',',$menu->active_menu_url))|| $menu->getProperty('always_active',false,'boolean')?'act-link':'' }}"
               href="{{ url($menu->url) }}" target="{{ $menu->target??'_self' }}">
                @if($menu->icon)<i class="{{ $menu->icon }}"></i>@endif
                {{ $menu->name }}
            </a>
            @if($menu->hasChildren())
                <ul class="sub-menu">
                    @include('partials.menu.menu_item', ['menus' => $menu->getChildren('active')])
                </ul>
            @endif
        </li>
    @endif
@endforeach
