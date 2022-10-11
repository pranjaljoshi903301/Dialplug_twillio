<div id="comments">
    <div class="comment-box">
        @if(!user())
            <div class="alert alert-info alert-dismissible fade show text-center margin-bottom-1x"><span
                        class="alert-close"
                        data-dismiss="alert"></span><i class="icon-layers"></i>
                @lang('corals-classified-craigs::labels.partial.tabs.need_login_review')
            </div>
        @else
            <section>
                <button type="button" class="btn btn-primary small" data-toggle="collapse" data-target="#demo">@lang('corals-classified-craigs::labels.partial.write_review')
                </button>
                <div class=" collapse" id="demo">
                    {!! Form::open( ['url' => url('classified/user/'.\Request::get('user').'/rate'),'method'=>'POST', 'class'=>'ajax-form ']) !!}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! CoralsForm::text('review_subject','corals-classified-craigs::attributes.tab.subject',true) !!}
                            </div>
                            <!--end form-group-->
                        </div>
                        <!--end col-md-8-->
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! CoralsForm::select('review_rating', 'corals-classified-craigs::attributes.tab.rating', trans('corals-classified-craigs::attributes.tab.rating_option'),true, null,['class'=>'ml-1']) !!}
                            </div>
                            <!--end form-group-->
                        </div>
                        <!--end col-md-4-->
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! CoralsForm::textarea('review_text','corals-classified-craigs::attributes.tab.review',true,null,['rows'=>4]) !!}
                            </div>
                            {!! CoralsForm::button('corals-classified-craigs::labels.partial.tabs.submit_review',['class'=>'btn btn-primary ladda-button small'], 'submit') !!}
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </section>
        @endif
        <section>
            @if($productUser)
                <h2>@lang('corals-classified-craigs::labels.partial.tabs.reviews')</h2>
                <div class="comments">
                    @foreach($productUser->ratings as $review)
                        <div class="media">
                            <div class="info-body">
                                <div class="media-heading d-flex justify-content-between">
                                    <h4 class="name">{{ $review->title }}</h4>

                                    <span class="comment-date">
                                    <i class="lni-alarm-clock"></i> {{$review->created_at->diffForHumans()}}
                                </span>
                                </div>
                                <div class="">
                                    @include('partials.components.rating',['rating'=> $review->rating,'rating_count'=>null ])
                                </div>
                                <div>
                                    {{ $review->body }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>