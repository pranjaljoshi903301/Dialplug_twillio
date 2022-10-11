<a class="wishlist-icon " data-toggle="tooltip"
   title="@lang('corals-classified-craigs::labels.wishlist.title')" data-color="red" data-style="zoom-in"
   href="{{ url('classified/wishlist/'.$product->hashed_id) }}"
   data-action="post" data-page_action="toggleWishListProduct"
   data-wishlist_product_hashed_id="{{$product->hashed_id}}">
    <i data-wislist_class="{{$product->hashed_id}}"
       class="fa fa-heart {{!is_null($wishlist) ? 'lni-heart-filled':''}}"></i>
</a>