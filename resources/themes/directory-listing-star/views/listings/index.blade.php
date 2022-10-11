@extends('layouts.master')

@section('title', $title)

@section('content')
    <main id="listar-main" class="listar-main listar-haslayout">
        <div class="listar-dashboardbanner">
            <h1>@lang('corals-directory-listing-star::labels.dashboard.my_listings')</h1>
        </div>
        <div id="listar-content" class="listar-content">
            <form class="listar-formtheme listar-formaddlisting listar-formwishlist">
                <fieldset>
                    <div class="listar-boxtitle">
                        <h3>@lang('corals-directory-listing-star::labels.dashboard.my_listings')</h3>
                    </div>
                    <div class="listar-dashboardwishlists tab-content">
                        <div role="tabpanel" class="tab-pane active" id="home">
                            @include('partials.my_listings')
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </main>
@endsection