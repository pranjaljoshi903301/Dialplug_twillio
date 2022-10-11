@extends('layouts.theme')

@section('hero_area')
    @include('partials.page_header')
@endsection

@section('editable_content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                {!! $item->rendered !!}
            </div>
        </div>
    </div>
@endsection