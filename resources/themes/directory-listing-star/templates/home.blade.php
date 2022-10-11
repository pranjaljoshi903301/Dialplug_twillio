@extends('layouts.public')

@section('body_class','listar-home listar-homeone')
@section('class_header','listar-header cd-auto-hide-header listar-haslayout listar_darkheader')
@section('content')


    <div id="listar-wrapper" class="listar-wrapper listar-haslayout">
        @include('partials.header')

        <div class="listar-homebannerslider">
            <div id="listar-homeslider" class="listar-homeslider owl-carousel">
                <div class="item">
                    <figure><img src="{{\Theme::url('images/slider/img-01.jpg')}}" alt="image description"></figure>
                </div>
                <div class="item">
                    <figure><img src="{{\Theme::url('images/slider/img-02.jpg')}}" alt="image description"></figure>
                </div>
                <div class="item">
                    <figure><img src="{{\Theme::url('images/slider/img-03.jpg')}}" alt="image description"></figure>
                </div>
                <div class="item">
                    <figure><img src="{{\Theme::url('images/slider/img-04.jpg')}}" alt="image description"></figure>
                </div>
                <div class="item">
                    <figure><img src="{{\Theme::url('images/slider/img-05.jpg')}}" alt="image description"></figure>
                </div>
            </div>
            <div class="listar-homebanner">
                @php
                    \Actions::do_action('pre_content',$item, $home??null);
                @endphp
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="listar-bannercontent">
                                {!! $item->rendered !!}
                                @include('partials.home_page_filter')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <main id="listar-main" class="listar-main listar-haslayout">
            @include('partials.featured_category')
            {!!   \Shortcode::compile( 'block','blocks-home' ) ; !!}
            @include('partials.latest_listings')
            @include('partials.blog')
            <section class="listar-haslayout">
                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 pull-left">
                    <div class="row">
                        <div class="listar-postfirstlisting">
                            <figure><a href="{{url('directory/user/listings/create')}}"><img
                                            src="{{\Theme::url('images/placeholder-03.png')}}"
                                            alt="image description"></a></figure>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 pull-right">
                    <div class="row">
                        <div class="listar-followus">
                            <figure><a href="javascript:void(0);"><img
                                            src="{{\Theme::url('images/placeholder-04.png')}}"
                                            alt="image description"></a></figure>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                    <div class="row">
                        <div class="listar-newsletter">
                            <div class="listar-newsletteroverlay">
                                <h2>@lang('corals-directory-listing-star::labels.footer.subscribe')</h2>
                                <div class="listar-description">
                                    <p>@lang('corals-directory-listing-star::labels.footer.subscribe_description')</p>
                                </div>
                                {!! Form::open( ['url' => url('utilities/newsletter/subscribe'),'method'=>'POST', 'class'=>'ajax-form listar-formtheme listar-formnewsletter','id'=>'subscribe']) !!}
                                <fieldset>
                                    <input type="email" name="email" id="subscribe-email" class="form-control"
                                           placeholder="@lang('corals-directory-listing-star::labels.template.home.your_email')">
                                    <input type="hidden" name="list_id">
                                    <button type="submit" id="subscribe-button"><i class="icon-arrow-right2"></i>
                                    </button>
                                </fieldset>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    @include('partials.news')
@stop
@section('js')
    @parent
    <script type="text/javascript">
        $(document).ready(function () {
            $("#shop_sort").change(function () {
                $("#filterSort").val($(this).val());

                $("#filterForm").submit();
            })
        });
    </script>
@endsection