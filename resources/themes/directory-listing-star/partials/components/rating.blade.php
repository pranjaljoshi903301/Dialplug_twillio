<span style="display: flex">
    @for($i = 1 ; $i <= 5; $i++)
                <i class="fa fa-star{{ $rating >= $i ?  ' active' : '-o' }}"></i>
    @endfor
    @if($rating_count)
        <em>&nbsp;@lang('corals-directory-listing-star::labels.partial.component.customer_review',['name' => $rating ,'count' => $rating_count ])</em>
    @endif
</span>
