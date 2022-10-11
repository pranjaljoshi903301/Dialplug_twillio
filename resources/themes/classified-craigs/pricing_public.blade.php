@extends('layouts.public')


@section('page_header')
    @include('partials.page_header',['item'=>$pricing])
@endsection
@section('content')

    {!! $pricing->rendered !!}

    <section class="content">
        <section class="block">
            <div class="container">

                <section>
                    <div class="row">
                        @foreach($products as $product)
                            <div class="col-md-6 col-sm-6 col-lg-6">
                                <div class="pricing box">
                                    <h2>{{ $product->name }}</h2>
                                    <ul>
                                        <li class="available">
                                        <p>{{$product->description}}</p>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                        @endforeach
                    </div>
                    <div class="row">
                        {!!   \Shortcode::compile( 'pricing',$product->id ) !!}
                    </div>
                </section>
            </div>
        </section>
    </section>

@endsection