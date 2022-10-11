@extends('layouts.public')

@section('class_header','listar-header listar-fixedheader listar-haslayout')
@section('content')
    <main id="listar-main" class="listar-main listar-haslayout">
        <div id="listar-content" class="listar-content">
            <div class="listar-listing {{ $layout == 'grid'?'listar-listing listar-listingvtwo':'' }}">
                <div id="listar-mapclustring" class="listar-mapclustring">
                    <div class="listar-maparea">
                        <div id="listar-listingmap" class="listar-listingmap"></div>
                        <div class="listar-mapcontrols">
                            <span id="doc-mapplus"><i class="fa fa-plus"></i></span>
                            <span id="doc-mapminus"><i class="fa fa-minus"></i></span>
                            <span id="doc-lock"><i class="fa fa-lock"></i></span>
                            <span id="listar-geolocation"><i class="fa fa-crosshairs"></i></span>
                        </div>
                    </div>
                </div>
                <div class="listar-listingbox">
                    <div class="row">
                        <div class="listar-listingarea">
                            <div class="listar-innerpagesearch">
                                <div class="listar-innersearch">
                                    @if($userListing = getUserByHash(\Request::get('user')))
                                        @include('partials.user')
                                    @endif
                                    <div class="listar-searchstatus">
                                        <h1>@lang('corals-directory-listing-star::labels.template.listing.search_listing')</h1>
                                    </div>
                                    @include('partials.listing_filter')
                                </div>
                            </div>
                            <div class="listar-themeposts listar-placesposts {{ $layout == 'grid'?'listar-gridview':'listar-listview' }}">
                                @forelse($listings as $listing)
                                    @include('partials.listing_'.$layout.'_item',compact('listing'))
                                    @include('partials.modal_listing',compact('listing'))
                                @empty
                                    <h4 style="padding-left: 20px">@lang('corals-directory-listing-star::labels.template.listing.sorry_no_result')</h4>
                                @endforelse
                                {{$listings->links('partials.paginator')}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
@section('js')
    @parent
    <script type="text/javascript">

        $(document).ready(function () {
            $("#shop_sort").change(function () {
                $("#filterSort").val($(this).val());

                $("#filterForm").submit();
            })

            $(".listar-inputwithicon input[name='location']").change(function () {
                $(".filter-tags-wrap").removeClass("display-none");
            });

            $("input[name='location_coordinates']").on("click", function() {
                $('#lat').val(null);
                $('#long').val(null);
            });

        });


        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition);
            } else {
                window.alert('error');
            }
        }

        function showPosition(position) {
            $('#lat').val(position.coords.latitude);
            $('#long').val(position.coords.longitude);
        }


    </script>
@endsection