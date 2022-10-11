@extends('layouts.master')

@section('title', $title)

@section('content')

    <main id="listar-main" class="listar-main listar-haslayout">
        <div id="listar-content" class="listar-content">
            <form class="listar-formtheme listar-formaddlisting listar-formwishlist">
                <fieldset>
                    <div class="listar-boxtitle">
                        <h3>@lang('corals-directory-listing-star::labels.dashboard.my_wishlists')</h3>
                    </div>
                    <div class="listar-dashboardwishlists">
                        @forelse($wishlists as $wishlist)
                            <div class="listar-themepost listar-placespost" id="{{'row_'.$wishlist->hashed_id}}">
                                <a class="del-btn listar-btndelpost" style="position: absolute"
                                   href="{{url('utilities/wishlist/'.$wishlist->hashed_id)}}"
                                   data-action="delete"
                                   data-page_action="removeRow"
                                   data-action_data="{{ $wishlist->hashed_id }}">x</a>
                                <figure class="bg listar-featuredimg custom-background-my-listing"
                                        data-bg="{{$wishlist->wishlistable->image}}"
                                        data-scrollax="properties: { translateY: '30%' }"><a
                                            href="{{$wishlist->getShowURL()}}"></a></figure>
                                <div class="listar-postcontent">
                                    <h3>
                                        <a href="{{url('listings/'.$wishlist->wishlistable->slug)}}">{{$wishlist->wishlistable->name}}</a>
                                    </h3>
                                    <span class="listar-catagory">{{$wishlist->wishlistable->address}}</span>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info alert-dismissible text-center margin-bottom-1x"><span
                                        class="alert-close"
                                        data-dismiss="alert"></span><i class="fa fa-exclamation-circle"
                                                                       aria-hidden="true"></i>
                                @lang('corals-directory-listing-star::labels.dashboard.sry_nothing_to_show')
                            </div>
                        @endforelse
                    </div>
                    {!! $wishlists->links('partials.paginator') !!}
                </fieldset>
            </form>
        </div>
        <!--************************************
                    Dashboard Content End
        *************************************-->
    </main>
@endsection

@section('js')
    <script type="text/javascript">
        function removeRow(response, $form, hashedId) {
            $("#row_" + hashedId).fadeOut();
        }
    </script>
@endsection