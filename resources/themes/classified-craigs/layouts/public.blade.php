<!DOCTYPE html>
<html lang="{{ \Language::getCode() }}" dir="{{ \Language::getDirection() }}">
<head>
    {!! \SEO::generate() !!}
    @include('partials.scripts.header')
</head>

<body>
@php \Actions::do_action('after_body_open') @endphp

<div class="page @yield('home', 'sub-page')">
    @include('partials.header')
    @yield('content')
    @include('partials.footer')
</div>

@include('partials.scripts.footer')

@include('components.modal',['id'=>'global-modal'])

@php \Actions::do_action('admin_footer_js') @endphp

</body>
</html>