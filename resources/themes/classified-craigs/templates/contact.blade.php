@extends('layouts.public')


@section('page_header')
    @include('partials.page_header')
@endsection

@section('content')
    <section class="content">
        <section class="block">
            <div class="map height-500px" id="map-contact">
                <iframe src="{{ \Settings::get('google_map_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3387.331591494841!2d35.19981536504809!3d31.897586781246385!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x518201279a8595!2sLeaders!5e0!3m2!1sen!2s!4v1512481232226') }}"
                        height="450" frameborder="0" style="border:0;width: 100%;" allowfullscreen></iframe>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        {!! $item->content !!}
                        <br>
                        <figure class="with-icon">
                            <i class="fa fa-phone"></i>
                            <span>{{ \Settings::get('contact_mobile','+970599593301') }}</span>
                        </figure>
                        <figure class="with-icon">
                            <i class="fa fa-envelope"></i>
                            <a href="#">{{ user()->email }}</a>
                        </figure>
                    </div>
                    <div class="col-md-8">
                        <h2>@lang('corals-classified-craigs::labels.template.product.contact_form')</h2>
                        <form class="form email ajax-form" id="main-contact-form" name="contact-form" method="post"
                              data-page_action="clearContactForm"
                              action="{{ url('contact/email') }}">
                            {{csrf_field()}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="col-form-label required">
                                            @lang('corals-classified-craigs::email.contact_form.name')
                                        </label>
                                        <input name="name" type="text" class="form-control" id="name"
                                               placeholder="@lang('corals-classified-craigs::email.contact_form.name')">
                                    </div>
                                    <!--end form-group-->
                                </div>
                                <!--end col-md-6-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="col-form-label required">
                                            @lang('corals-classified-craigs::email.contact_form.email')
                                        </label>
                                        <input name="email" type="email" class="form-control" id="email"
                                               placeholder="@lang('corals-classified-craigs::email.contact_form.email')">
                                    </div>
                                    <!--end form-group-->
                                </div>
                                <!--end col-md-6-->
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="col-form-label required">
                                            @lang('corals-classified-craigs::email.contact_form.phone')
                                        </label>
                                        <input type="text" class="form-control" id="msg_phone" name="phone"
                                               placeholder="@lang('corals-classified-craigs::email.contact_form.phone')">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="col-form-label required">
                                            @lang('corals-classified-craigs::email.contact_form.name')
                                        </label>
                                        <input type="text" class="form-control" id="company_name"
                                               name="company"
                                               placeholder="@lang('corals-classified-craigs::email.contact_form.company_name')">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="subject" class="col-form-label">
                                    @lang('corals-classified-craigs::email.contact_form.subject')
                                </label>
                                <input name="subject" type="text" class="form-control" id="subject"
                                       placeholder="@lang('corals-classified-craigs::email.contact_form.subject')">
                            </div>
                            <!--end form-group-->
                            <div class="form-group">
                                <label for="message" class="col-form-label required">
                                    @lang('corals-classified-craigs::email.contact_form.message')
                                </label>
                                <textarea name="message" id="message" class="form-control" rows="4"
                                          placeholder="@lang('corals-classified-craigs::email.contact_form.message')"></textarea>
                            </div>
                            <div class="form-group">

                                {!! NoCaptcha::display() !!}

                            </div>
                            <!--end form-group-->
                            <button type="submit" id="submit" class="btn btn-primary float-right">
                                @lang('corals-classified-craigs::email.contact_form.submit_message')
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </section>
@endsection

@section('js')

    {!! NoCaptcha::renderJs() !!}

@endsection
