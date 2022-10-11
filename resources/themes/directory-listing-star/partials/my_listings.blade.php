@php
    $listings = \Corals\Modules\Directory\Facades\Directory::getListingsList(false,null,user()->id,true);
@endphp
@forelse( $listings as $listing)
    <div class="listar-themepost listar-placespost" data-scrollax-parent="true" id="{{'row_'.$listing->hashed_id}}">
        <a class="listar-btnedite" href="{{url('directory/user/listings/'.$listing->hashed_id.'/edit')}}"><i
                    class="icon-pencil4"></i></a>
        <a href="{{url('directory/user/listings/'.$listing->hashed_id)}}" data-action="delete"
           data-page_action="removeRow" data-action_data="{{ $listing->hashed_id }}"
           class="listar-btndelpost del-btn" style="position: absolute">x</a>
        <figure class="bg listar-featuredimg custom-background-my-listing"
                data-bg="{{$listing->image}}"
                data-scrollax="properties: { translateY: '30%' }"><a href="{{$listing->getShowURL()}}">
            </a>
        </figure>
        <div class="listar-postcontent">
            <h3><a href="{{$listing->getShowURL()}}">{!! \Str::limit($listing->name,20) !!}</a></h3>
            <span class="listar-catagory">{!! \Str::limit($listing->address??$listing->location->address,20) !!}</span>
            <div class="listar-reviewcategory">
                <div class="listar-review">
                    @if(\Settings::get('directory_rating_enable',true))
                        @include('partials.components.rating',['rating'=> $listing->averageRating(1)[0],'rating_count'=>$listing->countRating()[0] ])
                    @endif
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="alert alert-info alert-dismissible fade show text-center margin-bottom-1x"><span
                class="alert-close"
                data-dismiss="alert"></span><i class="fa fa-exclamation-circle" aria-hidden="true"></i>
        @lang('corals-directory-listing-star::labels.dashboard.sry_nothing_to_show')
    </div>
@endforelse

{!! $listings->links('partials.paginator') !!}

<script type="text/javascript">
    function removeRow(response, $form, hashedId) {
        $("#row_" + hashedId).fadeOut();
    }
</script>
