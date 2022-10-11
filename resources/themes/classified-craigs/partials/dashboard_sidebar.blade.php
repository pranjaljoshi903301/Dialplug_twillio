<nav class="nav flex-column side-nav">
    @include('partials.menu.dashboard_menu_item', ['menus'=> \Menus::getMenu('sidebar','active')])
</nav>