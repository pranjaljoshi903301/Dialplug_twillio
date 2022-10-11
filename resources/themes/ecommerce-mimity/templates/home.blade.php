@extends('layouts.master')

@section('editable_content')
    @php \Actions::do_action('pre_content',$item, $home??null) @endphp
    {!! $item->rendered !!}
    <div class="services-box">
        {!!   \Shortcode::compile( 'block','e-commerce-features' ) ; !!}
    </div>
    <!-- /Services -->
    @include('partials.featured_categories')
    @include('partials.new_products')
    @include('partials.featured_brands')
    @include('partials.featured_products')

    @include('partials.news')


@stop


