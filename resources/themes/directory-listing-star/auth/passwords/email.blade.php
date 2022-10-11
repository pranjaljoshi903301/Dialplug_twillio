@extends('layouts.public')

@section('title',trans('corals-directory-listing-star::auth.reset_password'))

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
                                                                                  data-toggle="tab">{{trans('corals-directory-listing-star::auth.reset_password')}}</a>
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
                                            <form method="post" action="{{ route('password.email') }}" id="login-form"
                                                  class="listar-formtheme listar-formlogin">

                                                {{ csrf_field() }}
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
                                                    <button type="submit"
                                                            class="listar-btn listar-btngreen">@lang('corals-directory-listing-star::auth.send_password_reset')
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