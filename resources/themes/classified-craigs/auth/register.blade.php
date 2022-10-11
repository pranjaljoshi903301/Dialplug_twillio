@extends('layouts.public')

@section('page_header')
    @include('partials.page_header')
@endsection
@section('class_search','small')
@section('main_search')
    <a href="#collapseMainSearchForm" class="main-search-form-toggle" data-toggle="collapse" aria-expanded="false"
       aria-controls="collapseMainSearchForm">
        <i class="fa fa-search"></i>
        <i class="fa fa-close"></i>
    </a>
@endsection
@section('side_bar')
    <div class="collapse" id="collapseMainSearchForm">
        @include('partials.home_search')
    </div>
@endsection

@section('title',trans('corals-classified-craigs::auth.register'))
@section('css')
    {!! Html::style('assets/corals/plugins/authy/flags.authy.css') !!}
    {!! Html::style('assets/corals/plugins/authy/form.authy.css') !!}
    <style type="text/css">
        #terms {
            color: black;
        }

        input[name=name] {
            border-bottom-right-radius: 0px;
            border-top-right-radius: 0px;
        }

        input[name=last_name] {
            border-top-left-radius: 0px;
            border-bottom-left-radius: 0px;
        }
    </style>
@endsection

@section('content')
    <section class="content">
        <section class="block">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-6 col-lg-7 col-md-8 col-sm-10">
                        <form class="form clearfix ajax-form" method="POST" action="{{ route('register') }}">
                            {{csrf_field()}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                                        <label for="name"
                                               class="col-form-label required">{{ trans('User::attributes.user.name') }}</label>
                                        <input name="name" type="text" class="form-control" id="name"
                                               placeholder="@lang('User::attributes.user.name')"
                                               value="{{old('email')}}"
                                               autofocus required>
                                        @if ($errors->has('name'))
                                            <div class="help-block">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('last_name') ? ' has-error' : '' }}">
                                        <label for="name"
                                               class="col-form-label required">{{ trans('User::attributes.user.last_name') }}</label>
                                        <input name="last_name" type="text" class="form-control" id="last_name"
                                               placeholder="@lang('User::attributes.user.last_name')"
                                               value="{{old('last_name')}}"
                                               required>
                                        @if ($errors->has('last_name'))
                                            <div class="help-block">
                                                <strong>{{ $errors->first('last_name') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>


                            </div>

                            <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="name"
                                       class="col-form-label required">{{ trans('User::attributes.user.email') }}</label>
                                <input name="email" type="text" class="form-control" id="email"
                                       placeholder="@lang('User::attributes.user.email')" value="{{old('email')}}"
                                       required>
                                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                @if ($errors->has('email'))
                                    <div class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                                        <label for="name"
                                               class="col-form-label required">{{ trans('User::attributes.user.password') }}</label>
                                        <input name="password" type="password" class="form-control" id="password"
                                               placeholder="@lang('User::attributes.user.password')"
                                               required>
                                        @if ($errors->has('password'))
                                            <div class="help-block">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                        <label for="name"
                                               class="col-form-label required">{{ trans('User::attributes.user.retype_password') }}</label>
                                        <input name="password_confirmation" type="password" class="form-control"
                                               id="password"
                                               placeholder="@lang('User::attributes.user.password')"
                                               required>
                                        @if ($errors->has('password_confirmation'))
                                            <div class="help-block">
                                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!--end form-group-->
                            <div class="d-flex justify-content-between align-items-baseline {{ $errors->has('terms') ? ' has-error' : '' }}">
                                <label>
                                    <input name="terms" value="1" type="checkbox" required>
                                    @lang('corals-classified-craigs::auth.agree')
                                    <a href="#" data-toggle="modal" id="terms-anchor"
                                       data-target="#terms">@lang('corals-classified-craigs::auth.terms')</a>
                                    @if ($errors->has('terms'))
                                        <span class="help-block"><strong>@lang('corals-classified-craigs::auth.accept_terms')</strong></span>
                                    @endif
                                </label>
                                <button type="submit"
                                        class="btn btn-primary">@lang('corals-classified-craigs::auth.register')</button>
                            </div>
                        </form>
                        @component('components.modal',['id'=>'terms','header'=>\Settings::get('site_name').' Terms and policy'])
                            {!! \Settings::get('terms_and_policy') !!}
                        @endcomponent
                        <hr>
                    </div>
                    <!--end col-md-6-->
                </div>
                <!--end row-->
            </div>
            <!--end container-->
        </section>
        <!--end block-->
    </section>
@endsection

@section('js')
    {!! \Html::script('assets/corals/plugins/authy/form.authy.js') !!}
    <script type="text/javascript">
        $('#country-div').on("DOMSubtreeModified", function () {
            $(".countries-input").addClass('form-control');
        });
    </script>
@endsection
