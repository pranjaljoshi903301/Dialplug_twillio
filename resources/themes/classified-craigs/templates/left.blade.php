@extends('layouts.public')

@section('page_header')
    @include('partials.page_header')
@endsection

@section('content')
    <section class="content">
        <section class="block">
            <div class="row">
                <div class="col-md-4">
                    @include('partials.page_sidebar')
                </div>
                <div class="col-md-8">
                    {!! $item->rendered !!}
                </div>
            </div>
        </section>
    </section>
@endsection