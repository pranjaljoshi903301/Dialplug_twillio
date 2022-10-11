<div class="row  m-10">
    <div class="col-md-12 text-right">
        @if(user() && user()->can('Utility::comment.create'))
            <button type="button"
                    class="panel_toggle_btn listar-helpful m-r-10"
                    data-toggle="collapse" data-target="#comments_box_{{ $review->id }}">
                <i class="fa fa-plus"></i> @lang('corals-directory-listing-star::labels.template.product_single.add_comments')
            </button>
            <button type="button"
                    class="panel_toggle_btn listar-helpful pull-center flat-btn m-l-10"
                    data-toggle="collapse" data-target="#review_comments_{{ $review->id }}">
                <i class="fa comment-o"></i> @lang('corals-directory-listing-star::labels.template.product_single.comments_count',['count'=>count($review->comments)])
            </button>
        @endif
        @auth

        @endauth
        <div class="clearfix"></div>
    </div>
</div>
@if(user() && user()->can('Utility::comment.create'))
    <div class="row  m-10">
        <div class="col-md-12">
            <div class="m-t-20" id="comments_box_{{ $review->id }}" style="">
                <div class="row">
                    <div class="col-md-2">
                        <img src="{{  user()->picture_thumb }}"
                             alt="{{ user()->name }}"><br>
                    </div>
                    <div class="col-md-10">
                        <form class="custom-form ajax-form"
                              action="{{url('directory/user/'.$review->hashed_id.'/create-comment' )}}"
                              method="POST"
                              data-page_action="site_reload">
                            <div class="form-group required-field">
                                <textarea name="body" class="form-control custom-radius" cols="10" rows="2" style="height: 80px"
                                          placeholder="@lang('corals-directory-listing-star::labels.template.product_single.add_comments')"></textarea>
                            </div>
                            <button type="submit"
                                    class="btn small-btn color-bg flat-btn pull-right"
                                    style="margin: 0;">@lang('corals-directory-listing-star::labels.template.product_single.add_comment')
                                <i class="fa fa-paper-plane-o"
                                   aria-hidden="true"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
<div class="review_comments" id="review_comments_{{ $review->id }}">
    @if(count($review->comments))
        @foreach($review->comments as $comment)
            <div class="reviews-comments-item">
                <div class="review-comments-avatar">
                    <img src="{{ $comment->comment_author->picture_thumb }}"
                         alt="{{$comment->comment_author->full_name}}" style="max-width: 72px"><br>
                </div>
                <div class="reviews-comments-item-text">
                    <div class="clearfix"></div>

                    <p>{{$comment->body}}</p>
                    <span class="reviews-comments-item-date">
                                <i class="fa fa-calendar-check-o"></i>
                        {{ $comment->created_at->diffForHumans() }}
                            </span>
                </div>
            </div>
        @endforeach
    @endif
</div>
