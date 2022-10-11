@extends('layouts.master')

@section('editable_content')
    @include('partials.page_header',['content'=>$item->rendered])
    <div class="container">
        <div class="space-90"></div>
        <div class='row'>
            <div class="col-md-12 margin-b-40">
                <form id="main-contact-form" class="saas-contact ajax-form" name="contact-form" method="post"
                      data-page_action="clearContactForm"
                      action="{{ url('contact/email') }}">
                    {{ csrf_field() }}
                    <div class='row'>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" name="name" class="form-control"
                                       placeholder="Name *">
                            </div>
                            <div class="form-group">
                                <input type="email" name="email" class="form-control"
                                       placeholder="Email *">
                            </div>
                            <div class="form-group">
                                <input type="text" name="phone" class="form-control" placeholder="Phone">
                            </div>
                            <div class="form-group">
                                <input type="text" name="company" class="form-control" placeholder="Company Name">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" name="subject" class="form-control"
                                       placeholder="Subject *">
                            </div>
                            <div class="form-group">
                                <textarea name="message" id="message" class="form-control"
                                          rows="6" placeholder="Message *"></textarea>
                            </div>
                            <div class="form-group">

                                {!! NoCaptcha::display() !!}

                            </div>
                            <div class="form-group text-right">
                                <button type="submit" name="submit" class="btn btn-primary btn-rounded">
                                    @lang('corals-saas::labels.template.send_message')
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--Google Maps-->
    <div class="google-map-container margin-b-60">
        <iframe src="{{ \Settings::get('google_map_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3387.331591494841!2d35.19981536504809!3d31.897586781246385!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x518201279a8595!2sLeaders!5e0!3m2!1sen!2s!4v1512481232226') }}"
                width="100%" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
    </div>
    <div class="cta-skin">
        <div class="container text-center">
            <h2> @lang('corals-saas::labels.template.are_you_interested')</h2>
            <p> @lang('corals-saas::labels.template.give_you_platform')</p>
            <a href="{{ url('register') }}"
               class="btn btn-rounded btn-white-border">@lang('corals-saas::labels.template.create_account')</a>
        </div>
    </div>
@stop

@section('js')

    {!! NoCaptcha::renderJs() !!}

@endsection