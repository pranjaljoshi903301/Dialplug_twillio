<section>
    <div class="container">
        <div class="author big">
            <div class="author-image">
                <div class="background-image">
                    <img src="{{ asset($productUser->picture) }}" alt="{{ $productUser->full_name }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <!--end author-image-->
                    <div class="author-description">
                        <div class="section-title">
                            <h2>{!! $productUser->full_name !!}</h2>
                            <figure>
                                <div class="float-left">
                                    @if(\Settings::get('classified_rating_enable',true))
                                        @include('partials.components.rating',['rating'=> $productUser->averageRating(1)[0],'rating_count'=>$productUser->countRating()[0] ])
                                    @endif
                                </div>
                                <div class="text-align-right social">
                                    <a href="#">
                                        <i class="fa fa-facebook-square"></i>
                                    </a>
                                    <a href="#">
                                        <i class="fa fa-twitter-square"></i>
                                    </a>
                                    <a href="#">
                                        <i class="fa fa-instagram"></i>
                                    </a>
                                </div>
                            </figure>
                        </div>
                        <div class="additional-info">
                            <ul>
                                <li>
                                    <figure>@lang('corals-classified-craigs::labels.partial.email')</figure>
                                    <aside><a href="mailto:{{ $productUser->email }}"
                                              class="btn btn-primary text-caps btn-primary"><i
                                                    class="fa fa-envelope"></i></a></aside>
                                </li>
                                <li>
                                    @if(!empty($productUser->phone))
                                        <a href="#" class="btn btn-common call">
                                            <i class="fa fa-phone" aria-hidden="true"></i>
                                            <span class="phonenumber">{{$productUser->phone}}</span></a>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    @include('partials.user_reviews',['productUser'=> $productUser])
                </div>
            </div>
        </div>
    </div>
    <div class="background">
        <div class="background-image original-size background-repeat-x">
            <img src="assets/img/gradient-background.png" alt="">
        </div>
        <!--end background-image-->
    </div>
    <!--end background-->
</section>


