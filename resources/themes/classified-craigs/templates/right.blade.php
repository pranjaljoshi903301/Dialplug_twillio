@extends('layouts.public')

@section('hero_area')
    @include('partials.page_header')
@endsection

@section('editable_content')
    <section class="content">
        <section class="block">
            <div class="row">
                <div class="col-md-8">
                    {!! $item->rendered !!}
                </div>
                <div class="col-md-4">
                    @include('partials.page_sidebar')
                </div>
            </div>
        </section>
    </section>
@endsection