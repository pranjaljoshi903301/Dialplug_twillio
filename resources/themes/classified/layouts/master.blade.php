<!DOCTYPE html>
<html lang="{{ \Language::getCode() }}" dir="{{ \Language::getDirection() }}">
<head>
    <title>@yield('title') | {{ \Settings::get('site_name', 'Corals') }}</title>

    @include('partials.scripts.header')
</head>

<body>
@php \Actions::do_action('after_body_open') @endphp

@include('partials.header')

<div>
    @yield('before_content')

    <div class="section-padding">
        <div class="container-fluid">
            <div class="row mt-5">
                <aside id="sidebar"
                       class="col-md-3 col-xs-12 right-sidebar @yield('sidebar-class')">
                    @include('partials.dashboard_sidebar')
                </aside>
                <div class="@yield('content-class','col-md-9 col-xs-12')  content">
                    @php \Actions::do_action('before_page_content') @endphp
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    @yield('after_content')

    @include('partials.footer')
</div>

@include('partials.scripts.footer')

@include('components.modal',['id'=>'global-modal'])


@php \Actions::do_action('admin_footer_js') @endphp

</body>
</html>