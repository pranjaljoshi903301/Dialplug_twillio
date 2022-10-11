<div class="stars-header-rating text-center" style="display: inline-block">
    <div class="meta">
            @for($i = 1 ; $i <= 5; $i++)
                <i class="active fa fa-star {{ $rating >= $i ?  'active' : 'd-none' }}" style="color: #ff0000;"></i>
            @endfor
    </div>
    @if($rating_count)
        <div class="text-muted">
            <a href="#comments">@lang('corals-classified-craigs::labels.partial.component.customer_review',['rating' => $rating ,'count' => $rating_count ])</a>
        </div>
    @endif
</div>