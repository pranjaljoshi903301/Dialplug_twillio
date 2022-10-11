@extends('layouts.master')



@section('title', $title_singular)

@section('hero_area')
    @include('partials.page_header', ['content'=> '<h2 class="product-title">'. $title .'</h2>'])
@endsection

@section('js')
@endsection
