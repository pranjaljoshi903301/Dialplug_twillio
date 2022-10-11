@extends('layouts.public')

@section('page_header')
    @include('partials.page_header')
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                {!! $item->rendered !!}
            </div>
        </div>
    </div>
@endsection