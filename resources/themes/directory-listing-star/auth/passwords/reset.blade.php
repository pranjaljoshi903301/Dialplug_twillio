@extends('layouts.public')

@section('title',trans('corals-directory-basic::auth.reset_password'))

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="login-page">
                    <figure class="listar-loginsingupimg"
                            data-vide-bg="poster: {{\Theme::url('images/slider/img-03.jpg')}}"
                            data-vide-options="position: 50% 50%" style="height: 450px"></figure>
                    <div class="listar-contentarea">
                        <div class="listar-themescrollbar">
                            <div class="listar-logincontent">
                                <div class="listar-themetabs">
                                    <ul class="listar-tabnavloginregistered" role="tablist">
                                        <li role="presentation" class="active"><a href="#listar-loging"
                                                                                  data-toggle="tab"> {{trans('corals-directory-listing-star::auth.reset_password')}}</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content listar-tabcontentloginregistered">
                                        @php \Actions::do_action('pre_login_form') @endphp
                                        @if (session('status'))
                                            <div class="alert alert-success">
                                                {{ session('status') }}
                                            </div>
                                        @endif
                                        <div role="tabpanel" class="tab-pane active fade in" id="listar-loging">
                                            <form method="POST" action="{{ route('password.request') }}" id="login-form"
                                                  class="listar-formtheme listar-formlogin">

                                                {{ csrf_field() }}

                                                <input type="hidden" name="token" value="{{ $token }}">
                                                <div class="form-group text-center">
                                                    @if(session('confirmation_user_id'))
                                                        <a href="{{ route('auth.resend_confirmation') }}">@lang('User::labels.confirmation.resend_email')</a>
                                                    @endif
                                                </div>
                                                <fieldset>
                                                    <div class="form-group listar-inputwithicon {{ $errors->has('email') ? ' has-error' : '' }}">
                                                        <i class="icon-profile-male"></i>
                                                        <input id="email" name="email" type="text"
                                                               value="{{ old('email') }}"
                                                               class="form-control"
                                                               placeholder="@lang('User::attributes.user.email')"
                                                               autofocus>
                                                        @if ($errors->has('email'))
                                                            <div class="help-block">
                                                                <strong>{{ $errors->first('email') }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="form-group listar-inputwithicon {{ $errors->has('password') ? ' has-error' : '' }}">
                                                        <i class="icon-lock-stripes"></i>
                                                        <input name="password" type="password" onClick="this.select()"
                                                               class="form-control"
                                                               placeholder="@lang('User::attributes.user.password')">
                                                        @if ($errors->has('password'))
                                                            <div class="help-block">
                                                                <strong>{{ $errors->first('password') }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="form-group listar-inputwithicon {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                                        <i class="icon-lock-stripes"></i>
                                                        <input name="password_confirmation" type="password"
                                                               onClick="this.select()"
                                                               class="form-control"
                                                               placeholder="@lang('User::attributes.user.password_confirmation')">
                                                        @if ($errors->has('password'))
                                                            <div class="help-block">
                                                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <button type="submit"
                                                            class="listar-btn listar-btngreen">@lang('corals-directory-listing-star::auth.reset_password')
                                                    </button>
                                                </fieldset>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
