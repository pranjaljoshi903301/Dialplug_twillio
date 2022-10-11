<!DOCTYPE html>
<html lang="{{ \Language::getCode() }}" dir="{{ \Language::getDirection() }}">
<head>
    {!! \SEO::generate() !!}
    
    @yield('css')
    @include('partials.scripts.header')
    {!! Theme::css('css/custom.css') !!}
</head>

<body class="@yield('body_class')">
@php \Actions::do_action('after_body_open') @endphp


<div class="preloader-outer">
    <div class="pin"></div>
    <div class="pulse"></div>
</div>

@include('partials.header')

@yield('content')
@include('partials.login_modal')

@include('partials.footer')


@include('partials.scripts.footer')

@include('components.modal',['id'=>'global-modal'])


@yield('js')
</body>
</html>