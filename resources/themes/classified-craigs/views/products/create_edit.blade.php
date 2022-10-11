@extends('layouts.master')

@section('title', $title_singular)

@section('css')
    {!! Theme::css('css/color-switcher.css') !!}
    {!! Theme::css('css/summernote.css') !!}
@stop

@section('page_header')
    @include('partials.page_header', ['content'=> '<h2 class="product-title">'. $title_singular .'</h2>'])
@endsection

@section('content')
    @parent
    <div class="inner-box">
        <div class="dashboard-box">
            <h2 class="dashbord-title">{{ $title_singular }}</h2>
        </div>
        <div class="dashboard-wrapper">
            @if($product->exists)
                <div class="row">
                    <div class="col-md-12 pb-4">
                        @include('Utility::gallery.gallery',['galleryModel'=> $product, 'editable'=>true])
                    </div>
                </div>
            @endif

            {!! Form::model($product, ['url' => url($resource_url.'/'.$product->hashed_id),'method'=>$product->exists?'PUT':'POST','files'=>true,'class'=>'ajax-form']) !!}
            <div class="row">
                <div class="col-md-6">
                    {!! CoralsForm::text('name','Classified::attributes.product.name',true,$product->name,[]) !!}
                </div>
                <div class="col-md-6">
                    {!! CoralsForm::text('caption','Classified::attributes.product.caption',true,$product->caption) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    {!! CoralsForm::select('location_id','Classified::attributes.product.location', \Address::getLocationsList('Classified'),true,null ,[]) !!}
                </div>
                <div class="col-md-3">
                    {!! CoralsForm::select('categories[]','Classified::attributes.product.categories', \Category::getCategoriesList('Classified', false, false, 'active'),true,null,['id'=>'categories', 'multiple'=>true]) !!}
                    <div id="attributes">
                    </div>
                </div>
                <div class="col-md-6">
                    {!! CoralsForm::text('slug','Classified::attributes.product.slug',false, $product->slug, ['help_text'=>'Classified::attributes.product.slug_help']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label class="custom-control-label" for="status">&nbsp;</label>
                    {!! CoralsForm::radio('status','Corals::attributes.status',true, $statusOptions ) !!}

                    @if(\Classified::subscriptionActive() &&
                    ($product->is_featured || (!empty($subscriptionStatus) && !$subscriptionStatus['feature_products_count']['limit_reached'])))
                        {!! CoralsForm::checkbox('is_featured', 'Classified::attributes.product.is_featured', $product->is_featured) !!}
                    @endif
                </div>
                @if(\Settings::get('classified_year_model_visible'))
                    <div class="col-md-2">
                        {!! CoralsForm::number('year_model', 'Classified::attributes.product.year_model', false, $product->year_model,
                                                ['step'=>0.01, 'min'=>0, 'max'=>999999]) !!}
                    </div>
                @endif
                <div class="col-md-6">
                    {!! CoralsForm::select('tags[]','Classified::attributes.product.tags', \Tag::getTagsList('Classified'), false,null,[ 'multiple'=>true]) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    {!! CoralsForm::select('condition','Classified::attributes.product.condition', \Settings::get('classified_product_condition_options',[])) !!}
                </div>
                <div class="col-md-3">
                    {!! CoralsForm::text('brand','Classified::attributes.product.brand') !!}
                </div>
                <div class="col-md-2">
                    {!! CoralsForm::number('price', 'Classified::attributes.product.price', true, $product->price,
                    ['step'=>0.01, 'min'=>0, 'max'=>999999, 'left_addon'=>'<span class="'.$product->currency_icon.' input-group-text"></span>']) !!}

                </div>
                <div class="col-md-4">
                    <label class="custom-control-label" for="price_on_call">&nbsp;</label>
                    {!! CoralsForm::checkbox('price_on_call', 'Classified::attributes.product.price_on_call', $product->price_on_call,1,['help_text'=>'Classified::attributes.product.price_on_call_help']) !!}

                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    {!! CoralsForm::textarea('description','Classified::attributes.product.description',false, $product->description, ['class'=>'ckeditor','rows'=>5]) !!}
                </div>
            </div>

            {!! \Actions::do_action('classified_product_form_post_fields', $product) !!}

            {!! CoralsForm::customFields($product) !!}
            <div class="row">
                <div class="col-md-12">
                    {!! CoralsForm::formButtons() !!}
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('js')
    @include('Utility::category.category_scripts', ['category_field_id'=>'#categories','attributes_div'=>'#attributes'])
@endsection