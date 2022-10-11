@extends('layouts.master')

@section('css')
    <style type="text/css">
        .m-t-30 {
            margin-top: 30px;
        }

        .p-t-30 {
            padding-top: 30px;
        }

    </style>
@endsection
@section('editable_content')
    @include('partials.page_header')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="m-t-30 p-t-30 ">
                    <h2>{!! $item->title !!}</h2>
                    <br/>
                    {!! $item->rendered !!}
                </div>
            </div>
        </div>
    </div>
@stop