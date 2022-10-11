@extends('layouts.public')

@section('page_header')
    @include('partials.page_header')
@endsection

@section('content')
    {!! $item->rendered !!}
@endsection