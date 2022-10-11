@extends('layouts.public')

@section('title',trans('corals-classified-craigs::auth.reset_password'))

@section('page_header')
    @include('partials.page_header',['content'=> '<h2 class="product-title">'.trans('corals-classified-craigs::auth.reset_password').'</h2>'])
@endsection

@section('content')
    <section class="content">
        <section class="block">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <form class="form clearfix"method="POST" action="{{ route('password.request') }}" id="login-form">
                            {{csrf_field()}}

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" id="email"
                                       class="col-form-label required">@lang('User::attributes.user.email')</label>
                                <input name="email" type="email" class="form-control" id="email"
                                       placeholder="@lang('User::attributes.user.email')" value="{{ old('email') }}"
                                       autofocus required>
                            </div>
                            @if ($errors->has('email'))
                                <div class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </div>
                            @endif

                        <!--end form-group-->
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

                            <div class="form-group">
                                <label for="password_confirmation"
                                       class="col-form-label required">@lang('User::attributes.user.password')</label>
                                <input name="password_confirmation" type="password" class="form-control" id="password"
                                       placeholder="@lang('User::attributes.user.retype_password')" required>
                            </div>
                            @if ($errors->has('password_confirmation'))
                                <div class="help-block">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between align-items-baseline">
                                <button type="submit" class="btn btn-primary">@lang('corals-classified-craigs::auth.reset_password')</button>
                            </div>
                        </form>
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
