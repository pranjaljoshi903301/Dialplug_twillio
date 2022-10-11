<ul id="listar-comments" class="listar-comments">
    <li>
        <div class="listar-comment">
            <div class="listar-commentauthorbox">
                <figure><a href="javascript:void(0);"><img
                                src="{{ $review->author->picture_thumb }}"
                                alt="{{$review->author->full_name}}" style="max-width: 75px"></a></figure>
                <div class="listar-authorinfo">
                    @if ($show_status)
                        <div class="pull-right">{!! $review->present('status') !!}</div>
                    @endif
                    <h3>{{$review->title}} {!!   $show_name ? ' - <a class="reviews-comments-item-link" target="_blank" href="'.$review->reviewrateable->getShowURL().'">'.  $review->reviewrateable->getIdentifier()  .'</a> ': ''  !!}</h3>
                    <em>{{$review->author->full_name}}</em>
                    @include('partials.components.rating',['rating'=> $review->rating,'rating_count'=>null ])
                </div>
            </div>
            <div class="listar-commentcontent">
                <time datetime="2017-09-09">
                    <i class="icon-alarmclock"></i>
                    <span>{{$review->created_at->diffForHumans()}}</span>
                </time>
                <div class="listar-description">
                    <p>{{$review->body}}</p>
                </div>
                @include('partials.components.review_comments',['review'=> $review])
            </div>
            @auth
            <div id="custom-menu">
                {!! $review->present('action')  !!}
            </div>
            @endauth
        </div>
    </li>
</ul>