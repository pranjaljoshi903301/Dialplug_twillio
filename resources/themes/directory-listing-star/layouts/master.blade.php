<!DOCTYPE html>
<html lang="{{ \Language::getCode() }}" dir="{{ \Language::getDirection() }}">
<head>
    <title>@yield('title') | {{ \Settings::get('site_name', 'Corals') }}</title>
    @yield('css')
    @include('partials.scripts.header')
    {!! Theme::css('css/dbstyle.css') !!}
    {!! Theme::css('css/dbresponsive.css') !!}
    {!! Theme::css('css/custom_dashboard.css') !!}
</head>

<body style="background: #e9eff9;">

<div class="preloader-outer">
    <div class="pin"></div>
    <div class="pulse"></div>
</div>

<div id="listar-wrapper" class="listar-wrapper listar-haslayout">
    @include('partials.header_dashboard')
    <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
        @yield('content_header')
        <!-- Main content -->
            <section class="">
                <div class="row">
                    <div class="col-md-6">
                        @yield('custom-actions')
                    </div>
                    <div class="col-md-6 text-right" style="padding-bottom: 10px;">
                        @yield('actions')
                    </div>
                </div>
            </section>
            <!-- /.content -->
            @yield('content')

        </div>
    @include('partials.scripts.footer')
</div>
@include('partials.dashboard_footer')

@include('components.modal',['id'=>'global-modal'])



</body>
</html>