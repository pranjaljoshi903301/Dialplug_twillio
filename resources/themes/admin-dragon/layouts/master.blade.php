<!DOCTYPE html>
<html lang="{{ \Language::getCode() }}" dir="{{ \Language::getDirection() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        @auth
        @if($unreadNotifications = user()->unreadNotifications()->count())
            ({{ $unreadNotifications }})
        @endif
        @endauth
        @yield('title') | {{ \Settings::get('site_name', 'Corals') }}
    </title>

    <link rel="shortcut icon" href="{{ \Settings::get('site_favicon') }}" type="image/png">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.scripts.header')

    <style type="text/css">
        {!! \Settings::get('custom_admin_css', '') !!}
    </style>
</head>
<body>
@php \Actions::do_action('after_body_open') @endphp

@include('partials.sidebar')
<div class="dashboard-body">
    @include('partials.header')
    <div class="dashboard-content">
        @yield('content_header')
        <div class="row">
            <div class="col-md-6">
                @yield('custom-actions')
            </div>
            <div class="col-md-6 text-right">
                @yield('actions')
            </div>
        </div>
        @yield('content')
    </div>
</div>
<div class="shadow-film closed"></div>
@include('components.modal',['id'=>'global-modal'])
@include('partials.scripts.footer')
@include('partials.footer')
<script type="text/javascript">
    {!! \Settings::get('custom_admin_js', '') !!}
</script>
</body>
</html>