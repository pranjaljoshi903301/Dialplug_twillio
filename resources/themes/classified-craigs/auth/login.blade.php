@extends('layouts.public')

@section('title',trans('corals-classified-craigs::auth.login'))

@section('css')
    {!! \Theme::css('css/AdminLTE-bootstrap-social.min.css') !!}

    <style type="text/css">
        .login-left {
            border-right: 4px solid #ddd;
        }

        @media (max-width: 470px) {
            .login-left {
                border-right: none;
            }
        }

        .or-separator {
            text-align: center;
            margin: 10px 0;
            text-transform: uppercase;
        }

        .or-separator:after, .or-separator:before {
            content: ' -- ';
        }
    </style>
@endsection
@section('page_header')
    @include('partials.page_header')
@endsection
@section('side_bar')

    <div class="page-title">
        <div class="container clearfix">
            <div class="float-left float-xs-none">
                <h1>@lang('corals-classified-craigs::auth.sign_in_start_session')</h1>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <section class="content">
        <section class="block">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-5">
                        @php \Actions::do_action('pre_login_form') @endphp
                        <div class="socials-buttons">
                            @if(config('services.facebook.client_id'))
                                <a class="btn btn-block btn-social btn-facebook"
                                   href="{{ route('auth.social', 'facebook') }}">
                                    <span class="fa fa-facebook"></span>
                                </a>
                            @endif
                            @if(config('services.twitter.client_id'))
                                <a class="btn btn-block btn-social btn-twitter"
                                   href="{{ route('auth.social', 'twitter') }}">
                                    <span class="fa fa-twitter"></span>
                                </a>
                            @endif
                            @if(config('services.google.client_id'))
                                <a class="btn btn-block btn-social btn-google"
                                   href="{{ route('auth.social', 'google') }}">
                                    <span class="fa fa-google"></span>
                                </a>
                            @endif
                        </div>
                        <form class="form clearfix" method="POST" action="{{ route('login') }}" id="login-form">
                            {{csrf_field()}}
                            <div class="form-group">
                                @if(session('confirmation_user_id'))
                                    <a href="{{ route('auth.resend_confirmation') }}">@lang('User::labels.confirmation.resend_email')</a>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" id="email"
                                       class="col-form-label required">@lang('User::attributes.user.email')</label>
                                <input name="email" type="email" class="form-control" id="email"
                                       placeholder="@lang('User::attributes.user.email')"
                                       value="{{ old('email') }}"
                                       autofocus required>
                            </div>
                            @if ($errors->has('email'))
                                <div class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="password"
                                       class="col-form-label required">@lang('User::attributes.user.password')</label>
                                <input name="password" type="password" class="form-control" id="password"
                                       placeholder="@lang('User::attributes.user.password')" required>
                            </div>
                            @if ($errors->has('password'))
                                <div class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </div>
                        @endif
                        <!--end form-group-->
                            <div class="d-flex justify-content-between align-items-baseline">
                                <label>
                                    <input type="checkbox"
                                           name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    @lang('corals-classified-craigs::auth.remember_me')
                                </label>

                            </div>
                            <button type="submit"
                                    class="btn btn-primary btn-block">@lang('corals-classified-craigs::auth.login')</button>
                        </form>
                        <hr>
                        <p>
                            @lang('corals-classified-craigs::auth.do_you_account')<a href="{{route('register')}}"
                                                         class="link">@lang('corals-classified-craigs::auth.register')</a>
                        </p>
                        <P>
                            <a href="{{ route('password.request') }}"
                               class="link">

                                @lang('corals-classified-craigs::auth.forget_password')<span
                                        class="fa fa-question"></span>
                            </a>
                        </P>
                    </div>
                </div>
            </div>
        </section>
    </section>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            if (!$(".socials-buttons").children().length > 0) {
                $(".or-separator").remove();
            }
        });
    </script>
@endsection