@extends('layouts.public')

@section('content')
    <div class="section-headline-wrap">
        <div class="section-headline">
            <h2>@lang('admin-dragon::labels.auth.reset_password')</h2>
            <p>@lang('admin-dragon::labels.auth.reset_password')</p>
        </div>
    </div>
    <div class="section-wrap">
        <div class="section demo" style="display: flex">
            <div class="form-popup">
                <div class="form-popup-content">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <h4 class="popup-title">@lang('admin-dragon::labels.auth.reset_password')</h4>
                    <hr class="line-separator">
                    <form method="POST" action="{{ route('password.request') }}" id="login-form">
                        {{csrf_field()}}
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="form-group">
                            @if(session('confirmation_user_id'))
                                <a href="{{ route('auth.resend_confirmation') }}">@lang('User::labels.confirmation.resend_email')</a>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="username" class="rl-label">@lang('User::attributes.user.email')</label>
                            <input type="text" id="email" name="email"
                                   placeholder="@lang('User::attributes.user.email')" value="{{ old('email') }}"
                                   autofocus>
                            @if ($errors->has('email'))
                                <div class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </div>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="username" class="rl-label">@lang('User::attributes.user.password')</label>
                            <input type="password" name="password"
                                   placeholder="@lang('User::attributes.user.password')"/>
                            @if ($errors->has('password'))
                                <div class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </div>
                            @endif
                        </div>
                        <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="username"
                                   class="rl-label">@lang('User::attributes.user.password_confirmation')</label>
                            <input type="password" name="password_confirmation"
                                   placeholder="@lang('User::attributes.user.retype_password')"/>
                            @if ($errors->has('password_confirmation'))
                                <div class="help-block">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </div>
                            @endif
                        </div>
                        <button class="button mid dark"
                                type="submit">@lang('admin-dragon::labels.auth.send_password_reset')</button>
                    </form>
                    <hr class="line-separator double">
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
@endsection