@extends('layouts.public')

@section('content')
    <div class="listar-innerbanner">
        <div class="listar-parallaxcolor listar-innerbannerparallaxcolor">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="listar-innerbannercontent">
                            <div class="listar-pagetitle">
                                <h1>@lang('corals-directory-listing-star::labels.template.home.how_it_works')</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <main id="listar-main" class="listar-main listar-innerspeace listar-haslayout">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    {!! $item->content !!}
                </div>
            </div>
        </div>
    </main>
@endsection