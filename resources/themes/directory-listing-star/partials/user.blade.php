<div class="custom-user">
    <h4 class="text-center"><span>{{$userListing->name}}</span></h4>
    <div class="user-profile-avatar"><img src="{{$userListing->picture_thumb}}" style="max-width: 130px" alt="">
    </div>
    <div class="list-author-widget-contacts user-profile-listing">
        <ul>
            <li>
                <span><i class="fa fa-map-marker"></i> @lang('corals-directory-listing-star::labels.template.product_single.address')
                    :</span> <a href="#">{{$userListing->address}}</a></li>
            <li><span><i class="fa fa-phone"></i> @lang('corals-directory-listing-star::labels.template.product_single.phone')
                    :</span> <a>{{$userListing->phone}}</a></li>
            <li>
                <span><i class="fa fa-envelope-o"></i> @lang('corals-directory-listing-star::labels.template.product_single.email')
                    :</span> <a
                        href="mailto:{{$userListing->email}}">{{$userListing->email}}</a></li>
        </ul>
    </div>

</div>




