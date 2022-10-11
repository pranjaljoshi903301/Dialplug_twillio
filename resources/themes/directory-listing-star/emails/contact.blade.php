{{trans('corals-directory-listing-star::labels.email.received_message',['name' => $name])}}

<p>
    {{trans('corals-directory-listing-star::labels.email.name',['name' => $name])}}
</p>

<p>
    {{trans('corals-directory-listing-star::labels.email.name_email',['name' => $email])}}
</p>


<p>
    {{trans('corals-directory-listing-star::labels.email.subject',['name' => 'Contact message for: '.\Request::get('listing_name')])}}
</p>

<p>
    {{trans('corals-directory-listing-star::labels.email.message',['name' =>  \Request::get('message')])}}
</p>