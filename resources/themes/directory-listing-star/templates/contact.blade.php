@extends('layouts.public')

@section('content')
    <main id="listar-main" class="listar-main listar-haslayout">
        <iframe src="{{ \Settings::get('google_map_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3387.331591494841!2d35.19981536504809!3d31.897586781246385!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x518201279a8595!2sLeaders!5e0!3m2!1sen!2s!4v1512481232226') }}"
                height="600" frameborder="0" style="border:0"
                allowfullscreen></iframe>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div id="listar-content" class="listar-content">
                        <div class="listar-contactusarea">
                            <div class="col-xs-12 col-sm-12 col-md-7 col-lg-8 pull-left">
                                <div class="row">
                                    {!! $item->rendered !!}
                                    <form id="main-contact-form" class="listar-formtheme listar-formcontactus ajax-form"
                                          method="post" data-page_action="clearContactForm"
                                          action="{{ url('contact/email') }}" name="contact-form">
                                        {{ csrf_field() }}
                                        <fieldset>
                                            <h2>@lang('corals-directory-listing-star::labels.template.contact.contact_form')</h2>
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" name="name" id="name" value=""
                                                               class="form-control"
                                                               placeholder="@lang('corals-directory-listing-star::labels.template.contact.name')">
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                    <div class="form-group">
                                                        <input type="email" name="email" id="email" class="form-control"
                                                               placeholder="@lang('corals-directory-listing-star::labels.template.contact.email')">
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" name="phone" id="phone" class="form-control"
                                                               placeholder="@lang('corals-directory-listing-star::labels.template.contact.phone')">
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" name="company" id="phone"
                                                               class="form-control"
                                                               placeholder="@lang('corals-directory-listing-star::labels.template.contact.company_name')">
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" name="subject" id="phone"
                                                               class="form-control"
                                                               placeholder="@lang('corals-directory-listing-star::labels.template.contact.subject')">
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                    <div class="form-group">
                                                        <textarea class="form-control" name="message" id="message"
                                                                  placeholder="@lang('corals-directory-listing-star::labels.template.contact.message')"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                    <div class="form-group">

                                                        {!! NoCaptcha::display() !!}

                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                    <button class="listar-btn listar-btngreen" id="submit"
                                                            type="submit">
                                                        @lang('corals-directory-listing-star::labels.template.contact.submit_message')
                                                    </button>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </form>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-5 col-lg-4 pull-right">
                                <div class="row">
                                    <div class="listar-contactinfo">
                                        <h2>@lang('corals-directory-listing-star::labels.template.contact.contact_form')</h2>
                                        <ul class="listar-contactinfolist">
                                            <li>
                                                <i class="icon-icons208"></i>
                                                <span><a href="mailto:listingstar@gmail.com">{{ \Settings::get('contact_form_email','support@corals.io') }}</a></span>
                                            </li>
                                        </ul>
                                        <ul class="listar-socialicons listar-socialiconsborder">
                                            @foreach(\Settings::get('social_links',[]) as $key=>$link)
                                                <li class="listar-{{$key}}"><a href="{{ $link }}" target="_blank"><i
                                                                class="fa fa-{{ $key }}"></i></a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!--************************************
            Main End
    *************************************-->
    <!--************************************
            Footer Start
    *************************************-->
@stop

<!-- contentend -->
@section('js')

    {!! NoCaptcha::renderJs() !!}

@endsection