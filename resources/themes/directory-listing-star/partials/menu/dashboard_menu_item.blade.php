@foreach($menus as $menu)
    @if($menu->hasChildren('active') && $menu->user_can_access)
        <li data-toggle="collapse" data-target="#memu_{{$menu->hashed_id}}"
            class="{{ \Request::is(explode(',',$menu->active_menu_url))|| $menu->getProperty('always_active',false,'boolean')?' collapsed active':'' }}">
            <a href="#">
                @if($menu->icon)<i class="{{ $menu->icon }} fa-fw"></i>@endif
                <span>{{ $menu->name }}</span>
                <span class="arrow icon-arrow-down3"></span>

            </a>
        </li>

        <ul class="collapse" id="memu_{{$menu->hashed_id}}">
            @include('partials.menu.dashboard_menu_item', ['menus'=>$menu->getChildren('active')])
        </ul>
    @elseif($menu->user_can_access)
        <li class="{{ \Request::is(explode(',',$menu->active_menu_url))|| $menu->getProperty('always_active',false,'boolean')?'listar-active':'' }}">
            <a href="{{ url($menu->url) }}" target="{{ $menu->target??'_self' }}">
                {!! $menu->icon?'<i class="'. $menu->icon .' fa-fw"></i> ':'' !!} <span>{{ $menu->name }}</span>
            </a>
        </li>
    @endif
@endforeach

