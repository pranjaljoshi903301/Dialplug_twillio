@extends('layouts.theme')

@section('content')

    <div class="container padding-bottom-3x mb-1">
        <div class="row">
            <div class="col-md-8">
                {!! $item->rendered !!}
            </div>
            <div class="col-md-4">
                @include('partials.sidebar')
            </div>
        </div>
    </div>
@stop