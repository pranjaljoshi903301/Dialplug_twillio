@extends('layouts.public')

@section('editable_content')
    @include('partials.page_header')
    <div class="container padding-bottom-2x mb-2">
        {!! $item->rendered !!}
        <div class="row">
            <div class="col-lg-12">
                <form id="main-contact-form" class="contact-form ajax-form" name="contact-form" method="post"
                      data-page_action="clearContactForm"
                      action="{{ url('contact/email') }}">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>@lang('corals-ecommerce-ultimate::labels.template.contact.name')</label>
                                <input type="text" name="name" class="form-control form-control-rounded">
                            </div>
                            <div class="form-group">
                                <label>@lang('corals-ecommerce-ultimate::labels.template.contact.email')</label>
                                <input type="email" name="email" class="form-control form-control-rounded">
                            </div>
                            <div class="form-group">
                                <label>@lang('corals-ecommerce-ultimate::labels.template.contact.phone')</label>
                                <input type="text" name="phone" class="form-control form-control-rounded">
                            </div>
                            <div class="form-group">
                                <label>@lang('corals-ecommerce-ultimate::labels.template.contact.company_name')</label>
                                <input type="text" name="company" class="form-control form-control-rounded">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>@lang('corals-ecommerce-ultimate::labels.template.contact.subject')</label>
                                <input type="text" name="subject" class="form-control form-control-rounded">
                            </div>
                            <div class="form-group">
                                <label>@lang('corals-ecommerce-ultimate::labels.template.contact.message')</label>
                                <textarea name="message" id="message"
                                          class="form-control form-control-rounded"
                                          rows="10"></textarea>
                            </div>
                            <div class="form-group">

                                {!! NoCaptcha::display() !!}

                            </div>
                            <div class="form-group text-right">
                                <button type="submit" name="submit" class="btn btn-outline-primary"
                                        required="required">
                                    @lang('corals-ecommerce-ultimate::labels.template.contact.submit_message')
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7">
                <div class="display-3 text-muted opacity-75 mb-30">Customer Service</div>
            </div>
            <div class="col-md-5">
                <ul class="list-icon">
                    <li><i class="icon-mail text-muted"></i><a class="navi-link"
                                                               href="mailto:customer.service@unishop.com">customer.service@unishop
                            .com</a>
                    </li>
                    <li><i class="icon-phone text-muted"></i>+1 (080) 44 357 260</li>
                    <li><i class="icon-clock text-muted"></i>1 - 2 business days</li>
                </ul>
            </div>
        </div>
        <hr class="margin-top-2x">
        <div class="row margin-top-2x">
            <div class="col-md-7">
                <div class="display-3 text-muted opacity-75 mb-30">Tech Support</div>
            </div>
            <div class="col-md-5">
                <ul class="list-icon">
                    <li><i class="icon-mail text-muted"></i><a class="navi-link" href="mailto:support@unishop.com">support@unishop
                            .com</a>
                    </li>
                    <li><i class="icon-phone text-muted"></i>00 33 169 7720</li>
                    <li><i class="icon-clock text-muted"></i>1 - 2 business days</li>
                </ul>
            </div>
        </div>
        <h3 class="margin-top-3x text-center mb-30">Outlet Stores</h3>
        <div class="row">
            <div class="col-md-4 col-sm-6">
                <div class="card mb-30">
                    <div class="map">
                        <iframe src="{{ \Settings::get('google_map_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3387.331591494841!2d35.19981536504809!3d31.897586781246385!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x518201279a8595!2sLeaders!5e0!3m2!1sen!2s!4v1512481232226') }}"
                                width="358" height="250" frameborder="0" style="border:0" allowfullscreen></iframe>
                    </div>
                    <div class="card-body">
                        <ul class="list-icon">
                            <li><i class="icon-map-pin text-muted"></i>514 S. Magnolia St. Orlando, FL 32806, USA</li>
                            <li><i class="icon-phone text-muted"></i>+1 (786) 322 560 40</li>
                            <li><i class="icon-mail text-muted"></i><a class="navi-link"
                                                                       href="mailto:orlando.store@unishop.com">orlando.store@unishop
                                    .com</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="card mb-30">
                    <div class="map">
                        <iframe src="{{ \Settings::get('google_map_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3387.331591494841!2d35.19981536504809!3d31.897586781246385!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x518201279a8595!2sLeaders!5e0!3m2!1sen!2s!4v1512481232226') }}"
                                width="358" height="250" frameborder="0" style="border:0" allowfullscreen></iframe>
                    </div>
                    <div class="card-body">
                        <ul class="list-icon">
                            <li><i class="icon-map-pin text-muted"></i>44 Shirley Ave. West Chicago, IL 60185, USA</li>
                            <li><i class="icon-phone text-muted"></i>+1 (847) 252 765 33</li>
                            <li><i class="icon-mail text-muted"></i><a class="navi-link"
                                                                       href="mailto:chicago.store@unishop.comm">chicago.store@unishop
                                    .com</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="card mb-30">
                    <div class="map">
                        <iframe src="{{ \Settings::get('google_map_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3387.331591494841!2d35.19981536504809!3d31.897586781246385!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x518201279a8595!2sLeaders!5e0!3m2!1sen!2s!4v1512481232226') }}"
                                width="358" height="250" frameborder="0" style="border:0" allowfullscreen></iframe>
                    </div>
                    <div class="card-body">
                        <ul class="list-icon">
                            <li><i class="icon-map-pin text-muted"></i>89 Clinton Dr. Holbrook, NY 11741, USA</li>
                            <li><i class="icon-phone text-muted"></i>+1 (212) 477 690 000</li>
                            <li><i class="icon-mail text-muted"></i><a class="navi-link"
                                                                       href="mailto:newyork.store@unishop.com">newyork.store@unishop
                                    .com</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Site Footer-->
@stop


@section('js')

    {!! NoCaptcha::renderJs() !!}

@endsection
