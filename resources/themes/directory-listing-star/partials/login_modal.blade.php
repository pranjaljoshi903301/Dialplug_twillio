<div id="listar-loginsingup" class="listar-loginsingup">
    <button type="button" class="listar-btnclose">x</button>
    <figure class="listar-loginsingupimg" data-vide-bg="poster: {{\Theme::url('images/slider/img-01.jpg')}}"
            data-vide-options="position: 50% 50%"></figure>
    <div class="listar-contentarea">
        <div class="listar-themescrollbar">

            <div class="listar-logincontent">
                <div class="listar-themetabs" id="custom-padding-login-modal">
                    <ul class="listar-tabnavloginregistered" role="tablist">
                        <li role="presentation" class="active"><a href="#listar-loging-modal"
                                                                  data-toggle="tab">@lang('corals-directory-listing-star::auth.login')</a>
                        </li>
                        <li role="presentation"><a href="#listar-register-modal"
                                                   data-toggle="tab">@lang('corals-directory-listing-star::auth.register')</a>
                        </li>
                    </ul>
                    <div class="tab-content listar-tabcontentloginregistered">
                        @php \Actions::do_action('pre_login_form') @endphp

                        <div role="tabpanel" class="tab-pane active fade in" id="listar-loging-modal">
                            <form method="post" action="{{ route('login') }}" id="login-form"
                                  class="listar-formtheme listar-formlogin">
                                {{ csrf_field() }}
                                <fieldset>
                                    <div class="form-group listar-inputwithicon {{ $errors->has('email') ? ' has-error' : '' }}">
                                        <i class="icon-profile-male"></i>
                                        <input id="email" name="email" type="text" value="{{ old('email') }}"
                                               class="form-control"
                                               placeholder="@lang('User::attributes.user.email')" autofocus>
                                        @if ($errors->has('email'))
                                            <div class="help-block">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                    @if ($errors->has('email'))
                                        <div class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </div>
                                    @endif
                                    <div class="form-group listar-inputwithicon {{ $errors->has('password') ? ' has-error' : '' }}">
                                        <i class="icon-icons208"></i>
                                        <input id="password" name="password" type="password" onClick="this.select()"
                                               class="form-control"
                                               placeholder="@lang('User::attributes.user.password')">
                                        @if ($errors->has('password'))
                                            <div class="help-block">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <div class="listar-checkbox">
                                            <input type="checkbox" id="remember"
                                                   name="check" {{ old('remember') ? 'checked' : '' }}>
                                            <label for="remember">@lang('corals-directory-listing-star::auth.remember_me')</label>
                                        </div>
                                        <span><a href="{{ route('password.request') }}">@lang('corals-directory-listing-star::auth.forget_password')</a></span>
                                    </div>
                                    <button type="submit"
                                            class="listar-btn listar-btngreen flex-center">@lang('corals-directory-listing-star::auth.login')</button>
                                </fieldset>
                            </form>
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="listar-register-modal">
                            <form method="POST" action="{{ route('register') }}"
                                  class="ajax-form listar-formtheme listar-formlogin">
                                <fieldset>
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-group listar-inputwithicon {{ $errors->has('name') ? ' has-error' : '' }}">
                                            <i class="icon-profile-male"></i>
                                            <input type="text" name="name" class="form-control"
                                                   placeholder="@lang('User::attributes.user.name')">
                                            @if ($errors->has('name'))
                                                <div class="help-block">
                                                    <strong>{{ $errors->first('name') }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-group listar-inputwithicon {{ $errors->has('last_name') ? ' has-error' : '' }}">
                                            <i class="icon-profile-male"></i>
                                            <input type="text" name="last_name" class="form-control"
                                                   placeholder="@lang('User::attributes.user.last_name')">
                                            @if ($errors->has('last_name'))
                                                <div class="help-block">
                                                    <strong>{{ $errors->first('last_name') }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="form-group listar-inputwithicon {{ $errors->has('email') ? ' has-error' : '' }}">
                                            <i class="icon-icons208"></i>
                                            <input name="email" type="text" onClick="this.select()"
                                                   class="form-control"
                                                   placeholder="@lang('User::attributes.user.email')">
                                            @if ($errors->has('email'))
                                                <div class="help-block">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-group listar-inputwithicon {{ $errors->has('password') ? ' has-error' : '' }}">
                                            <i class="icon-lock-stripes"></i>
                                            <input name="password" type="password"
                                                   onClick="this.select()"
                                                   class="form-control"
                                                   placeholder="@lang('User::attributes.user.password')">
                                            @if ($errors->has('password'))
                                                <div class="help-block">
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
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
                                    </div>

                                    @if($is_two_factor_auth_enabled = \Settings::get('two_factor_auth_enabled', false))
                                        <div class="form-group listar-inputwithicon {{ $errors->has('phone_country_code') ? ' has-error' : '' }}">
                                            <i class="fa fa-flag"></i>
                                            <select class="form-control" id="authy-countries"
                                                    name="phone_country_code"></select>
                                            <span class="glyphicon glyphicon-flag form-control-feedback"></span>

                                            @if ($errors->has('phone_country_code'))
                                                <div class="help-block">
                                                    <strong>{{ $errors->first('phone_country_code') }}</strong>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="form-group listar-inputwithicon {{ $errors->has('phone_number') ? ' has-error' : '' }}">
                                            <i class="fa fa-phone"></i>
                                            <input id="authy-cellphone" class="form-control"
                                                   placeholder="@lang('User::attributes.user.cell_phone_number')"
                                                   value="{{ old('phone_number') }}"
                                                   name="phone_number"/>
                                            <span class="glyphicon glyphicon-phone form-control-feedback"></span>

                                            @if ($errors->has('phone_number'))
                                                <div class="help-block">
                                                    <strong>{{ $errors->first('phone_number') }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="form-group listar-inputwithicon {{ $errors->has('terms') ? ' has-error' : '' }}">
                                        <div class="listar-checkbox">
                                            <input class="custom-login" name="terms" value="1" id="terms-login"
                                                   type="checkbox"
                                                   required>
                                            <label for="terms-login">@lang('corals-directory-listing-star::auth.agree')
                                                <a href="#" data-toggle="modal"
                                                   id="terms-anchor"
                                                   data-target="#terms">@lang('corals-directory-listing-star::auth.terms')</a>
                                            </label>
                                            @if ($errors->has('terms'))
                                                <span class="help-block"><strong>@lang('corals-directory-listing-star::auth.accept_terms')</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                    <button type="submit"
                                            class="listar-btn listar-btngreen flex-center">@lang('corals-directory-listing-star::auth.register')</button>
                                </fieldset>
                            </form>
                        </div>
                        @component('components.modal',['id'=>'terms','header'=>\Settings::get('site_name').' Terms and policy'])
                        {!! \Settings::get('terms_and_policy') !!}
                        @endcomponent
                    </div>
                </div>
                <div class="listar-shareor"><span>@lang('corals-directory-listing-star::labels.template.home.or')</span>
                </div>
                <div class="listar-signupwith">
                    <h2>@lang('corals-directory-listing-star::auth.sign_in')</h2>
                    <ul class="listar-signinloginwithsocialaccount">
                        @if(config('services.facebook.client_id'))
                            <li class="listar-facebook"><a
                                        href="{{ route('auth.social', 'facebook') }}"><i
                                            class="icon-facebook-1"></i><span>@lang('corals-directory-listing-star::auth.sign_in_facebook')</span></a>
                            </li>
                        @endif
                        @if(config('services.twitter.client_id'))
                            <li class="listar-twitter"><a
                                        href="{{ route('auth.social', 'twitter') }}"><i
                                            class="icon-twitter-1"></i><span>@lang('corals-directory-listing-star::auth.sign_in_twitter')</span></a>
                            </li>
                        @endif
                        @if(config('services.google.client_id'))
                            <li class="listar-googleplus"><a
                                        href="{{ route('auth.social', 'google') }}"><i
                                            class="icon-google4"></i><span>@lang('corals-directory-listing-star::auth.sign_in_google')</span></a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@section('js')
    {!! \Html::script('assets/corals/plugins/authy/form.authy.js') !!}
    <script type="text/javascript">
        $('#country-div').on("DOMSubtreeModified", function () {
            $(".countries-input").addClass('form-control');
        });
    </script>

    @php \Actions::do_action('admin_footer_js') @endphp

@endsection

