@extends('layouts.public')

@section('title',trans('admin-dragon::labels.auth.login_register'))
@section('content')
    <div class="section-headline-wrap">
        <div class="section-headline">
            <h2>@lang('admin-dragon::labels.auth.login_register')</h2>
            <p>@lang('admin-dragon::labels.auth.login_register')</p>
        </div>
    </div>
    <div class="section-wrap">
        <div class="section demo">
            <div class="form-popup">
                <div class="form-popup-headline secondary">
                    <h2>@lang('admin-dragon::labels.auth.login')</h2>

                </div>
                <div class="form-popup-content">
                    @php \Actions::do_action('pre_login_form') @endphp

                    <hr class="line-separator">
                    <form method="post" action="{{route('login')}}" id="login-form">
                        {{csrf_field()}}
                        <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="rl-label required">@lang('User::attributes.user.email')</label>
                            <input type="text" id="email" name="email"
                                   placeholder="@lang('User::attributes.user.email')" value="{{ old('email') }}"
                                   autofocus>
                            @if ($errors->has('email'))
                                <div class="help-block">
                                    <small>{{ $errors->first('email') }}</small>
                                </div>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="rl-label required">@lang('User::attributes.user.password')</label>
                            <input type="password" id="password" name="password"
                                   placeholder="@lang('User::attributes.user.password')">
                            @if ($errors->has('password'))
                                <div class="help-block">
                                    <small>{{ $errors->first('password') }}</small>
                                </div>
                            @endif
                        </div>
                        <div class="">
                            <input type="checkbox" id="remember" name="remember"
                                    {{ old('remember') ? 'checked' : '' }} >
                            <label for="remember" class="label-check">
                                <span class="checkbox tertiary"><span></span></span>
                                @lang('admin-dragon::labels.auth.remember_me')
                            </label>
                        </div>
                        <p>@lang('admin-dragon::auth.forget_password')<a
                                    href="{{ route('password.request') }}"
                                    class="primary">@lang('admin-dragon::auth.click_here')</a>
                        </p>
                        <button class="button mid dark"
                                type="submit">@lang('admin-dragon::labels.auth.login')</button>
                    </form>
                    <hr class="line-separator double">
                    <div class="custom-flex">
                        @if(config('services.facebook.client_id'))
                            <a href="{{ route('auth.social', 'facebook') }}" id="custom-width-login"
                               class="button mid fb half">
                                @lang('admin-dragon::auth.sign_in_facebook')</a>
                        @endif
                        @if(config('services.twitter.client_id'))
                            <a href="{{ route('auth.social', 'twitter') }}" id="custom-width-login"
                               class="button mid twt half">
                                @lang('admin-dragon::auth.sign_in_twitter')
                            </a>
                        @endif
                        @if(config('services.google.client_id'))
                            <a href="{{ route('auth.social', 'google') }}" id="custom-width-login"
                               class="button mid google half">
                                @lang('admin-dragon::auth.sign_in_google')
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="form-popup" id="custom-register">
                <div class="form-popup-headline secondary">
                    <h2>@lang('admin-dragon::labels.auth.register_new_account')</h2>

                </div>
                <div class="form-popup-content">
                    <hr class="line-separator">
                    <form id="register-form" method="POST" action="{{ route('register') }}" class="ajax-form">
                        {{csrf_field()}}
                        <div class="form-group half {{ $errors->has('name') ? ' has-error' : '' }}"
                        >
                            <label for="name"
                                   class="rl-label required">@lang('User::attributes.user.name')</label>
                            <input type="text" name="name"
                                   placeholder="@lang('User::attributes.user.name')" value="{{ old('name') }}"
                                   autofocus/>
                            @if ($errors->has('name'))
                                <div class="help-block">
                                    <small>{{ $errors->first('name') }}</small>
                                </div>
                            @endif
                        </div>
                        <div class="form-group half {{$errors->has('last_name') ? 'has-error': ''}}"
                        >
                            <label for="last_name" class="rl-label required">@lang('User::attributes.user.last_name')</label>
                            <input type="text" name="last_name"
                                   placeholder="@lang('User::attributes.user.last_name')" value="{{ old('last_name') }}"
                                   autofocus/>
                            @if ($errors->has('last_name'))
                                <div class="help-block">
                                    <small>{{ $errors->first('last_name') }}</small>
                                </div>
                            @endif
                        </div>
                        <div class="form-group {{$errors->has('email') ? 'has-error': ''}}"
                             style="padding-right: 28px;">
                            <label for="email" class="rl-label required">@lang('User::attributes.user.email')</label>
                            <input type="email" name="email"
                                   placeholder="@lang('User::attributes.user.email')" value="{{ old('email') }}"
                                   autofocus/>
                            @if ($errors->has('email'))
                                <div class="help-block">
                                    <small>{{ $errors->first('email') }}</small>
                                </div>
                            @endif
                        </div>
                        <div class="form-group half {{ $errors->has('password') ? 'has-error' : '' }}">
                            <label class="rl-label required">@lang('User::attributes.user.password')</label>
                            <input type="password" name="password"
                                   placeholder="@lang('User::attributes.user.password')">
                            @if ($errors->has('password'))
                                <div class="help-block">
                                    <small>{{ $errors->first('password') }}</small>
                                </div>
                            @endif
                        </div>
                        <div class="form-group half {{ $errors->has('password_confirmation' ? 'has-error' : '' ) }}">
                            <label for="retype_password"
                                   class="rl-label required">@lang('User::attributes.user.retype_password')</label>
                            <input type="password" name="password_confirmation"
                                   placeholder="@lang('User::attributes.user.retype_password')">
                            @if ($errors->has('password_confirmation'))
                                <div class="help-block">
                                    <small>{{ $errors->first('password_confirmation') }}</small>
                                </div>
                            @endif
                        </div>
                        @if($is_two_factor_auth_enabled = \Settings::get('two_factor_auth_enable',false))
                            <div class="form-group {{ $errors->has('phone_country_code') ? 'has-error': '' }}">
                                <label for="phone_country"
                                       class="rl_label required">@lang('User::attributes.user.phone_country_code')</label>
                                <select class="form-control" id="authy-countries"
                                        name="phone_country_code"></select>
                                @if ($errors->has('phone_country_code'))
                                    <div class="help-block">
                                        <small>{{ $errors->first('phone_country_code') }}</small>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group {{$errors->has('phone_number') ? 'has-error': ''}}">
                                <label for="authy-cellphone"
                                       class="control-label">@lang('User::attributes.user.phone_number')
                                    :</label>
                                <input class="form-control" id="authy-cellphone"
                                       placeholder="@lang('User::attributes.user.cell_phone_number')"
                                       type="text"
                                       value="{{ old('phone_number') }}"
                                       name="phone_number"/>
                                @if ($errors->has('phone_number'))
                                    <div class="help-block">
                                        <small>{{ $errors->first('phone_number') }}</small>
                                    </div>
                                @endif

                            </div>
                        @endif
                        <div class="form-group {{ $errors->has('terms') ? ' has-error' : '' }}">
                            <input name="terms" value="1" type="checkbox" id="term"/>
                            <label for="term">
                                <span class="checkbox tertiary"><span></span></span>
                                @lang('admin-dragon::labels.auth.agree')
                                <strong><a href="#" data-toggle="modal" id="terms-anchor"
                                           data-target="#terms">@lang('admin-dragon::labels.auth.terms')</a>
                                </strong>
                            </label>
                            @if ($errors->has('terms'))
                                <span class="help-block"><strong>@lang('corals-marketplace-master::labels.auth.accept_terms')</strong></span>
                            @endif
                        </div>
                        <button type="submit"
                                class="button mid dark">@lang('admin-dragon::labels.auth.register')
                        </button>
                    </form>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    @component('components.modal',['id'=>'terms','header'=>\Settings::get('site_name').' Terms and policy'])
        {!! \Settings::get('terms_and_policy') !!}
    @endcomponent
@endsection
@section('js')
    {!! \Html::script('assets/corals/plugins/authy/form.authy.js') !!}
    <script type="text/javascript">
        $('#country-div').on("DOMSubtreeModified", function () {
            $(".countries-input").addClass('form-control');
        });
    </script>

    @php \Actions::do_action('admin_footer_js') @endphp

@endsection


