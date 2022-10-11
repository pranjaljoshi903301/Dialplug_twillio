<a
        class="listar-btnquickinfo {{ $wishlist ? '' : 'gray' }}"
        data-toggle="tooltip"
        data-style="zoom-in"
        href="{{ url('directory/wishlist/'.$listing->hashed_id) }}"
        data-action="post" data-page_action="toggleWishListListing"
        data-wishlist_product_hashed_id="{{$listing->hashed_id}}"
                data-wishlist_class="{{$listing->hashed_id}}">
    <i
            class="icon-heart-active"></i>
</a>
