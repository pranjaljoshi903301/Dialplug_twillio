@if(!user())
    <div class="alert alert-info alert-dismissible text-center margin-bottom-1x in"
         style="margin-bottom: 40px;"><span
                class="alert-close"
                data-dismiss="alert"></span><i class="fa fa-exclamation-circle" aria-hidden="true"></i>
        @lang('corals-directory-listing-star::labels.partial.tabs.need_login_review')
    </div>
@elseif(user()->hashed_id != optional($listing->owner())->hashed_id)
    @if(user()->can('Utility::rating.create'))
        <div class="listar-formreviewarea">
            <h3>@lang('corals-directory-listing-star::labels.template.product_single.add_review')</h3>
            <form class="listar-formtheme listar-formaddreview ajax-form"
                  action="{{url('directory/user/'.$listing->hashed_id.'/rate' )}}"
                  method="POST" id="reviewForm" data-page_action="site_reload">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <fieldset>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <div class="listar-rating">
                                <p>@lang('corals-directory-listing-star::labels.template.product_single.your_rating_for_this_listing')</p>
                                <div class="leave-rating">
                                    <div id="custom-rating">
                                        <input type="radio" name="review_rating" id="rating-1"
                                               value="5"/>
                                        <label for="rating-1" class="fa fa-star-o"></label>
                                        <input type="radio" name="review_rating" id="rating-2"
                                               value="4"/>
                                        <label for="rating-2" class="fa fa-star-o"></label>
                                        <input type="radio" name="review_rating" id="rating-3"
                                               value="3"/>
                                        <label for="rating-3" class="fa fa-star-o"></label>
                                        <input type="radio" name="review_rating" id="rating-4"
                                               value="2"/>
                                        <label for="rating-4" class="fa fa-star-o"></label>
                                        <input type="radio" name="review_rating" id="rating-5"
                                               value="1"/>
                                        <label for="rating-5" class="fa fa-star-o"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <input type="text" name="review_subject"
                                       class="form-control"
                                       placeholder="@lang('corals-directory-listing-star::labels.template.product_single.subject') *">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                                                    <textarea name="review_text" class="form-control"
                                                                              placeholder="@lang('corals-directory-listing-star::labels.template.product_single.your_review'):"></textarea>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <button class="listar-btn listar-btngreen"
                                    type="submit">@lang('corals-directory-listing-star::labels.template.product_single.submit_review')
                            </button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    @endif
@else
    <div class="alert alert-info alert-dismissible text-center margin-bottom-1x in"
         style="margin-bottom: 40px;"><span
                class="alert-close"
                data-dismiss="alert"></span><i class="fa fa-exclamation-circle" aria-hidden="true"></i>
        @lang('corals-directory-listing-star::labels.partial.tabs.owner_of_listing')
    </div>
@endif
