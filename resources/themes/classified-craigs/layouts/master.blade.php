<!DOCTYPE html>
<html lang="{{ \Language::getCode() }}" dir="{{ \Language::getDirection() }}">
<head>
    <title>@yield('title') | {{ \Settings::get('site_name', 'Corals') }}</title>

    @include('partials.scripts.header')
</head>

<body>
@php \Actions::do_action('after_body_open') @endphp

<div class="page sub-page" id="dashboard-layout">
    @include('partials.header_dashboard')
    <section class="content">
        <section class="block">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        @php \Actions::do_action('before_page_content') @endphp
                        @yield('content')
                    </div>
                </div>
            </div>
        </section>
    </section>
    @include('partials.footer')
</div>

@include('partials.scripts.footer')

@include('components.modal',['id'=>'global-modal'])

@php \Actions::do_action('admin_footer_js') @endphp

</body>
</html>