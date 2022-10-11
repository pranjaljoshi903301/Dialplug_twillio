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
                        <form class="form clearfix" method="post" action="{{ route('password.email') }}"
                              id="login-form">
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
                                       placeholder="@lang('User::attributes.user.email')" value="{{ old('email') }}"
                                       autofocus required>
                            </div>
                            @if ($errors->has('email'))
                                <div class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between align-items-baseline">
                                <button type="submit"
                                        class="btn btn-primary">@lang('corals-classified-craigs::auth.send_password_reset')</button>
                            </div>
                        </form>
                    </div>
                    <!--end col-md-6-->
                </div>
                <!--end row-->
            </div>
            <!--end container-->
        </section>
    </section>
@endsection
